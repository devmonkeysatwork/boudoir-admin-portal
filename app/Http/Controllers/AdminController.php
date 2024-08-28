<?php

namespace App\Http\Controllers;

use App\Events\NewMessage;
use App\Models\Notifications;
use App\Models\OrderLogs;
use App\Models\Orders;
use App\Models\OrderStatus;
use App\Models\User;
use App\Models\Workstations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Status IDs based on your categorization
        $inProductionStatusIds = [1, 2, 3, 4, 5, 12, 13, 14, 15, 16];
        $onHoldStatusIds = [7, 9, 10];
        $readyToShipStatusId = 8;
        $qualityControlStatusId = 6;

        // Dynamic counts for each category
        $inProductionOrdersCount = Orders::whereIn('status_id', $inProductionStatusIds)->count();
        $onHoldOrdersCount = Orders::whereIn('status_id', $onHoldStatusIds)->count();
        $readyToShipOrdersCount = Orders::where('status_id', $readyToShipStatusId)->count();
        $qualityControlOrdersCount = Orders::where('status_id', $qualityControlStatusId)->count();

        // Fetch orders with pagination
        $orders = Orders::with(['items', 'status', 'last_log', 'last_log.status', 'last_log.sub_status', 'addresses', 'station', 'station.worker'])
                        ->where('orderType', Orders::parentType)
                        ->paginate(10);

        $teamMembers = User::with(['workstations'])->get();

        foreach ($teamMembers as $teamMember) {
            $totalOrders = 0;

            foreach ($teamMember->workstations as $workstation) {
                $totalOrders += Orders::where('workstation_id', $workstation->id)->count();
            }

            $teamMember->total_orders = $totalOrders;
            $teamMember->time_spent = $this->calculateTimeSpent($teamMember->workstations->pluck('id'));
        }

        $workstations = Workstations::with(['orders'])->get();

        return view('admin.dashboard', compact(
            'inProductionOrdersCount',
            'onHoldOrdersCount',
            'readyToShipOrdersCount',
            'qualityControlOrdersCount',
            'orders',
            'teamMembers', 
            'workstations'
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

        foreach ($teamMembers as $teamMember) {
            $totalOrders = 0;

            foreach ($teamMember->workstations as $workstation) {
                $totalOrders += Orders::where('workstation_id', $workstation->id)->count();
            }

            $teamMember->total_orders = $totalOrders;
            $teamMember->time_spent = $this->calculateTimeSpent($teamMember->workstations->pluck('id'));
        }

        return view('admin.team', compact('teamMembers'));
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

}
