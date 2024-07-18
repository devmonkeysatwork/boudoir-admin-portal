<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use App\Models\OrderStatus;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function orders()
    {
        $data['orders'] = Orders::with(['items','status','addresses','station','station.worker','items.attributes'])
        ->get();
//        dd($data['orders']);
        return view('admin.orders',$data);
    }

    public function workstations()
    {
        return view('admin.workstations');
    }

    public function manageStatuses()
    {
        $data['statuses'] = OrderStatus::all();


        return view('admin.manage-statuses',$data);
    }

    public function manageEmails()
    {
        return view('admin.manage-emails');
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
