<?php

namespace App\Http\Controllers;

use App\Models\OrderLogs;
use App\Models\Orders;
use App\Models\OrderStatus;
use App\Models\SubStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    //

    public function reports(Request $request){


        return view('admin.reports.reports');
    }
    public function compare(Request $request){


        return view('admin.reports.comparison');
    }

    public function dashboard(Request $request)
    {

        $yesterday = Carbon::yesterday()->toDateString();
        $today = Carbon::today()->toDateString();

        $yesterdayCount = Orders::whereDate('date_started', $yesterday)->count();
        $todayCount = Orders::whereDate('date_started', $today)->count();
        $percentageChange = [];

        if ($yesterdayCount > 0) {
            $percentageChange['production'] = (($todayCount - $yesterdayCount) / $yesterdayCount) * 100;
        } elseif ($yesterdayCount === 0 && $todayCount > 0) {
            $percentageChange['production'] = 100; // All new orders
        } elseif ($yesterdayCount === 0 && $todayCount === 0) {
            $percentageChange['production'] = 0; // No change if both are zero
        }

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

//        Percentage Counts ****************************************
        $yesterdayStart = \Carbon\Carbon::yesterday()->startOfDay();
        $yesterdayEnd = \Carbon\Carbon::yesterday()->endOfDay();
        $todayStart = \Carbon\Carbon::today()->startOfDay();
        $todayEnd = \Carbon\Carbon::today()->endOfDay();
        // Initialize counts
        $orderCounts = [
            'readyForPrint' => ['yesterday' => 0, 'today' => 0],
            'onHold' => ['yesterday' => 0, 'today' => 0],
        ];

        // Count orders for each status updated yesterday
        $orderCounts['readyForPrint']['yesterday'] = Orders::whereIn('status_id', $readyForPrintStatusId)
            ->whereBetween('updated_at', [$yesterdayStart, $yesterdayEnd])
            ->count();

        $orderCounts['onHold']['yesterday'] = Orders::whereIn('status_id', $onHoldStatusIds)
            ->whereBetween('updated_at', [$yesterdayStart, $yesterdayEnd])
            ->count();

// Count orders for each status updated today
        $orderCounts['readyForPrint']['today'] = Orders::whereIn('status_id', $readyForPrintStatusId)
            ->whereBetween('updated_at', [$todayStart, $todayEnd])
            ->count();

        $orderCounts['onHold']['today'] = Orders::whereIn('status_id', $onHoldStatusIds)
            ->whereBetween('updated_at', [$todayStart, $todayEnd])
            ->count();

        foreach ($orderCounts as $status => $counts) {
            if ($counts['yesterday'] > 0) {
                $percentageChange[$status] = (($counts['today'] - $counts['yesterday']) / $counts['yesterday']) * 100;
            } else {
                $percentageChange[$status] = $counts['today'] > 0 ? 100 : 0; // If there were no orders yesterday
            }
        }

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
            'percentageChange',
            'statuses',
            'orderLog'
        ));
    }
}
