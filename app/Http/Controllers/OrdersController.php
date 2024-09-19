<?php

namespace App\Http\Controllers;

use App\Events\NewMessage;
use App\Mail\TemplateEmail;
use App\Models\CostumerAddress;
use App\Models\EmailTemplates;
use App\Models\ItemAttributes;
use App\Models\Notifications;
use App\Models\OrderComments;
use App\Models\OrderItems;
use App\Models\OrderLogs;
use App\Models\Orders;
use App\Http\Requests\StoreOrdersRequest;
use App\Http\Requests\UpdateOrdersRequest;
use App\Models\OrderStatus;
use App\Models\SubStatus;
use App\Models\TimelinePool;
use App\Models\User;
use App\Models\Workstations;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter_product = $request->input('filter_product');
        $filter_date = $request->input('filter_date');
        $filter_status = $request->input('filter_status');
        $filter_priority = $request->input('filter_priority');



        $query = Orders::with(['children','items','status','last_log','last_log.status','last_log.sub_status','addresses','station','station.worker','items.attributes'])
            ->when($filter_date, function ($q) use ($filter_date) {
                if ($filter_date == 'oldest') {
                    $q->orderBy(\Illuminate\Support\Facades\DB::raw('DATE(date_started)'), 'ASC');
                } elseif ($filter_date == 'newest') {
                    $q->orderBy(\Illuminate\Support\Facades\DB::raw('DATE(date_started)'), 'DESC');
                }
            }, function ($q) {
                $q->orderBy('is_rush','DESC')
                  ->orderBy(\Illuminate\Support\Facades\DB::raw('DATE(deadline)'),'DESC')
                ->orderBy(\Illuminate\Support\Facades\DB::raw('DATE(date_started)'), 'DESC');
            })
            ->when($filter_product,function ($q) use ($filter_product){
                $q->whereHas('items', function ($query) use ($filter_product) {
                    $query->where('product_name', $filter_product);
                });
            })
            ->when($filter_priority,function ($q) use ($filter_priority){
                    $q->where('is_rush', $filter_priority);
            })
            ->when($filter_status,function ($q) use ($filter_status){
                    $q->where('status_id', $filter_status);
            })
            ->where('orderType','=',Orders::parentType);
        $orders = $query->paginate(10);
//        dd($orders);
        $workstations = Workstations::all();
        $statuses = OrderStatus::all();
        $edit_statuses = OrderStatus::whereIn('status_name',OrderStatus::adminStatuses)->get();
        $sub_statuses = SubStatus::with('status')->get();
        $users = User::all();
        $order_id = null;
        $products = \DB::table('order_items')->select('product_name')->distinct()->get();
        if($request->order_id){
            $order_id = Orders::where('order_id',$request->order_id)->pluck('id')->first();
        }
//        dd($data);
        return view('admin.orders',compact('orders', 'workstations', 'statuses', 'edit_statuses','users','order_id',
            'sub_statuses','products','filter_product','filter_date','filter_status','filter_priority'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $production_days = 0;
            if($request->has('order_id')){
                $order_data = $request->all();
//                Log::info($request->all());
                $existingOrder = Orders::where('order_id', $order_data['order_number'])->first();
                if ($existingOrder) {
                    // Order already exists, handle accordingly (e.g., return a response or log a message)
                    return response()->json(['message' => 'Order already exists'], 409);
                }

                $order_number = $order_data['order_number'] ?? null;
                $order = new Orders();
                if ($order_number) {
                    if (strpos($order_number, '-') !== false) {
                        $parts = explode('-', $order_number);
                        $parent_order_id = $parts[0];

                        $parent_order = Orders::where('order_id',$parent_order_id)->pluck('id')->first();
                        if (!$parent_order) {
                            return response()->json(['message' => 'Parent order with '.$parent_order.' does not exists.'], 409);
                        }
                        $order->parentOrder = $parent_order;
                        $order->orderType = \App\Models\Orders::childType;
                    } else {
                        $order->order_id = $order_number;
                        $order->orderType = \App\Models\Orders::parentType;
                    }
                }

                $order->order_id = $order_number;
                $order->order_unique_id = $order_data['order_unique_id']??null;
                $order->customer_name = $order_data['customer_name']??null;
                $order->customer_email = $order_data['customer_email']??null;
                $order->date_started = Carbon::now()->format('Y-m-d h:i:s')??null;
                $order->status_id = 1;
                $order->workstation_id = 1;
                $order->payment_method = $order_data['payment_method']??null;
                $order->payment_method_title = $order_data['payment_method_title']??null;
                $order->transaction_id = $order_data['transaction_id']??null;
                $order->currency = $order_data['currency']??null;
                $order->total_amount = $order_data['total_amount']??null;
                $order->subtotal = $order_data['order_id']??null;
                $order->total_discount = $order_data['total_discount']??null;
                $order->total_tax = $order_data['total_tax']??null;
                $order->shipping_total = $order_data['shipping_total']??null;
                $order->shipping_tax = $order_data['shipping_tax']??null;
                $order->customer_note = $order_data['customer_note']??null;
                $order->is_rush = intval($order_data['rush']) ??0;
                $order->save();

                $items = $order_data['items'];
                foreach ($items as $item){
                    $order_item = new OrderItems();
                    $order_item->order_id = $order->id;
                    $order_item->item_id = $item['item_id'];
                    $order_item->product_id = $item['product_id'];
                    $order_item->product_name = $item['product_name'];
                    $order_item->quantity = $item['quantity'];
                    $order_item->subtotal = $item['subtotal'];
                    $order_item->total = $item['total'];
                    $order_item->tax = $item['tax'];
                    $order_item->tax_class = $item['tax_class'];
                    $order_item->sku = $item['sku'];
                    $order_item->save();

                    $production_days += TimelinePool::where('item',$item['product_name'])->whereNull('attribute')
                        ->whereNull('attribute_value')->pluck('days')->first();
                    $attributes = $item['attributes']??[];
                    foreach ($attributes as $attribute){
                        $arr = explode('_',$attribute);
                        $order_attribute = new ItemAttributes();
                        $order_attribute->type= $arr[0]??null;
                        $order_attribute->title=$arr[1]??null;
                        $order_attribute->item_id=$order_item->id;
                        $order_attribute->save();
                        $production_days += TimelinePool::where('item',$item['product_name'])->where('attribute',$arr[0])
                            ->where('attribute_value',$arr[1])->pluck('days')->first();
                    }
                }

                $address = new CostumerAddress();
                $address->first_name = $order_data['billing_address']['first_name'] ?? null;
                $address->last_name = $order_data['billing_address']['last_name'] ?? null;
                $address->company = $order_data['billing_address']['company'] ?? null;
                $address->address_1 = $order_data['billing_address']['address_1'] ?? null;
                $address->address_2 = $order_data['billing_address']['address_2'] ?? null;
                $address->city = $order_data['billing_address']['city'] ?? null;
                $address->state = $order_data['billing_address']['state'] ?? null;
                $address->postcode = $order_data['billing_address']['postcode'] ?? null;
                $address->country = $order_data['billing_address']['country'] ?? null;
                $address->email = $order_data['billing_address']['email'] ?? null;
                $address->phone = $order_data['billing_address']['phone'] ?? null;
                $address->type = 'billing_address';
                $address->order_id = $order->id;
                $address->save();
                $address2 = new CostumerAddress();
                $address2->first_name = $order_data['shipping_address']['first_name'] ?? null;
                $address2->last_name = $order_data['shipping_address']['last_name'] ?? null;
                $address2->company = $order_data['shipping_address']['company'] ?? null;
                $address2->address_1 = $order_data['shipping_address']['address_1'] ?? null;
                $address2->address_2 = $order_data['shipping_address']['address_2'] ?? null;
                $address2->city = $order_data['shipping_address']['city'] ?? null;
                $address2->state = $order_data['shipping_address']['state'] ?? null;
                $address2->postcode = $order_data['shipping_address']['postcode'] ?? null;
                $address2->country = $order_data['shipping_address']['country'] ?? null;
                $address2->email = $order_data['shipping_address']['email'] ?? null;
                $address2->phone = $order_data['shipping_address']['phone'] ?? null;
                $address2->type = 'shipping_address';
                $address2->order_id = $order->id;
                $address2->save();

                if($order->is_rush){
                    $order->deadline = $order_data['rush_delivery_date'];
                }else{
                    $startDate = Carbon::now();
                    $deadline = $this->addBusinessDays($startDate, $production_days);
                    $order->deadline = $deadline->format('Y-m-d');
                }

                $order->save();

                DB::commit();
                Log::info('Successfully saved ORDER -------'.$order->id);
            }else{
                Log::info('Failed to save ORDER -------'.$request);
            }
        }catch (\Exception $e){
            DB::rollBack();
            Log::info('Failed to save ORDER -------'.$request);
            Log::info('Error -------'.$e);
        }

    }

    function addBusinessDays(Carbon $startDate, int $daysToAdd) {
        $currentDate = $startDate->copy();

        while ($daysToAdd > 0) {
            $currentDate->addDay();
            if ($currentDate->isWeekday()) {
                $daysToAdd--;
            }
        }

        return $currentDate;
    }

    /**
     * Display the specified resource.
     */
    public function show(Orders $orders)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Orders $orders)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrdersRequest $request, Orders $orders)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Orders $orders)
    {
        //
    }


    public function addComment(Request $request)
    {
        try {

            if(isset($request->reply_to)){
                DB::beginTransaction();
                $orderComment = new OrderComments();
                $orderComment->order_id = $request->order_id;
                $orderComment->user_id = Auth::user()->id;
                $orderComment->comment = $request->comment;
                $orderComment->parent_id = $request->reply_to;
                $orderComment->save();

                $comment = OrderComments::whereId($orderComment->parent_id)->with(['user','replies'])->get();
                $order_id = Orders::find($request->order_id)->pluck('order_id')->first();

                $notification = new Notifications();
                $notification->type = Notifications::typeComment;
                $notification->comment_id = $comment[0]->id;
                $notification->save();

                $message = ['message'=>'A reply to a comment was added on order id '.$order_id,'comment'=>$comment[0],'order_id'=>$order_id];
                event(new NewMessage($message));

                $comments_vew = view('admin.partials.comments',['comments'=>$comment])->render();
                DB::commit();
                $response = [
                    'status' => 200,
                    'message' => 'Comment added successfully.',
                    'comment_view' => $comments_vew,
                ];
            }else{
                DB::beginTransaction();
                $orderComment = new OrderComments();
                $orderComment->order_id = $request->order_id;
                $orderComment->user_id = Auth::user()->id;
                $orderComment->comment = $request->comment;
                $orderComment->save();

                $comment = OrderComments::whereId($orderComment->id)->with('user')->get();
                $order_id = Orders::find($request->order_id)->pluck('order_id')->first();

                $notification = new Notifications();
                $notification->type = Notifications::typeComment;
                $notification->comment_id = $comment[0]->id;
                $notification->save();

                $message = ['message'=>'A comment was added on order id '.$order_id,'comment'=>$comment[0],'order_id'=>$order_id];
                event(new NewMessage($message));

                $comments_vew = view('admin.partials.comments',['comments'=>$comment])->render();

                DB::commit();
                $response = [
                    'status' => 200,
                    'message' => 'Comment added successfully.',
                    'comment_view' => $comments_vew,
                ];
            }

        }catch (\Exception $e){
            DB::rollBack();
            return response()->json([
                'status' => 400,
                'message' => 'Something went wrong'.$e->getMessage()
            ]);
        }
        return response()->json($response);

    }

    public function getOrderDetails(Request $request)
    {
        $id = $request->id;

        $order = Orders::with(['children','children.status','children.station',
            'children.station.worker','status','logs','logs.user','logs.status',
            'logs.sub_status','comments','comments.replies','comments.replies.user','comments.user'])
            ->whereId($id)->first();

        $status_log = OrderLogs::with(['user','status','sub_status'])
            ->where('order_id',$order->order_id)
            ->where('status_id','!=',null)
            ->latest('time_started')
            ->first();


        $comments_vew = view('admin.partials.comments',['comments'=>$order->comments])->render();



        return response()->json(['status' => 200,'order' => $order,'status_log' => $status_log,'comments_vew' => $comments_vew]);

    }

    public function search(Request $request)
    {
        $term = $request->input('query');

        // Perform search based on your logic
        $term = '%'. $term .'%'; // assuming $term is already sanitized and defined
        $query = Orders::with([
            'status',
            'station',
            'station.worker'
        ])
            ->where('order_id', 'like', $term)
            ->orWhereHas('status', function ($query) use ($term) {
                $query->where('status_name', 'like', $term);
            })
            ->orWhereHas('station.worker', function ($query) use ($term) {
                $query->where('name', 'like', $term);
            })
            ->orderBy('is_rush', 'DESC')
            ->orderBy('deadline', 'DESC')
            ->orderBy('date_started', 'DESC')
            ->where('orderType', '=', Orders::parentType);
        $orders = $query->paginate(10);


        $order_vew = view('admin.partials.order_table',['orders'=>$orders])->render();
        // Return JSON response
        return response()->json(['status'=>200,'orders_view' => $order_vew]);
    }

    public function addOrUpdateOrderStatusRow(Request $request)
    {

        $inputs = explode(',', $request->input);
        if (count($inputs) > 0) {
            try {
                for ($i = 0; $i < count($inputs) - 1; $i++) {
                    $input = $inputs[$i];
                    $text = explode('-', $input);
                    if (count($text) > 0) {
                        DB::beginTransaction();

                        $userId = User::where('name', $text[0])->pluck('id')->first();
                        $content = $text[1];

                        if (is_numeric($text[1])) {
                            $content = $text[1];
                            $existingOrderStatus = OrderLogs::where('status_id', null)
                                ->where('order_id', $content)
                                ->first();

                            if ($existingOrderStatus) {
                                $response = [
                                    'status' => 200,
                                    'message' => 'Order log row already exists.',
                                ];
                            } else {
                                $orderStatus = new OrderLogs();
                                $orderStatus->order_id = $content;
                                $orderStatus->user_id = $userId;
                                $orderStatus->save();

                                $response = [
                                    'status' => 200,
                                    'message' => 'Status log row created.',
                                ];
                            }

                        } else {
                            $status_id = OrderStatus::where('status_name', $content)->pluck('id')->first();
                            $orderStatus = OrderLogs::where('user_id', $userId)
                                ->where('order_id', '!=', null)
                                ->where('status_id', null)
                                ->first();

                            if ($orderStatus) {
                                $orderStatus->status_id = $status_id;
                                $orderStatus->time_started = \Illuminate\Support\Carbon::now()->format('Y-m-d H:i:s');
                                $orderStatus->save();

                                $order = Orders::where('order_id', $orderStatus->order_id)->first();
                                if ($order) {
                                    $order->status_id = $status_id;
                                    $order->save();
                                }
                                $log = OrderLogs::with(['user','status'])
                                    ->where('id', $orderStatus->id)
                                    ->first();

                                $notification = new Notifications();
                                $notification->type = Notifications::typestatus;
                                $notification->log_id = $log->id;
                                $notification->save();

                                $message = ['message'=>'A status was updated for order id '.$orderStatus->order_id,'log'=>$log];
                                event(new NewMessage($message));
                                $this->sendIssueWithPrintEmail($order,$log->status->status_name);
                                $response = [
                                    'status' => 200,
                                    'message' => 'Status log updated successfully.',
                                ];
                            } else {
                                $response = [
                                    'status' => 404,
                                    'message' => 'No matching order status found to update.',
                                ];
                            }
                        }

                        DB::commit();
                    } else {
                        Log::info('--------------ERROR WHILE UPDATING STATUS-----------------------');
                        Log::info($input);
                        Log::info('-------------------------------------');
                    }
                }

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => 400,
                    'message' => 'Something went wrong: ' . $e->getMessage(),
                ]);
            }
        }
        else{
            return response()->json([
                'status' => 400,
                'message' => 'No inputs provided.',
            ]);
        }

        return response()->json($response);
    }


    public function sendIssueWithPrintEmail($order,$status)
    {
        \Log::info('Sending email for order status '.$status, ['order_id' => $order->id]);
        $template = EmailTemplates::where('status_id', $order->status_id)->where('status', 1)->first();
        if ($template) {
            $content = str_replace(
                ['{{ customer_name }}', '{{ order_number }}', '{{ support_email }}'],
                [$order->customer_name, $order->order_id, 'support@boudoir.com'],
                $template->content
            );
            $address = DB::table('costumer_address')->where('order_id',$order->id)->where('type','=','billing_address')->first();

            Mail::to($address->email)->send(new TemplateEmail($template->subject, $content));

            \Log::info('Email sent successfully', ['order_id' => $order->id]);
        } else {
            \Log::error('No email template found for status '.$status.'.');
        }
    }
}
