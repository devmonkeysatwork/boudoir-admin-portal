<?php

namespace App\Http\Controllers;

use App\Events\NewMessage;
use App\Models\CostumerAddress;
use App\Models\ItemAttributes;
use App\Models\OrderComments;
use App\Models\OrderItems;
use App\Models\OrderLogs;
use App\Models\Orders;
use App\Http\Requests\StoreOrdersRequest;
use App\Http\Requests\UpdateOrdersRequest;
use App\Models\OrderStatus;
use App\Models\User;
use App\Models\Workstations;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Orders::with(['items','status','addresses','station','station.worker','items.attributes']);
        $orders = $query->paginate(10);
        $workstations = Workstations::all();
        $statuses = OrderStatus::all();
        $users = User::all();
//        dd($data);
        return view('admin.orders',compact('orders', 'workstations', 'statuses','users'));
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

            if($request->has('order_id')){
                $order_data = $request->all();
                Log::info($request->all());
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

                    $attributes = $item['attributes']??[];
                    foreach ($attributes as $attribute){
                        $arr = explode('_',$attribute);
                        $order_attribute = new ItemAttributes();
                        $order_attribute->type= $arr[0]??null;
                        $order_attribute->title=$arr[1]??null;
                        $order_attribute->item_id=$order_item->id;
                        $order_attribute->save();
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
            DB::beginTransaction();
            $order = new OrderComments();
            $order->order_id = $request->order_id;
            $order->user_id = Auth::user()->id;
            $order->comment = $request->comment;
            $order->save();
            $comment = OrderComments::whereId($order->id)->with('user')->first();

            $message = ['message'=>'A comment was added on order id '.$request->order_id,'comment'=>$comment];
            event(new NewMessage($message));


            DB::commit();
            $response = [
                'status' => 200,
                'message' => 'Comment added successfully.',
                'comment' => $comment,
            ];
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

        $order = Orders::with(['logs','logs.user','logs.status','comments','comments.replies','comments.user'])
            ->whereId($id)->first();

        return response()->json(['status' => 200,'order' => $order]);

    }

    public function search(Request $request)
    {
        $term = $request->input('query');

        // Perform search based on your logic
        $orders = Orders::where('order_id', 'like', '%' . $term . '%')
            ->with(['status','station','station.worker'])
            ->orWhereHas('status', function ($query) use ($term) {
                $query->where('status_name', 'like', '%' . $term . '%');
            })
            ->orWhereHas('station.worker', function ($query) use ($term) {
                $query->where('name', 'like', '%' . $term . '%');
            })
            ->get();
        // Return JSON response
        return response()->json(['orders' => $orders]);
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



}
