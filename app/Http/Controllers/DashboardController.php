<?php

namespace App\Http\Controllers;

use App\Models\OrderLogs;
use App\Models\Orders;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\ProductAttributes;
use App\Models\ProductAttributeValues;
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
        // Extract existing inputs
        $data['productName'] = $request->input('product_name')??null;
        $data['status_id'] = intval($request->input('status'));
        $data['productAttribute'] = $request->input('product_attribute');
        $data['attribute'] = $request->input('attribute');
        $data['teamMember'] = $request->input('team_member');
        $data['groupBy'] = $request->input('group_by');
        $data['date_range'] = $request->input('date_range');
        $dates = explode(' - ', $data['date_range']);

        // Parse start and end dates
        $data['start_date'] = isset($dates[0]) ? Carbon::parse($dates[0])->startOfDay() : null;
        $data['end_date'] = isset($dates[1]) ? Carbon::parse($dates[1])->endOfDay() : null;

        // For form display
        $data['start_date_display'] = $data['start_date'] ? $data['start_date']->format('m/d/Y') : '';
        $data['end_date_display'] = $data['end_date'] ? $data['end_date']->format('m/d/Y') : '';

        // If date range is not provided, use default values or don't apply date filtering
        $hasDateRange = $data['start_date'] && $data['end_date'];

        $data['statuses'] = OrderStatus::all();
        $data['products'] = Product::all();
        $data['team_members'] = User::whereRoleId(2)->get();
        $data['attributes'] = ProductAttributes::where('product_id', $data['productName'])->get(['id', 'name']);
        $data['attributesValues'] = ProductAttributeValues::where('attribute_id', $data['productAttribute'])->get(['id', 'value']);

        // Get the completed status ID once and reuse it
        $completed_id = OrderStatus::whereTitle('Completed')->pluck('id')->first();

        // Modified to use last_log with completed status consistently and include date filtering
        $ordersCompletedQuery = Orders::whereHas('last_log', function ($query) use ($completed_id) {
            $query->where('status_id', $completed_id);
        })
        ->whereHas('logs', function ($query) use ($data) {
            // Apply filtering on user_id and status_id only if they're present
            if (!empty($data['teamMember'])) {
                $query->where('user_id', $data['teamMember']);
            }
            if (!empty($data['status_id'])) {
                $query->where('status_id', $data['status_id']);
            }
        })
        ->when($data['productName'], function ($query) use ($data) {
            return $query->whereHas('items', function ($query) use ($data) {
                $query->where('product_id', $data['productName']);
            });
        })
        ->whereHas('items.attributes', function ($query) use ($data) {
            if (!empty($data['productAttribute'])) {
                $query->where('attribute_id', $data['productAttribute']);
            }
            if (!empty($data['attribute'])) {
                $query->where('attribute_value_id', $data['attribute']);
            }
        });

        // Apply date range filter if provided
        if ($hasDateRange) {
            $ordersCompletedQuery->whereHas('last_log', function ($query) use ($data) {
                $query->whereBetween('time_end', [$data['start_date'], $data['end_date']]);
            });
        }

        $data['orders_completed'] = $ordersCompletedQuery->count();

        // Time spent calculations with date range
        $totalTimeSpentQuery = OrderLogs::whereHas('order', function ($query) use ($completed_id) {
            $query->whereHas('last_log', function ($q) use ($completed_id) {
                $q->where('status_id', $completed_id);
            });
        })
        ->when($data['status_id'], function ($query) use ($data) {
            return $query->where('status_id', $data['status_id']);
        })
        ->when($data['teamMember'], function ($query) use ($data) {
            return $query->where('user_id', $data['teamMember']);
        })
        ->whereNotNull('time_started')
        ->whereNotNull('time_end');

        // Apply date range filter to time spent
        if ($hasDateRange) {
            $totalTimeSpentQuery->whereBetween('time_end', [$data['start_date'], $data['end_date']]);
        }

        $data['total_time_spent'] = $totalTimeSpentQuery->sum('time_spent');

        // Modified average time calculation with date range
        $avgTimeSpentQuery = OrderLogs::select('order_id', DB::raw('SUM(time_spent) as total_time_spent'))
            ->whereHas('order', function ($query) use ($completed_id) {
                $query->whereHas('last_log', function ($q) use ($completed_id) {
                    $q->where('status_id', $completed_id);
                });
            })
            ->when($data['status_id'], function ($query) use ($data) {
                return $query->where('status_id', $data['status_id']);
            })
            ->when($data['teamMember'], function ($query) use ($data) {
                return $query->where('user_id', $data['teamMember']);
            })
            ->whereNotNull('time_started')
            ->whereNotNull('time_end');

        // Apply date range filter
        if ($hasDateRange) {
            $avgTimeSpentQuery->whereBetween('time_end', [$data['start_date'], $data['end_date']]);
        }

        $data['avg_time_spent_on_order'] = $avgTimeSpentQuery
            ->groupBy('order_id')
            ->get()
            ->avg('total_time_spent');

        // Get the product ID for the album product
        $selected_product_id = $data['productName'] > 0 ?$data['productName']: 1;
        $data['selected_product'] = Product::whereId($selected_product_id)->pluck('name')->first();
        // For monthly breakdown charts, use either the selected date range or default to last 12 months
        if ($hasDateRange) {
            $startDate = $data['start_date'];
            $endDate = $data['end_date'];
            // Calculate number of months between start and end date
            $monthsDiff = $startDate->diffInMonths($endDate) + 1; // +1 to include both start and end months
            $months = [];
            // Generate months array based on the specified range
            for ($i = 0; $i < $monthsDiff; $i++) {
                $months[] = $startDate->copy()->addMonths($i)->format('Y-m');
            }
        } else {
            // Default: use last 12 months
            $startDate = Carbon::now()->subMonths(11)->startOfMonth(); // to get 12 months including current
            $endDate = Carbon::now()->endOfMonth();
            $months = [];
            for ($i = 0; $i < 12; $i++) {
                $months[] = Carbon::now()->subMonths(11-$i)->format('Y-m'); // Start from 11 months ago to include current month
            }
        }

        $teamMember = $data['teamMember'] ?? null;

        // Total orders query with date range
        $totalOrdersQuery = Orders::when($data['status_id'], function ($query) use ($data) {
            $query->where('status_id', $data['status_id']);
        })
            ->whereHas('last_log', function ($query) use ($completed_id) {
                $query->where('status_id', $completed_id);
            })
            ->when($teamMember, function ($query) use ($teamMember) {
                $query->whereHas('logs', function ($q) use ($teamMember) {
                    $q->where('user_id', $teamMember);
                });
            });

        // Apply date range filter
        if ($hasDateRange) {
            $totalOrdersQuery->whereHas('last_log', function ($query) use ($data) {
                $query->whereBetween('time_end', [$data['start_date'], $data['end_date']]);
            });
        } else {
            $totalOrdersQuery->where('created_at', '>=', $startDate);
        }

        $total_orders = $totalOrdersQuery
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, count(*) as total_orders')
            ->groupBy('month')
            ->get();

        // Orders with specified product query with date range
        $ordersWithAlbumQuery = Orders::with(['items'])
            ->whereHas('items', function ($query) use ($selected_product_id) {
                $query->where('product_id', $selected_product_id);
            })
            ->whereHas('last_log', function ($query) use ($completed_id) {
                $query->where('status_id', $completed_id);
            })
            ->when($teamMember, function ($query) use ($teamMember) {
                $query->whereHas('logs', function ($q) use ($teamMember) {
                    $q->where('user_id', $teamMember);
                });
            })
            ->when($data['status_id'], function ($query) use ($data) {
                $query->where('status_id', $data['status_id']);
            });

        // Apply date range filter
        if ($hasDateRange) {
            $ordersWithAlbumQuery->whereHas('last_log', function ($query) use ($data) {
                $query->whereBetween('time_end', [$data['start_date'], $data['end_date']]);
            });
        } else {
            $ordersWithAlbumQuery->where('created_at', '>=', $startDate);
        }

        $orders_with_album = $ordersWithAlbumQuery
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, count(*) as orders_with_album')
            ->groupBy('month')
            ->get();

        // Monthly time spent totals with date range
        $monthlyTotalTimeQuery = OrderLogs::select(
            DB::raw('YEAR(time_started) as year'),
            DB::raw('MONTH(time_started) as month'),
            DB::raw('SUM(time_spent) as total_time_spent')
        )
            ->whereHas('order', function ($query) use ($completed_id) {
                $query->whereHas('last_log', function ($q) use ($completed_id) {
                    $q->where('status_id', $completed_id);
                });
            })
            ->when($data['status_id'], function ($query) use ($data) {
                return $query->where('status_id', $data['status_id']);
            })
            ->when($data['teamMember'], function ($query) use ($data) {
                return $query->where('user_id', $data['teamMember']);
            })
            ->when($data['productName'], function ($query) use ($data) {
                $query->whereHas('order.items', function ($q) use ($data) {
                    $q->where('product_id', $data['productName']);
                });
            })
            ->whereNotNull('time_started')
            ->whereNotNull('time_end');

        // Apply date range filter
        if ($hasDateRange) {
            $monthlyTotalTimeQuery->whereBetween('time_end', [$data['start_date'], $data['end_date']]);
        } else {
            $monthlyTotalTimeQuery->where('time_started', '>=', $startDate);
        }

        $monthly_total_time_spent = $monthlyTotalTimeQuery
            ->groupBy(DB::raw('YEAR(time_started), MONTH(time_started)'))
            ->orderBy(DB::raw('YEAR(time_started), MONTH(time_started)'))
            ->get()
            ->mapWithKeys(function ($item) {
                return [
                    Carbon::create($item->year, $item->month, 1)->format('Y-m') => $item->total_time_spent
                ];
            });

        // Monthly average time spent with date range
        $monthlyAvgTimeQuery = OrderLogs::select(
            DB::raw('YEAR(time_started) as year'),
            DB::raw('MONTH(time_started) as month'),
            DB::raw('AVG(time_spent) as avg_time_spent')
        )
            ->whereHas('order', function ($query) use ($completed_id) {
                $query->whereHas('last_log', function ($q) use ($completed_id) {
                    $q->where('status_id', $completed_id);
                });
            })
            ->when($data['status_id'], function ($query) use ($data) {
                return $query->where('status_id', $data['status_id']);
            })
            ->when($data['teamMember'], function ($query) use ($data) {
                return $query->where('user_id', $data['teamMember']);
            })
            ->when($data['productName'], function ($query) use ($data) {
                $query->whereHas('order.items', function ($q) use ($data) {
                    $q->where('product_id', $data['productName']);
                });
            })
            ->whereNotNull('time_started')
            ->whereNotNull('time_end');

        // Apply date range filter
        if ($hasDateRange) {
            $monthlyAvgTimeQuery->whereBetween('time_end', [$data['start_date'], $data['end_date']]);
        } else {
            $monthlyAvgTimeQuery->where('time_started', '>=', $startDate);
        }

        $monthly_avg_time_spent = $monthlyAvgTimeQuery
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

            $comparison['months'][] = Carbon::parse($month)->format('M-Y');
            $comparison['total_orders'][] = $totalOrdersCount;
            $comparison['orders_with_album'][] = $ordersWithAlbumCount;

            $time_graph_data['total_time_spent'][] = isset($monthly_total_time_spent[$month]) ? round($monthly_total_time_spent[$month],2) : 0;
            $time_graph_data['avg_time_spent'][] = isset($monthly_avg_time_spent[$month]) ? round($monthly_avg_time_spent[$month],2)  : 0;
        }

        $data['orders_graph_monthly'] = $comparison;
        $data['time_graph_data'] = $time_graph_data;

        // Get the status IDs for the three statuses
        $onHoldStatusId = OrderStatus::where('status_name', OrderStatus::adminStatuses[0])->pluck('id')->first();
        $printIssueStatusId = OrderStatus::where('status_name', OrderStatus::adminStatuses[1])->pluck('id')->first();
        $remakeReasonStatusId = OrderStatus::where('status_name', OrderStatus::adminStatuses[2])->pluck('id')->first();

        // Modified reprinting orders queries with date range
        $reprintingOrdersQueries = [
            // On Hold Orders
            OrderLogs::whereHas('order', function ($query) use ($completed_id) {
                $query->whereHas('last_log', function ($q) use ($completed_id) {
                    $q->where('status_id', $completed_id);
                });
            })
                ->when($teamMember, function ($query) use ($teamMember) {
                    return $query->where('user_id', $teamMember);
                })
                ->when($data['productName'], function ($query) use ($data) {
                    $query->whereHas('order.items', function ($q) use ($data) {
                        $q->where('product_id', $data['productName']);
                    });
                })
                ->whereNotNull('time_started')
                ->whereNotNull('time_end')
                ->where('status_id', $onHoldStatusId),

            // Print Issue Orders
            OrderLogs::whereHas('order', function ($query) use ($completed_id) {
                $query->whereHas('last_log', function ($q) use ($completed_id) {
                    $q->where('status_id', $completed_id);
                });
            })
                ->when($teamMember, function ($query) use ($teamMember) {
                    return $query->where('user_id', $teamMember);
                })
                ->when($data['productName'], function ($query) use ($data) {
                    $query->whereHas('order.items', function ($q) use ($data) {
                        $q->where('product_id', $data['productName']);
                    });
                })
                ->whereNotNull('time_started')
                ->whereNotNull('time_end')
                ->where('error', 1)
                ->where('status_id', $printIssueStatusId),

            // Remake Reason Orders
            OrderLogs::whereHas('order', function ($query) use ($completed_id) {
                $query->whereHas('last_log', function ($q) use ($completed_id) {
                    $q->where('status_id', $completed_id);
                });
            })
                ->when($teamMember, function ($query) use ($teamMember) {
                    return $query->where('user_id', $teamMember);
                })
                ->when($data['productName'], function ($query) use ($data) {
                    $query->whereHas('order.items', function ($q) use ($data) {
                        $q->where('product_id', $data['productName']);
                    });
                })
                ->whereNotNull('time_started')
                ->whereNotNull('time_end')
                ->where('status_id', $remakeReasonStatusId)
        ];

        // Apply date range filter to all three queries
        if ($hasDateRange) {
            foreach ($reprintingOrdersQueries as &$query) {
                $query->whereBetween('time_end', [$data['start_date'], $data['end_date']]);
            }
        }

        // Execute each query and store results
        $data['reprinting_orders'] = [
            $reprintingOrdersQueries[0]->count(),
            $reprintingOrdersQueries[1]->count(),
            $reprintingOrdersQueries[2]->count()
        ];

        // Call the modified getErrorCountPerUser method with date range
        $data['error_count_per_user'] = $this->getErrorCountPerUser(
            $data['teamMember'],
            $data['productName'],
            $data['status_id'],
            $completed_id,
            $hasDateRange ? $data['start_date'] : null,
            $hasDateRange ? $data['end_date'] : null
        );

        return view('admin.reports.reports', $data);
    }
    public function compare(Request $request){
        $data['team_members'] = User::whereRoleId(2)->get();
        $data['teamMember1'] = $request->input('team_member_1');
        $data['teamMember2'] = $request->input('team_member_2');

        $data['group_by'] = $request->input('group');
        $data['date_range'] = $request->input('date_range');
        $dates = explode(' - ', $data['date_range']);

        // Parse start and end dates
        $startDate = !empty($dates[0])
            ? Carbon::parse($dates[0])->startOfDay()
            : Carbon::now()->startOfMonth();

        $endDate = !empty($dates[1])
            ? Carbon::parse($dates[1])->endOfDay()
            : Carbon::now();
        $data['start_date'] = Carbon::parse($startDate);
        $data['end_date'] = Carbon::parse($endDate);


        // First, get all orders that have last_log with status_id = completed_status_id
        $completed_status_id = OrderStatus::whereTitle('Completed')->pluck('id')->first();
        $completedOrderIds = OrderLogs::whereHas('order', function ($query) use ($completed_status_id) {
            $query->whereHas('last_log', function ($q) use ($completed_status_id) {
                $q->where('status_id', $completed_status_id);
            });
        })
            ->pluck('order_id')
            ->unique();

        $userIds = [$data['teamMember1'], $data['teamMember2']];
        $users = User::whereIn('id', $userIds)->pluck('name', 'id');
        $data['employees'] = array_map(function ($id) use ($users) {
            return $users[$id] ?? null;
        }, $userIds);


        $data['performanceData'] = $this->getUserPerformanceComparison(
            $completedOrderIds,
            $data['teamMember1'],
            $data['teamMember2'],
            $data['start_date'],
            $data['end_date'],
            $data['group_by'],
        );
//        dd($data['performanceData']);

        // Format data for the chart
//        $chartData = $this->formatDataForChart($performanceData);

//        $result = [
//            'chart_data' => $chartData,
//            'users' => [
//                'user1' => [
//                    'id' => $data['teamMember1'],
//                    'name' => $performanceData['user1']['name'],
//                    'total_orders' => array_sum($performanceData['user1']['counts']),
//                    'avg_per_day' => round(array_sum($performanceData['user1']['counts']) / count($performanceData['dates']), 2)
//                ],
//                'user2' => [
//                    'id' => $data['teamMember2'],
//                    'name' => $performanceData['user2']['name'],
//                    'total_orders' => array_sum($performanceData['user2']['counts']),
//                    'avg_per_day' => round(array_sum($performanceData['user2']['counts']) / count($performanceData['dates']), 2)
//                ]
//            ]
//        ];


        // First, get all orders that have last_log with status_id = 21
//        $completedOrderIds = OrderLogs::whereHas('order', function ($query) use ($completed_status_id) {
//            $query->whereHas('last_log', function ($q) use ($completed_status_id) {
//                $q->where('status_id', $completed_status_id);
//            });
//        })
//            ->pluck('order_id')
//            ->unique();
//
//        // Get date range for the query
//        $dates = $this->generateDateRange($data['start_date'], $data['end_date'],$data['group_by']);
//
//        // Initialize result array with dates and zero counts
//        $result = [
//            'dates' => $dates,
//            'user1' => [
//                'name' => User::find($data['teamMember1'])->name ?? "User {$data['teamMember1']}",
//                'counts' => array_fill(0, count($dates), 0)
//            ],
//            'user2' => [
//                'name' => User::find($data['teamMember2'])->name ?? "User {$data['teamMember2']}",
//                'counts' => array_fill(0, count($dates), 0)
//            ]
//        ];
//
//        // Query for user 1
//        $user1Data = $this->getUserOrderCounts($data['teamMember1'], $completedOrderIds, $data['start_date'], $data['end_date'],$data['group_by']);
//
//        // Query for user 2
//        $user2Data = $this->getUserOrderCounts($data['teamMember2'], $completedOrderIds, $data['start_date'], $data['end_date'],$data['group_by']);
//
//        // Fill in the actual counts
//        foreach ($dates as $index => $date) {
//            $formattedDate = $date->format('Y-m-d');
//            $result['user1']['counts'][$index] = $user1Data[$formattedDate] ?? 0;
//            $result['user2']['counts'][$index] = $user2Data[$formattedDate] ?? 0;
//        }
//
//        $data['data'] =  $result;



        // Get time metrics for both users
        $user1TimeData = $this->getUserTimeData($data['teamMember1'], $completedOrderIds, $data['start_date'], $data['end_date'], $data['group_by']);
        $user2TimeData = $this->getUserTimeData($data['teamMember2'], $completedOrderIds, $data['start_date'], $data['end_date'], $data['group_by']);
        $dates = $this->generateDateRange($data['start_date'], $data['end_date'], $data['group_by']);
        // Fill in the dates and actual time metrics
        foreach ($dates as $index => $date) {
            $dateKey = $this->getDateKeyForGrouping($date, $data['group_by']);
            $result['dates'][] = $dateKey;
            $result['user1']['total_time'][$index] = $user1TimeData[$dateKey]['total_time'] ?? 0;
            $result['user1']['avg_time'][$index] = $user1TimeData[$dateKey]['avg_time'] ?? 0;
            $result['user2']['total_time'][$index] = $user2TimeData[$dateKey]['total_time'] ?? 0;
            $result['user2']['avg_time'][$index] = $user2TimeData[$dateKey]['avg_time'] ?? 0;
        }
        $data['timeData'] = $result;


//        dd($data['timeData']);
        $data['start_date'] = Carbon::parse($startDate)->format('m-d-Y');
        $data['end_date'] = Carbon::parse($endDate)->format('m-d-Y');
        return view('admin.reports.comparison',$data);
    }

    public function efficiency(Request $request){
        $data['team_members'] = User::whereRoleId(2)->get();
        $data['teamMember1'] = $request->input('team_member_1');
        $data['teamMember2'] = $request->input('team_member_2');

        $data['group_by'] = $request->input('group');
        $data['date_range'] = $request->input('date_range');
        $dates = explode(' - ', $data['date_range']);

        // Parse start and end dates
        $startDate = !empty($dates[0])
            ? Carbon::parse($dates[0])->startOfMonth()
            : Carbon::now()->startOfYear();

        $endDate = !empty($dates[1])
            ? Carbon::parse($dates[1])->endOfDay()
            : Carbon::now();
        $data['start_date'] = Carbon::parse($startDate)->format('m-d-Y');
        $data['end_date'] = Carbon::parse($endDate)->format('m-d-Y');


        $all_remake_statuses = OrderStatus::where('title',OrderStatus::adminStatuses[2])->with('sub_status')->pluck('id')->first();
        $all_remake_sub_statuses = SubStatus::where('status_id',$all_remake_statuses)->pluck('sub_status.id');


        $teamMember = $data['teamMember1'] ?? null;
        $teamMember2 = $data['teamMember2'] ?? null;
        $completed_id = OrderStatus::whereTitle('Completed')->pluck('id')->first();

        //Query to get how many time an error on a specific workstation was reported for each reason.
        $dataResult = OrderLogs::where('status_id', $all_remake_statuses)
            ->when($teamMember || $teamMember2, function ($query) use ($teamMember,$teamMember2) {
                return $query->where(function ($q) use ($teamMember, $teamMember2) {
                    if ($teamMember) {
                        $q->Where('user_id', $teamMember);
                    }
                    if ($teamMember2) {
                        $q->orWhere('user_id', $teamMember2);
                    }
                });
            })
            ->whereHas('order', function ($query) use ($completed_id) {
                $query->whereHas('last_log', function ($q) use ($completed_id) {
                    $q->where('status_id', $completed_id);
                });
            })
            ->whereNotNull('time_started')
            ->whereNotNull('time_end')
            ->whereBetween('time_end', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->select(
                'order_logs.user_id',
                'order_logs.sub_status_id',
                'order_logs.status_id',
                DB::raw('(
            SELECT prev.status_id
            FROM order_logs prev
            WHERE prev.id = (
                SELECT MAX(ol.id)
                FROM order_logs ol
                WHERE ol.order_id = order_logs.order_id
                AND ol.created_at < order_logs.created_at
            )
        ) AS previous_status_id'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('order_logs.user_id', 'order_logs.sub_status_id', 'order_logs.status_id', 'previous_status_id')
            ->with('user')
            ->with('sub_status')
//            ->orderBy('order_logs.user_id')
            ->orderBy('order_logs.sub_status_id')
            ->orderBy('previous_status_id')
            ->get();

        $workstationIds = $dataResult->pluck('previous_status_id')->filter()->unique()->toArray();
        $subStatusIds = $dataResult->pluck('sub_status_id')->filter()->unique()->toArray();
        $statusTitles = OrderStatus::whereIn('id', $workstationIds)
            ->pluck('status_name', 'id')
            ->toArray();
        $subStatusTitles = SubStatus::whereIn('id', $subStatusIds)
            ->pluck('name', 'id')
            ->toArray();
        $graph = [];

        foreach ($dataResult as $res) {
            $statusKey = $res->previous_status_id;
            $subStatusKey = $res->sub_status_id;

            if (isset($statusTitles[$statusKey]) && isset($subStatusTitles[$subStatusKey])) {
                $statusTitle = $statusTitles[$statusKey];
                $subStatusTitle = $subStatusTitles[$subStatusKey];

                if (!isset($graph[$statusTitle][$subStatusTitle])) {
                    $graph[$statusTitle][$subStatusTitle] = 0;
                }

                $graph[$statusTitle][$subStatusTitle] += $res->count;
            }
        }

        $data['workstations'] = $statusTitles;
        $data['issues'] = $subStatusTitles;
        $data['issues_count'] = $graph;


        $data['performance_stats'] = $this->getPerformanceStats($teamMember,$teamMember2);


//        dd($data['performance_stats']);

        return view('admin.reports.comparison_efficiency',$data);
    }
    private function getPerformanceStats($teamMember = null, $teamMember2 = null)
    {
        $stats = [];

        if ($teamMember || $teamMember2) {
            if ($teamMember) {
                $stats[] = $this->getUserTaskStats($teamMember);
            }
            if ($teamMember2) {
                $stats[] = $this->getUserTaskStats($teamMember2);
            }
        } else {
            $users = OrderLogs::select('user_id')->distinct()->pluck('user_id');
            foreach ($users as $userId) {
                $stats[] = $this->getUserTaskStats($userId);
            }
        }

        return $stats;
    }

    private function getUserTaskStats($userId)
    {
        $logs = OrderLogs::select(
            'users.name as user_name',
            DB::raw('COUNT(*) as total_tasks'),
            DB::raw('SUM(TIMESTAMPDIFF(SECOND, time_started, time_end)) as total_duration_sec'),
            DB::raw('MIN(time_started) as first_task_time'),
            DB::raw('MAX(time_end) as last_task_time')
        )
            ->join('users', 'order_logs.user_id', '=', 'users.id')
            ->where('order_logs.user_id', $userId)
            ->whereNotNull('time_started')
            ->whereNotNull('time_end')
            ->groupBy('users.name')
            ->first();

        if (!$logs) return null;

        // Calculate total working hours
        $totalSeconds = strtotime($logs->last_task_time) - strtotime($logs->first_task_time);
        $totalHours = $totalSeconds > 0 ? $totalSeconds / 3600 : 1;

        $tasksPerHour = $logs->total_tasks / $totalHours;

        $avgCompletionMin = $logs->total_duration_sec > 0
            ? ($logs->total_duration_sec / $logs->total_tasks) / 60
            : 0;

        return [
            'user_name' => $logs->user_name,
            'total_tasks' => $logs->total_tasks,
            'avg_completion_time_min' => round($avgCompletionMin, 2),
            'tasks_per_hour' => round($tasksPerHour, 2),
        ];
    }

    public function quality(Request $request){
        $data['team_members'] = User::whereRoleId(2)->get();
        $data['teamMember1'] = $request->input('team_member_1');
        $data['teamMember2'] = $request->input('team_member_2');

        $data['group_by'] = $request->input('group');
        $data['date_range'] = $request->input('date_range');
        $dates = explode(' - ', $data['date_range']);

        $startDate = !empty($dates[0])
            ? Carbon::parse($dates[0])->startOfDay()
            : Carbon::now()->startOfYear();

        $endDate = $data['end_date'] = !empty($dates[1])
            ? Carbon::parse($dates[1])->endOfDay()
            : Carbon::now();
        $data['start_date'] = Carbon::parse($startDate)->format('m-d-Y');
        $data['end_date'] = Carbon::parse($endDate)->format('m-d-Y');
//        dd($startDate);

        $all_remake_statuses = OrderStatus::where('title',OrderStatus::adminStatuses[2])->with('sub_status')->pluck('id')->first();
//        $all_remake_sub_statuses = SubStatus::where('status_id',$all_remake_statuses)->pluck('sub_status.id');


        $teamMember1 = $data['teamMember1'] ?? null;
        $teamMember2 = $data['teamMember2'] ?? null;

        $completed_id = OrderStatus::whereTitle('Completed')->pluck('id')->first();

        $dataResult = OrderLogs::where('status_id', $all_remake_statuses)
            ->whereHas('order', function ($query) use ($completed_id,$startDate, $endDate) {
                $query->whereHas('last_log', function ($q) use ($completed_id,$startDate, $endDate) {
                    $q->where('status_id', $completed_id)
                        ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                            $q->whereBetween('time_end', [$startDate, $endDate]);
                        });
                });
            })
            ->whereNotNull('time_started')
            ->whereNotNull('time_end')
            ->where(function ($query) use ($teamMember1, $teamMember2) {
                // Add condition for both team members or all users
                $query->whereExists(function ($subquery) use ($teamMember1, $teamMember2) {
                    $subquery->select(DB::raw(1))
                        ->from('order_logs as prev_logs')
                        ->whereRaw('prev_logs.order_id = order_logs.order_id')
                        ->whereRaw('prev_logs.created_at < order_logs.created_at')
                        ->where(function($q) use ($teamMember1, $teamMember2) {
                            if ($teamMember1 && $teamMember2) {
                                $q->whereIn('prev_logs.user_id', [$teamMember1, $teamMember2]);
                            } elseif ($teamMember1) {
                                $q->where('prev_logs.user_id', $teamMember1);
                            } elseif ($teamMember2) {
                                $q->where('prev_logs.user_id', $teamMember2);
                            }
                        })
                        ->where('prev_logs.error', 1)
                        ->orderBy('prev_logs.created_at', 'desc')
                        ->limit(1);
                });
            })
            ->select(
                DB::raw('(SELECT prev.user_id
                  FROM order_logs prev
                  WHERE prev.order_id = order_logs.order_id
                  AND prev.created_at < order_logs.created_at
                  ORDER BY prev.created_at DESC LIMIT 1) AS worker_user_id'),
                'order_logs.sub_status_id',
                'order_logs.status_id',
                DB::raw('(SELECT prev.status_id
                  FROM order_logs prev
                  WHERE prev.order_id = order_logs.order_id
                  AND prev.created_at < order_logs.created_at
                  ORDER BY prev.created_at DESC LIMIT 1) AS previous_status_id'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('worker_user_id', 'order_logs.sub_status_id', 'order_logs.status_id', 'previous_status_id')
            ->with(['sub_status'])
            ->orderBy('order_logs.sub_status_id')
            ->orderBy('previous_status_id')
            ->get();

        $userIds = $dataResult->pluck('worker_user_id')->filter()->unique()->toArray();
        $subStatusIds = $dataResult->pluck('sub_status_id')->filter()->unique()->toArray();

        $userNames = User::whereIn('id', $userIds)
            ->pluck('name', 'id')
            ->toArray();

        $subStatusTitles = SubStatus::whereIn('id', $subStatusIds)
            ->pluck('name', 'id')
            ->toArray();

        $graph = [];

        foreach ($dataResult as $res) {
            $statusKey = $res->worker_user_id;
            $subStatusKey = $res->sub_status_id;

            if (isset($userNames[$statusKey]) && isset($subStatusTitles[$subStatusKey])) {
                $statusTitle = $userNames[$statusKey];
                $subStatusTitle = $subStatusTitles[$subStatusKey];

                if (!isset($graph[$statusTitle][$subStatusTitle])) {
                    $graph[$statusTitle][$subStatusTitle] = 0;
                }

                $graph[$statusTitle][$subStatusTitle] += $res->count;
            }
        }

        $data['workstations'] = $userNames;
        $data['issues'] = $subStatusTitles;
        $data['issues_count'] = $graph;
        $data['qcPassChart'] = $this->getErrorPercentage($teamMember1,$teamMember2);
//        dd($data['qcPassChart']);


        return view('admin.reports.comparison_quality',$data);
    }

    private function getErrorPercentage($teamMember = null, $teamMember2 = null){
        $error_percentage = [];
        if($teamMember || $teamMember2){
            if($teamMember){
                $error_percentage[] = OrderLogs::selectRaw('
                    COUNT(*) as total,
                    users.name as user_name,
                    SUM(CASE WHEN error = 1 THEN 1 ELSE 0 END) as error_count
                ')
                    ->where('user_id', $teamMember)
                    ->join('users', 'order_logs.user_id', '=', 'users.id')
                    ->groupBy('users.name')
                    ->first();
            }
            if($teamMember2){
                $error_percentage[] = OrderLogs::selectRaw('
                    COUNT(*) as total,
                    users.name as user_name,
                    SUM(CASE WHEN error = 1 THEN 1 ELSE 0 END) as error_count
                ')
                    ->where('user_id', $teamMember2)
                    ->join('users', 'order_logs.user_id', '=', 'users.id')
                    ->groupBy('users.name')
                    ->first();
            }
        }else{
            $results = OrderLogs::selectRaw('
                    order_logs.user_id,
                    users.name as user_name,
                    COUNT(*) as total,
                    SUM(CASE WHEN error = 1 THEN 1 ELSE 0 END) as error_count
                ')
                ->join('users', 'order_logs.user_id', '=', 'users.id')
                ->groupBy('order_logs.user_id', 'users.name')
                ->get();

            // Now format the output
            $error_percentage = $results->map(function ($row) {
                $percentageErrors = $row->total > 0 ? ($row->error_count / $row->total) * 100 : 0;
                return [
                    'user_name' => $row->user_name,
                    'total' => $row->total,
                    'error_count' => $row->error_count,
                ];
            });
        }

        $chartData = collect($error_percentage)->map(function ($error_percentage) {
            $passRate = (($error_percentage['total'] - $error_percentage['error_count']) / $error_percentage['total']) * 100;
            return [
                'name' => $error_percentage['user_name'],
                'rate' => round($passRate, 2),
            ];
        });

        return $chartData;
    }

    private function getUserPerformanceComparison($completedOrderIds, $user1_id, $user2_id, $start_date, $end_date, $group_by = 'daily')
    {
        // Get date range for the query based on grouping
        $dates = $this->generateDateRange($start_date, $end_date, $group_by);



        // Initialize result array with dates and zero counts
        $result = [
            'dates' => [],
            'group_by' => $group_by,
            'user1' => [
//                'name' => $user1Name,
                'counts' => array_fill(0, count($dates), 0)
            ],
            'user2' => [
//                'name' => $user2Name,
                'counts' => array_fill(0, count($dates), 0)
            ]
        ];

        // Query for user 1
        $user1Data = $this->getUserOrderCounts($user1_id, $completedOrderIds, $start_date, $end_date, $group_by);

        // Query for user 2
        $user2Data = $this->getUserOrderCounts($user2_id, $completedOrderIds, $start_date, $end_date, $group_by);

        // Fill in the actual counts based on the grouping
        foreach ($dates as $index => $date) {
            $dateKey = $this->getDateKeyForGrouping($date, $group_by);
            $result['dates'][] = $dateKey;
            $result['user1']['counts'][$index] = $user1Data[$dateKey] ?? 0;
            $result['user2']['counts'][$index] = $user2Data[$dateKey] ?? 0;
        }
        return $result;
    }


    public function dashboard(Request $request)
    {
        $myWorkStatusIDs = auth()->user()->workstations->pluck('status_id')->toArray();
        $ordersInQueue = 0;
        $my_product_ids = [];
        if($myWorkStatusIDs){
            $p_ids = ProductFlows::whereIn('step_id',$myWorkStatusIDs)->get();
            $previous_step = [];
            foreach ($p_ids as $p_id){
                $previous_step[] = ProductFlows::where('product_id', $p_id->product_id)
                    ->where('step_no', '<', $p_id->step_no)
                    ->orderByDesc('step_no')
                    ->pluck('step_id')->first();
                $my_product_ids[]=$p_id->product_id;
            }

            $ordersInQueue = Orders::with('items') // Eager load items relation
                ->whereHas('items', function ($query) use ($my_product_ids) {
                    $query->whereIn('product_id', $my_product_ids); // Filter items by product_id
                })
                ->whereIn('status_id',$previous_step)
                ->count();

        }
//        dd($my_product_ids,$myWorkStatusIDs,$ordersInQueue,$previous_step);
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
        $filter_by_time = $request->input('filter_by_time');
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
            ->whereHas('items', function ($query) use ($my_product_ids) {
                $query->whereIn('product_id', $my_product_ids)  // Filter items by product_id
                ->where(function ($attrQuery) {
                    // For items with specified attribute types, ensure 'value' is not 'None'
                    $attrQuery->whereHas('attributes', function ($subQuery) {
                        $subQuery->whereIn('type', Product::possibleNoneValueAttributes)
                            ->whereNotIn('title', ['none']);
                    })
                        // Or ensure items without these attributes are also included
                    ->orWhereHas('attributes', function ($subQuery) {
                        $subQuery->whereNotIn('type', Product::possibleNoneValueAttributes);
                    });
                });
            })
            ->whereNotIn('status_id',$completedStatusId)
            ->where('orderType','=',Orders::parentType);
        $orders = $query->paginate(10);

        $statuses = OrderStatus::whereIn('id',$myWorkStatusIDs)->get();
        $edit_statuses = OrderStatus::whereIn('status_name',OrderStatus::adminStatuses)->get();
        $sub_statuses = SubStatus::with('status')->get();


        $userId = auth()->id();
        // Fetch the associated OrderLogs to get the time_started
        $orderLog = OrderLogs::with(['user','status','order'])
            ->where('user_id',$userId)
            ->whereNotNull('time_started')
            ->whereNull('time_end')
            ->get();

        return view('admin.dashboard', compact(
            'readyForPrintOrdersCount',
            'inProductionOrdersCount',
            'onHoldOrdersCount',
            'orders',
            'edit_statuses',
            'sub_statuses',
            'filter_date',
            'filter_by_time',
            'statuses',
            'orderLog',
            'ordersInQueue'
        ));
    }

    function getErrorCountPerUser($teamMember, $product_id, $status_id, $completed_id, $startDate = null, $endDate = null)
    {
        // If no date range specified, use last 12 months
        if (!$startDate || !$endDate) {
            $endDate = now();  // Current date
            $startDate = now()->copy()->subMonths(12);  // 12 months before the current date
        }

        $totalErrors = 0;

        // Modified to include date range filter
        $dataResult = OrderLogs::when($teamMember, function ($query) use ($teamMember) {
            return $query->where('user_id', $teamMember);
        })
            ->when($product_id, function ($query) use ($product_id) {
                $query->whereHas('order.items', function ($q) use ($product_id) {
                    $q->where('product_id', $product_id);
                });
            })
            ->whereHas('order', function ($query) use ($completed_id) {
                $query->whereHas('last_log', function ($q) use ($completed_id) {
                    $q->where('status_id', $completed_id);
                });
            })
            ->when($status_id, function ($query) use ($status_id) {
                return $query->where('status_id', $status_id);
            })
            ->with('user')
            ->whereNotNull('time_started')
            ->whereNotNull('time_end')
            ->where('error', 1)
            ->whereBetween('time_end', [$startDate->startOfDay(), $endDate->endOfDay()])
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

        // Format data by user and month
        $formattedData = [];

        foreach ($dataResult as $entry) {
            $userId = $entry->user_id;
            $userName = $entry->user->name;
            $year = $entry->year;
            $month = $entry->month;
            $orderCount = $entry->order_count;
            if (!isset($formattedData[$userId])) {
                $formattedData[$userId] = [
                    'user_id' => $userId,
                    'userName' => $userName,
                    'orders' => []
                ];
            }
            $totalErrors += $orderCount;
            $formattedData[$userId]['orders']["$year-$month"] = $orderCount;
        }

        // Calculate months array based on date range
        $monthDiff = $startDate->diffInMonths($endDate) + 1; // +1 to include both start and end months
        $last12Months = [];
        $monthLabels = [];
        $dateCursor = $startDate->copy()->startOfMonth();

        for ($i = 0; $i < $monthDiff; $i++) {
            $key = $dateCursor->format('Y-n'); // E.g., 2024-5
            $last12Months[] = $key;
            $monthLabels[] = $dateCursor->format('F'); // E.g., May
            $dateCursor->addMonth();
        }

        // Final data format
        $finalData = [];
        $userIds = [];

        foreach ($formattedData as $userId => $userData) {
            $userOrders = [];

            foreach ($last12Months as $monthKey) {
                $userOrders[] = $userData['orders'][$monthKey] ?? 0;
            }

            $finalData[] = $userOrders;
            $userIds[] = $userData['userName'];
        }

        return [
            'data' => $finalData,
            'months' => $monthLabels,     // Month names for x-axis
            'users' => $userIds,           // User IDs for labeling the graph
            'totalErrors' => $totalErrors  // Total error count
        ];
    }

    /**
     * Get order counts for a specific user based on time grouping
     * Count orders where time_started and time_end are on the same day
     *
     * @param int $user_id User ID
     * @param Collection $completedOrderIds Collection of order IDs
     * @param string $start_date Start date
     * @param string $end_date End date
     * @param string $group_by Group by option (daily, weekly, monthly, yearly)
     * @return array Order counts grouped by time period
     */
    private function getUserOrderCounts($user_id, $completedOrderIds, $start_date, $end_date, $group_by = 'daily')
    {
        $query = OrderLogs::whereIn('order_id', $completedOrderIds);
            // Filter by user_id only if a valid user ID is provided
            if (!empty($user_id)) {
                $query->where('user_id', $user_id);
            } else {
                // If user_id is empty, get data for all team members (role_id = 2)
                $teamMemberIds = User::whereRoleId(2)->pluck('id')->toArray();
                $query->whereIn('user_id', $teamMemberIds);
            }
            $query->whereNotNull('time_started')
            ->whereNotNull('time_end')
            // Only count logs where time_started and time_end are on the same day
            ->whereRaw('DATE(time_started) = DATE(time_end)')
            ->whereBetween(DB::raw('DATE(time_started)'), [$start_date, $end_date]);

        // Define the appropriate date format and grouping based on group_by parameter
        switch ($group_by) {
            case 'weekly':
                $dateFormat = "DATE_FORMAT(time_started, '%Y-%u')"; // Year-Week format (ISO week)
                $labelFormat = 'yearweek';
                break;
            case 'monthly':
                $dateFormat = "DATE_FORMAT(time_started, '%Y-%m')"; // Year-Month format
                $labelFormat = 'yearmonth';
                break;
            case 'yearly':
                $dateFormat = "YEAR(time_started)"; // Year format
                $labelFormat = 'year';
                break;
            case 'daily':
            default:
                $dateFormat = "DATE(time_started)"; // Daily format
                $labelFormat = 'date';
                break;
        }

        $counts = $query->select(DB::raw("{$dateFormat} as {$labelFormat}"), DB::raw('COUNT(DISTINCT order_id) as order_count'))
            ->groupBy($labelFormat)
            ->pluck('order_count', $labelFormat)
            ->toArray();

        return $counts;
    }

    /**
     * Generate array of Carbon objects between two dates based on group_by parameter
     *
     * @param string $start_date Start date
     * @param string $end_date End date
     * @param string $group_by Group by option (daily, weekly, monthly, yearly)
     * @return array Array of Carbon objects
     */
    private function generateDateRange($start_date, $end_date, $group_by = 'daily')
    {
        $start = Carbon::parse($start_date);
        $end = Carbon::parse($end_date);

        switch ($group_by) {
            case 'weekly':
                $start = $start->startOfWeek();
                $end = $end->endOfWeek()->startOfDay();
                $interval = 'week';
                break;
            case 'monthly':
                $start = $start->startOfMonth();
                $end = $end->endOfMonth()->startOfDay();
                $interval = 'month';
                break;
            case 'yearly':
                $start = $start->startOfYear();
                $end = $end->endOfYear()->startOfDay();
                $interval = 'year';
                break;
            case 'daily':
            default:
                $start = $start->startOfDay();
                $end = $end->startOfDay();
                $interval = 'day';
                break;
        }

        $dates = [];
        $currentDate = $start->copy();

        while ($currentDate->lte($end)) {
            $dates[] = $currentDate->copy();
            $currentDate->add(1, $interval);
        }

        return $dates;
    }

    /**
     * Get the appropriate key for the current date based on grouping
     *
     * @param Carbon $date Date to format
     * @param string $group_by Group by option
     * @return string Formatted date key
     */
    private function getDateKeyForGrouping(Carbon $date, $group_by)
    {
        switch ($group_by) {
            case 'weekly':
                return $date->format('Y-W'); // Year-Week format
            case 'monthly':
                return $date->format('Y-m'); // Year-Month format
            case 'yearly':
                return $date->format('Y'); // Year format
            case 'daily':
            default:
                return $date->format('Y-m-d'); // Default daily format
        }
    }

    /**
     * Format data for chart display based on grouping
     *
     * @param array $performanceData Performance data from getUserPerformanceComparison
     * @return array Chart-ready data
     */
    private function formatDataForChart($performanceData)
    {
//        dd($performanceData );
        $chartData = [];
        $groupBy = $performanceData['group_by'] ?? 'daily';

        foreach ($performanceData['dates'] as $index => $date) {
            // Format the date label based on grouping
            $dateLabel = $this->formatDateForDisplay($date, $groupBy);

            $chartData[] = [
                'date' => $dateLabel,
                'rawDate' => $this->getDateKeyForGrouping($date, $groupBy),
                'user1Orders' => $performanceData['user1']['counts'][$index],
                'user2Orders' => $performanceData['user2']['counts'][$index],
                'user1Name' => $performanceData['user1']['name'],
                'user2Name' => $performanceData['user2']['name'],
            ];
        }

        return $chartData;
    }

    /**
     * Format date for display based on grouping
     *
     * @param Carbon $date Date to format
     * @param string $group_by Group by option
     * @return string Formatted date for display
     */
    private function formatDateForDisplay(Carbon $date, $group_by)
    {
        switch ($group_by) {
            case 'weekly':
                $weekStart = $date->copy()->startOfWeek()->format('M d');
                $weekEnd = $date->copy()->endOfWeek()->format('M d, Y');
                return "{$weekStart} - {$weekEnd}";
            case 'monthly':
                return $date->format('F Y');
            case 'yearly':
                return $date->format('Y');
            case 'daily':
            default:
                return $date->format('Y-m-d');
        }
    }



    /**
     * Get time spent data for a specific user on completed orders
     *
     * @param int $user_id User ID
     * @param array $completedOrderIds Array of completed order IDs
     * @param Carbon $start_date Start date
     * @param Carbon $end_date End date
     * @param string $group_by Group by option (daily, weekly, monthly, yearly)
     * @return array Time data grouped by date
     */
    private function getUserTimeData($user_id, $completedOrderIds, $start_date, $end_date, $group_by = 'daily')
    {
        $query = OrderLogs::whereIn('order_id', $completedOrderIds);
            // Filter by user_id only if a valid user ID is provided
            if (!empty($user_id)) {
                $query->where('user_id', $user_id);
            } else {
                $teamMemberIds = User::whereRoleId(2)->pluck('id')->toArray();
                $query->whereIn('user_id', $teamMemberIds);
            }
        $query->whereNotNull('time_started')
            ->whereNotNull('time_end')
            // Only count logs where time_started and time_end are present
            ->whereRaw('time_end > time_started')
            ->whereBetween(DB::raw('DATE(time_started)'), [$start_date, $end_date]);

        // Define the appropriate date format and grouping based on group_by parameter
        switch ($group_by) {
            case 'weekly':
                $dateFormat = "DATE_FORMAT(time_started, '%Y-%u')"; // Year-Week format (ISO week)
                $labelFormat = 'yearweek';
                break;
            case 'monthly':
                $dateFormat = "DATE_FORMAT(time_started, '%Y-%m')"; // Year-Month format
                $labelFormat = 'yearmonth';
                break;
            case 'yearly':
                $dateFormat = "YEAR(time_started)"; // Year format
                $labelFormat = 'year';
                break;
            case 'daily':
            default:
                $dateFormat = "DATE(time_started)"; // Daily format
                $labelFormat = 'date';
                break;
        }

        // Calculate time difference in minutes between time_started and time_end
        $timeData = $query->select(
            DB::raw("{$dateFormat} as {$labelFormat}"),
            DB::raw('SUM(TIMESTAMPDIFF(MINUTE, time_started, time_end)) as total_minutes'),
            DB::raw('AVG(TIMESTAMPDIFF(MINUTE, time_started, time_end)) as avg_minutes'),
            DB::raw('COUNT(DISTINCT order_id) as order_count')
        )
            ->groupBy($labelFormat)
            ->get();

        // Format the results into an associative array
        $formattedData = [];
        foreach ($timeData as $item) {
            $formattedData[$item->{$labelFormat}] = [
                'total_time' => round($item->total_minutes, 2),
                'avg_time' => round($item->avg_minutes, 2),
                'order_count' => $item->order_count
            ];
        }
        return $formattedData;
    }


}
