<?php

namespace App\Http\Controllers;

use App\Models\OrderLogs;
use App\Models\Orders;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\SubStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    //

    public function reports(Request $request){


        $data['productName'] = $request->input('product_name');
        $data['status_id'] = $request->input('status');
        $data['productOption'] = $request->input('product_option');
        $data['attribute'] = $request->input('attribute');
        $data['teamMember'] = $request->input('team_member');
        $data['groupBy'] = $request->input('group_by');

        $data['statuses'] = OrderStatus::all();
        $data['products'] = Product::all();
        $data['team_members'] = User::whereRoleId(2)->get();


        $data['orders_completed'] = Orders::with(['items'])
            ->whereNotNull('date_completed')
            ->when($data['productName'], function ($query) use ($data) {
                // Only apply this condition if productName is set
                return $query->whereHas('items', function ($query) use ($data) {
                    $query->where('product_id', $data['productName']); // Use the actual product ID field
                });
            })
            ->count();




        $data['total_time_spent'] = OrderLogs::when($data['status_id'], function ($query) use ($data) {
                return $query->where('status_id', $data['status_id']);
            })
            ->whereNotNull('time_started')->whereNotNull('time_end')->sum('time_spent');
        $data['avg_time_spent_on_order'] = OrderLogs::select('order_id', DB::raw('SUM(time_spent) as total_time_spent'))
            ->when($data['status_id'], function ($query) use ($data) {
                return $query->where('status_id', $data['status_id']);
            })
            ->whereNotNull('time_started')
            ->whereNotNull('time_end')
            ->groupBy('order_id')
            ->get()
            ->avg('total_time_spent');

//        $results = OrderLogs::select('order_id', DB::raw('SUM(time_spent) as total_time_spent'))
//            ->whereNotNull('time_started')
//            ->whereNotNull('time_end')
//            ->groupBy('order_id')
//            ->pluck('total_time_spent');
//        dd($results);





        return view('admin.reports.reports',$data);
    }
    public function compare(Request $request){


        return view('admin.reports.comparison');
    }

    public function dashboard(Request $request)
    {
        // Status IDs based on your categorization
        $readyForPrintStatusId = OrderStatus::where('status_name','Sent To Print')->pluck('id')->toArray();
        $onHoldStatusIds = OrderStatus::where('status_name','On hold')->pluck('id')->toArray();
        $completedStatusId = OrderStatus::where('status_name','Completed')->pluck('id')->toArray();
        $excludedStatusIds = array_merge(
            $completedStatusId
        );
        $inProductionStatusIds = OrderStatus::whereNotIn('status_name',$excludedStatusIds)->pluck('id');

        // Dynamic counts for each category
        $readyForPrintOrdersCount = Orders::whereIn('status_id', $readyForPrintStatusId)->count();
        $inProductionOrdersCount = Orders::whereIn('status_id', $inProductionStatusIds)->count();
        $onHoldOrdersCount = Orders::whereIn('status_id', $onHoldStatusIds)->count();

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

        $statuses = OrderStatus::all();
        $edit_statuses = OrderStatus::whereIn('status_name',OrderStatus::adminStatuses)->get();
        $sub_statuses = SubStatus::with('status')->get();


        $userId = auth()->id();
        // Fetch the associated OrderLogs to get the time_started
        $orderLog = OrderLogs::with(['user','status'])
            ->where('user_id',$userId)
            ->whereNotNull('time_started')
            ->whereNull('time_end')
            ->first();

        return view('admin.dashboard', compact(
            'readyForPrintOrdersCount',
            'inProductionOrdersCount',
            'onHoldOrdersCount',
            'orders',
            'edit_statuses',
            'sub_statuses',
            'filter_date',
            'statuses',
            'orderLog'
        ));
    }
}
