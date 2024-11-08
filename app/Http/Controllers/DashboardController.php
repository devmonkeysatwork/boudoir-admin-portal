<?php

namespace App\Http\Controllers;

use App\Models\OrderLogs;
use App\Models\Orders;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\ProductFlows;
use App\Models\SubStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $data['attributes'] = DB::select('SELECT DISTINCT type FROM item_attributes');




        $data['orders_completed'] = Orders::with(['items'])
            ->whereNotNull('date_completed')
            ->when($data['productName'], function ($query) use ($data) {
                // Only apply this condition if productName is set
                return $query->whereHas('items', function ($query) use ($data) {
                    $query->where('product_id', $data['productName']); // Use the actual product ID field
                });
            })
            ->when($data['status_id'], function ($query) use ($data){
                $query->where('status_id', $data['status_id']);
            })
            ->count();




        $data['total_time_spent'] = OrderLogs::when($data['status_id'], function ($query) use ($data) {
                return $query->where('status_id', $data['status_id']);
            })->when($data['teamMember'], function ($query) use ($data) {
                return $query->where('user_id', $data['teamMember']);
            })
            ->whereNotNull('time_started')->whereNotNull('time_end')->sum('time_spent');

        $data['avg_time_spent_on_order'] = OrderLogs::select('order_id', DB::raw('SUM(time_spent) as total_time_spent'))
            ->when($data['status_id'], function ($query) use ($data) {
                return $query->where('status_id', $data['status_id']);
            })->when($data['teamMember'], function ($query) use ($data) {
                return $query->where('user_id', $data['teamMember']);
            })
            ->whereNotNull('time_started')
            ->whereNotNull('time_end')
            ->groupBy('order_id')
            ->get()
            ->avg('total_time_spent');




        // Get the product ID for the album "The Album"
        $album_id = Product::whereName('The Album')->pluck('id')->first();
        $last12Months = Carbon::now()->subMonths(12)->startOfMonth();
        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $months[] = Carbon::now()->subMonths($i)->format('Y-m');
        }
        $total_orders = Orders::when($data['status_id'], function ($query) use ($data) {
                $query->where('status_id', $data['status_id']);
            })
            ->where('created_at', '>=', $last12Months)
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, count(*) as total_orders')
            ->groupBy('month')
            ->get();

        $orders_with_album = Orders::with(['items'])
            ->whereHas('items', function ($query) use ($album_id) {
                $query->where('product_id', $album_id); // Use the actual product ID field
            })
            ->when($data['status_id'], function ($query) use ($data) {
                $query->where('status_id', $data['status_id']);
            })
            ->where('created_at', '>=', $last12Months)
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, count(*) as orders_with_album')
            ->groupBy('month')
            ->get();



        //        ----------- Total time and Average time spent graph data
        $startDate = Carbon::now()->subMonths(12)->startOfMonth();

// Total time spent for each month in the last 12 months
        $monthly_total_time_spent = OrderLogs::select(
            DB::raw('YEAR(time_started) as year'),
            DB::raw('MONTH(time_started) as month'),
            DB::raw('SUM(time_spent) as total_time_spent')
        )
            ->when($data['status_id'], function ($query) use ($data) {
                return $query->where('status_id', $data['status_id']);
            })
            ->when($data['teamMember'], function ($query) use ($data) {
                return $query->where('user_id', $data['teamMember']);
            })
            ->whereNotNull('time_started')
            ->whereNotNull('time_end')
            ->where('time_started', '>=', $startDate) // Filter for the last 12 months
            ->groupBy(DB::raw('YEAR(time_started), MONTH(time_started)'))
            ->orderBy(DB::raw('YEAR(time_started), MONTH(time_started)'))
            ->get()
            ->mapWithKeys(function ($item) {
                return [
                    Carbon::create($item->year, $item->month, 1)->format('Y-m') => $item->total_time_spent
                ];
            });


// Average time spent per order in each month over the last 12 months
        $monthly_avg_time_spent = OrderLogs::select(
            DB::raw('YEAR(time_started) as year'),
            DB::raw('MONTH(time_started) as month'),
            DB::raw('AVG(time_spent) as avg_time_spent')
        )
            ->when($data['status_id'], function ($query) use ($data) {
                return $query->where('status_id', $data['status_id']);
            })
            ->when($data['teamMember'], function ($query) use ($data) {
                return $query->where('user_id', $data['teamMember']);
            })
            ->whereNotNull('time_started')
            ->whereNotNull('time_end')
            ->where('time_started', '>=', $startDate) // Filter for the last 12 months
            ->groupBy(DB::raw('YEAR(time_started), MONTH(time_started)'))
            ->orderBy(DB::raw('YEAR(time_started), MONTH(time_started)'))
            ->get()
            ->mapWithKeys(function ($item) {
                return [
                    Carbon::create($item->year, $item->month, 1)->format('Y-m') => $item->avg_time_spent
                ];
            });






        $comparison = [];
        $time_graph_data = [];
        foreach ($months as $month) {
            // Get the total orders for the current month
            $totalOrder = $total_orders->firstWhere('month', $month);
            $totalOrdersCount = $totalOrder ? $totalOrder->total_orders : 0;

            // Get the orders with album for the current month
            $ordersWithAlbum = $orders_with_album->firstWhere('month', $month);
            $ordersWithAlbumCount = $ordersWithAlbum ? $ordersWithAlbum->orders_with_album : 0;

            // Add the comparison data for this month
//            $comparison[] = [
//                'month' => $month,
//                'total_orders' => $totalOrdersCount,
//                'orders_with_album' => $ordersWithAlbumCount,
//            ];
            $comparison['months'][] = Carbon::parse($month)->format('M-Y');
            $comparison['total_orders'][] = $totalOrdersCount;
            $comparison['orders_with_album'][] = $ordersWithAlbumCount;


            $time_graph_data['total_time_spent'][] = isset($monthly_total_time_spent[$month]) ? round($monthly_total_time_spent[$month],2) : 0;
            $time_graph_data['avg_time_spent'][] = isset($monthly_avg_time_spent[$month]) ? round($monthly_avg_time_spent[$month],2)  : 0;
        }

        $data['orders_graph_monthly'] = $comparison;
        $data['time_graph_data'] = $time_graph_data;


        $onHoldStatusId = OrderStatus::where('status_name',OrderStatus::adminStatuses[0])->pluck('id')->first();
        $printIssueStatusId = OrderStatus::where('status_name',OrderStatus::adminStatuses[1])->pluck('id')->first();
        $remakeReasonStatusId = OrderStatus::where('status_name',OrderStatus::adminStatuses[2])->pluck('id')->first();

        $onHoldStatusOrders = Orders::where('status_id', $onHoldStatusId)->count();
        $printIssueOrders = Orders::where('status_id', $printIssueStatusId)->count();
        $remakeReasonOrders = Orders::where('status_id', $remakeReasonStatusId)->count();

        $data['order_with_issues'] = [$onHoldStatusOrders,$printIssueOrders,$remakeReasonOrders];

//        $results = OrderLogs::select('order_id', DB::raw('SUM(time_spent) as total_time_spent'))
//            ->whereNotNull('time_started')
//            ->whereNotNull('time_end')
//            ->groupBy('order_id')
//            ->pluck('total_time_spent');
//        dd($results);


        $error_count_per_user = $this->getErrorCountPerUSer($data['teamMember']);
//        dd($error_count_per_user);





        return view('admin.reports.reports',$data);
    }
    public function compare(Request $request){


        return view('admin.reports.comparison');
    }

    public function dashboard(Request $request)
    {

        $myWorkStatusID = auth()->user()->product_status_id ?? null;
        $ordersInQueue = 0;
        if($myWorkStatusID){
            $p_ids = ProductFlows::whereStepId($myWorkStatusID)->pluck('product_id','step_no');
            $previous_step = [];
            foreach ($p_ids as $index => $p_id){
                $previous_step[] = ProductFlows::where('product_id', $p_id)
                    ->where('step_no', '<', $index)
                    ->orderByDesc('step_no')
                    ->pluck('step_id')->first();
            }

            $ordersInQueue = Orders::with('items') // Eager load items relation
                ->whereHas('items', function ($query) use ($p_ids) {
                    $query->whereIn('product_id', $p_ids); // Filter items by product_id
                })
                ->whereIn('status_id',$previous_step)
                ->count();

        }
//        dd($p_ids,$previous_step,$all_orders);
        // Status IDs based on your categorization
        $readyForPrintStatusId = OrderStatus::where('status_name','Ready for Production')->pluck('id')->toArray();
        $onHoldStatusIds = OrderStatus::where('status_name','On hold')->pluck('id')->toArray();
        $completedStatusId = OrderStatus::where('status_name','Completed')->pluck('id')->toArray();
        $excludedStatusIds = array_merge(
            $completedStatusId,
            $readyForPrintStatusId
        );
        $inProductionStatusIds = OrderStatus::whereNotIn('id',$excludedStatusIds)->pluck('id');

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
            'orderLog',
            'ordersInQueue'
        ));
    }

    function getErrorCountPerUser($teamMember)
    {
        // Determine the range for the last 12 months (from the current month back to 12 months ago)
        $endDate = now();  // Current date (e.g., November 2024)
        $startDate = now()->subMonths(12);  // 12 months before the current date (e.g., November 2023)

        // Retrieve data for the specified user(s) with error counts over the last 12 months
        $dataResult = OrderLogs::when($teamMember, function ($query) use ($teamMember) {
            return $query->where('user_id', $teamMember);
        })
            ->whereNotNull('time_started')
            ->whereNotNull('time_end')
            ->where('error', 1)  // Only select orders where error = 1
            ->whereBetween('time_started', [$startDate->startOfMonth(), $endDate->endOfMonth()])  // Last 12 months
            ->selectRaw('
            user_id,
            COUNT(DISTINCT order_id) as order_count,
            YEAR(time_started) as year,
            MONTH(time_started) as month
        ')
            ->groupBy('user_id', 'year', 'month')
            ->orderBy('user_id')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Prepare the data in a more usable format
        $formattedData = [];

        foreach ($dataResult as $entry) {
            $userId = $entry->user_id;
            $year = $entry->year;
            $month = $entry->month;
            $orderCount = $entry->order_count;

            // Initialize user's data array if not already set
            if (!isset($formattedData[$userId])) {
                $formattedData[$userId] = [
                    'user_id' => $userId,
                    'orders' => []
                ];
            }

            // Set the order count for the given month (1 to 12)
            $formattedData[$userId]['orders'][$month] = $orderCount;
        }

        // Define the months array (1 to 12 mapped to month names)
        $monthsArray = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];

        // Get the last 12 months dynamically in reverse order
        $last12Months = [];
        for ($i = 0; $i < 12; $i++) {
            $month = $endDate->subMonth()->month;  // Get the month in reverse order (starting from the last month)
            $last12Months[] = [
                'month' => $month,
                'name' => $monthsArray[$month],  // Get the month name
            ];
        }

        // Reverse the array so it starts from the most recent month
        $last12Months = array_reverse($last12Months);

        // Extract just the months array for the final result
        $months = array_column($last12Months, 'name');

        // Finalize the data for each user (orders in the format [month1, month2, ..., month12])
        $finalData = [];
        foreach ($formattedData as $userData) {
            $userId = $userData['user_id'];
            $orders = $userData['orders'];

            // Create an array for the 12 months, filling missing months with 0
            $userOrdersForGraph = [];
            foreach ($last12Months as $monthData) {
                $month = $monthData['month'];  // Get the month number (1-12)
                $userOrdersForGraph[] = $orders[$month] ?? 0;  // Get order count or 0 if missing
            }

            // Add the user orders for the graph
            $finalData[] = $userOrdersForGraph;
        }

        // Return the final data which includes the user order counts for each month
        return [
            'data' => $finalData,  // Array of user order counts for the last 12 months
            'months' => $months,    // Array of month names (from the most recent month)
        ];
    }




}
