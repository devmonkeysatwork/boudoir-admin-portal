<?php

namespace App\Http\Controllers;

use App\Events\NewMessage;
use App\Mail\TemplateEmail;
use App\Models\EmailTemplates;
use App\Models\Notifications;
use App\Models\OrderLogs;
use App\Models\Orders;
use App\Models\OrderStatus;
use App\Models\Roles;
use App\Models\SubStatus;
use App\Models\User;
use App\Models\Workstations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Picqer\Barcode\BarcodeGeneratorJPG;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        // Status IDs based on your categorization
        $readyForPrintStatusId = OrderStatus::where('status_name','Sent To Print')->pluck('id')->toArray();
        $onHoldStatusIds = OrderStatus::where('status_name','On hold')->pluck('id')->toArray();
        $readyToShipStatusId = OrderStatus::where('status_name','Ready to Ship')->pluck('id')->toArray();
        $qualityControlStatusId = OrderStatus::where('status_name','Quality Control')->pluck('id')->toArray();
        $excludedStatusIds = array_merge(
            $readyForPrintStatusId,
            $onHoldStatusIds,
            $readyToShipStatusId,
            $qualityControlStatusId
        );
        $inProductionStatusIds = OrderStatus::whereNotIn('status_name',$excludedStatusIds)->pluck('id');

        // Dynamic counts for each category
        $readyForPrintOrdersCount = Orders::whereIn('status_id', $readyForPrintStatusId)->count();
        $inProductionOrdersCount = Orders::whereIn('status_id', $inProductionStatusIds)->count();
        $onHoldOrdersCount = Orders::whereIn('status_id', $onHoldStatusIds)->count();
        $readyToShipOrdersCount = Orders::whereIn('status_id', $readyToShipStatusId)->count();
        $qualityControlOrdersCount = Orders::whereIn('status_id', $qualityControlStatusId)->count();

        // Fetch orders with pagination
        $filter_date = $request->input('filter_date');
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
            ->where('orderType','=',Orders::parentType);
        $orders = $query->paginate(10);

        $teamMembers = User::with(['workstations'])->get();

        foreach ($teamMembers as $teamMember) {
            $totalOrders = 0;

            foreach ($teamMember->workstations as $workstation) {
                $totalOrders += Orders::where('workstation_id', $workstation->id)->count();
            }

            $teamMember->total_orders = $totalOrders;
            $teamMember->time_spent = $this->calculateTimeSpent($teamMember->workstations->pluck('id'));
        }

        $workstations = OrderStatus::whereNotIn('id',$excludedStatusIds)->with(['first_log', 'last_log','orders'])->get();
        $edit_statuses = OrderStatus::whereIn('status_name',OrderStatus::adminStatuses)->get();
        $sub_statuses = SubStatus::with('status')->get();

        return view('admin.dashboard', compact(
            'readyForPrintOrdersCount',
            'inProductionOrdersCount',
            'onHoldOrdersCount',
            'readyToShipOrdersCount',
            'qualityControlOrdersCount',
            'orders',
            'teamMembers',
            'workstations',
            'edit_statuses',
            'sub_statuses'

        ));
    }

    public function orders()
    {
        $query = Orders::with(['items','status','addresses','station','station.worker','items.attributes']);
        $orders = $query->paginate(10);
        $workstations = Workstations::all();
        $statuses = OrderStatus::all();

        return view('admin.orders',compact('orders', 'workstations', 'statuses'));
    }

    public function updateOrderStatus(Request $request)
    {
        try {
            DB::beginTransaction();
            $order = Orders::find($request->id);
            $order->status_id = $request->edit_status;
            $order->save();

            $orderStatus = new OrderLogs();
            $orderStatus->order_id = $order->order_id;
            $orderStatus->status_id = $request->edit_status;
            $orderStatus->sub_status_id = $request->edit_sub_status??null;
            $orderStatus->user_id = Auth::user()->id;
            $orderStatus->notes = $request->notes??null;
            $orderStatus->time_started = \Illuminate\Support\Carbon::now()->format('Y-m-d H:i:s');
            $orderStatus->save();

            $log = OrderLogs::whereId($orderStatus->id)->with(['user','status','updated_by'])->first();

            $notification = new Notifications();
            $notification->type = Notifications::typestatus;
            $notification->log_id = $log->id;
            $notification->save();

            $message = ['message'=>'A status was updated for order id '.$request->order_id,'log'=>$log];
            event(new NewMessage($message));


            DB::commit();

            $this->sendIssueWithPrintEmail($order,$log->status->status_name);

            $response = [
                'status' => 200,
                'message' => 'Status updated successfully.',
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



    public function areas()
    {
        $workstations = Workstations::with(['orders'])->paginate(10);

        foreach ($workstations as $workstation) {
            $totalTimeInProduction = 0;

            foreach ($workstation->orders as $order) {
                if ($order->date_started) {
                    $dateStarted = \Carbon\Carbon::parse($order->date_started);
                    $now = \Carbon\Carbon::now();
                    $totalTimeInProduction += $dateStarted->diffInSeconds($now);
                }
            }

            $workstation->time_in_production = gmdate('H:i:s', $totalTimeInProduction);
        }

        return view('admin.areas', compact('workstations'));
    }

    public function getWorkstationDetails($id)
    {
        $workstation = Workstations::with('orders')->find($id);

        if ($workstation) {
            $ordersHtml = '';
            $counter = 1;
            foreach ($workstation->orders as $order) {
                $ordersHtml .= '
                <tr>
                    <td>' . $counter++ . '</td>
                    <td>Order #' . $order->order_id . '</td>
                    <td>' . gmdate('H:i', strtotime($order->time_in_production)) . '</td>
                </tr>';
            }

            return response()->json([
                'ordersHtml' => $ordersHtml,
                'orderCount' => count($workstation->orders)
            ]);
        } else {
            return response()->json(['message' => 'Workstation not found'], 404);
        }
    }

    public function team()
    {
        $teamMembers = User::with(['workstations'])->get();
        $roles = Roles::all();

        foreach ($teamMembers as $teamMember) {
            $totalOrders = 0;

            foreach ($teamMember->workstations as $workstation) {
                $totalOrders += Orders::where('workstation_id', $workstation->id)->count();
            }

            $teamMember->total_orders = $totalOrders;
            $teamMember->time_spent = $this->calculateTimeSpent($teamMember->workstations->pluck('id'));
        }

        return view('admin.team', compact(['teamMembers','roles']));
    }

    public function getTeamDetails($id)
    {
        $teamMember = User::with('workstations.orders')->find($id);

        if (!$teamMember) {
            return response()->json(['message' => 'Team member not found'], 404);
        }

        $ordersHtml = '';
        $counter = 1;
        foreach ($teamMember->workstations as $workstation) {
            foreach ($workstation->orders as $order) {
                $ordersHtml .= '
                <tr>
                    <td>' . $counter++ . '</td>
                    <td>Order #' . $order->order_id . '</td>
                    <td>' . gmdate('H:i', strtotime($order->time_in_production)) . '</td>
                </tr>';
            }
        }

        return response()->json([
            'ordersHtml' => $ordersHtml,
            'orderCount' => count($teamMember->workstations->pluck('orders')->flatten())
        ]);
    }


    private function calculateTimeSpent($workstationIds)
    {

        $totalTime = Orders::whereIn('workstation_id', $workstationIds)
                        ->sum(DB::raw('TIMESTAMPDIFF(SECOND, date_started, NOW())'));

        return gmdate('H:i:s', $totalTime);
    }

    public function notification()
    {
        $data['notifications'] = Notifications::with(['log','log.user','log.status','log.sub_status','comment','comment.user','comment.order'])
            ->orderBy('created_at','DESC')->get();

        return view('admin.notifications',$data);
    }

    public function manageStatuses()
    {
        $data['statuses'] = OrderStatus::paginate(10);
        return view('admin.manage-statuses', $data);
    }

    public function addStatuses(Request $request){
        $request->validate([
            'status-name' => 'required|string|max:255',
            'status-color' => 'required|string|max:10',
        ]);

        $status = new OrderStatus();
        $status->status_name = $request->input('status-name');
        $status->status_color = $request->input('status-color');
        $status->save();

        $response = [
            'status' => 200,
            'message' => 'Status added successfully.',
        ];

        return response()->json($response);
    }

    public function updateStatus(Request $request){
        $request->validate([
            'edit-status-name' => 'required|string|max:255',
            'edit-status-color' => 'required|string|max:10',
        ]);
        $status = OrderStatus::findOrFail($request->id);
        $status->status_name = $request->input('edit-status-name');
        $status->status_color = $request->input('edit-status-color');
        $status->save();

        $response = [
            'status' => 200,
            'message' => 'Status updated successfully.',
        ];

        return response()->json($response);
    }
    public function deleteStatus(Request $request)
    {
        $statusId = $request->input('id');
        $status = OrderStatus::find($statusId);
        if (!$status) {
            $response = [
                'status' => 404,
                'message' => 'Status not found.'
            ];
            return response()->json($response, 404);
        }
        $status->delete();
        $response = [
            'status' => 200,
            'message' => 'Status deleted successfully.'
        ];

        return response()->json($response);
    }



    public function sendSummaryEmail()
    {
        $completed_status = OrderStatus::where('status_name', Orders::statusCompleted)->pluck('id')->first();
        $hold_status = OrderStatus::where('status_name', Orders::statusHold)->pluck('id')->first();
        $issues = OrderStatus::whereIn('status_name', OrderStatus::adminStatuses)->pluck('id');

        $mailData = [
            'production_order' => Orders::with(['status', 'station', 'station.worker'])
                ->where('status_id', '!=', $completed_status)
                ->get(),
            'orders_on_hold' => Orders::with(['status', 'station', 'station.worker'])
                ->where('status_id', $hold_status)
                ->get(),
            'order_with_issues' => Orders::with(['status', 'station', 'station.worker'])
                ->whereIn('status_id', $issues)
                ->get(),
            'title' => 'Daily Summary Report'
        ];

        Mail::send('admin.email.summary_email', $mailData, function ($message) {
            $message->to(env('ADMIN_EMAIL'))
                    ->subject('Daily Summary Report');
        });
    }


    public function generateBarcode($status_name)
    {
        $generator = new BarcodeGeneratorJPG();
        $barcode = $generator->getBarcode($status_name, $generator::TYPE_CODE_128);

        return response($barcode)
            ->header('Content-Type', 'image/jpeg')
            ->header('Content-Disposition', 'inline; filename="'.strtolower($status_name).'_barcode.jpg"');
    }

}
