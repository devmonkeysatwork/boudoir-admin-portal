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

class AdminController extends Controller
{
    public function dashboard()
    {
        $query = Orders::with(['items','status','addresses','station','station.worker'])->where('orderType',Orders::parentType);
        $orders = $query->paginate(10);
        return view('admin.dashboard',compact('orders'));
    }

    public function orders()
    {
        $query = Orders::with(['items','status','addresses','station','station.worker','items.attributes']);
        $orders = $query->paginate(10);
        $workstations = Workstations::all();
        $statuses = OrderStatus::all();
//        dd($data);
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
            $orderStatus->user_id = $request->user_id;
            $orderStatus->updated_by = Auth::user()->id;
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
        $data['notifications'] = Notifications::with(['log','log.user','log.status','comment','comment.user','comment.order'])->get();

        return view('admin.notifications',$data);
    }

    public function manageStatuses()
    {
        $data['statuses'] = OrderStatus::all();


        return view('admin.manage-statuses',$data);
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
}
