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
    
        return view('admin.dashboard', compact(
            'inProductionOrdersCount',
            'onHoldOrdersCount',
            'readyToShipOrdersCount',
            'qualityControlOrdersCount',
            'orders'
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

    public function workstations()
    {
        $data['workstations'] = Workstations::with('worker')->get();
        return view('admin.workstations',$data);
    }
    public function notification()
    {
        $data['notifications'] = Notifications::with(['log','log.user','log.status','log.sub_status','comment','comment.user','comment.order'])
            ->orderBy('created_at','DESC')->get();

        return view('admin.notifications',$data);
    }

    public function manageStatuses()
    {
        $data['statuses'] = OrderStatus::paginate(10); // Pagination applied here
        return view('admin.manage-statuses', $data);
    }

    public function addStatuses(Request $request){
        $request->validate([
            'status-name' => 'required|string|max:255',
            'status-color' => 'required|string|max:10', // Assuming color is a hex code
        ]);

        // Create a new status record
        $status = new OrderStatus();
        $status->status_name = $request->input('status-name');
        $status->status_color = $request->input('status-color');
        $status->save();

        // Prepare response data
        $response = [
            'status' => 200,
            'message' => 'Status added successfully.',
        ];

        return response()->json($response);
    }

    public function updateStatus(Request $request){
        // Validate incoming request
        $request->validate([
            'edit-status-name' => 'required|string|max:255',
            'edit-status-color' => 'required|string|max:10', // Assuming color is a hex code
        ]);
        $status = OrderStatus::findOrFail($request->id);
        $status->status_name = $request->input('edit-status-name');
        $status->status_color = $request->input('edit-status-color');
        $status->save();

        // Prepare response data
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



    public function sendSummaryEmail(){

        $completed_status = OrderStatus::where('status_name',Orders::statusCompleted)->pluck('id')->first();
        $hold_status = OrderStatus::where('status_name',Orders::statusHold)->pluck('id')->first();
        $issues = OrderStatus::whereIn('status_name',OrderStatus::adminStatuses)->pluck('id');

        $mailData['production_order'] = Orders::with(['status','station','station.worker'])
            ->where('status_id','!=',$completed_status)->get();
        $mailData['orders_on_hold'] = Orders::with(['status','station','station.worker'])
            ->where('status_id',$hold_status)->get();
        $mailData['order_with_issues'] = Orders::with(['status','station','station.worker'])
            ->whereIn('status_id',$issues)->get();

        $mailData['title']='Daily Summary Report';
        Mail::to(env('ADMIN_EMAIL'))->send(new \App\Mail\OrderSummaryEmail($mailData));
    }
}
