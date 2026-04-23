<?php


namespace App\Http\Controllers;

use App\Events\NewMessage;
use App\Exports\ProductionReportExport;
use App\Mail\TemplateEmail;
use App\Models\EmailTemplates;
use App\Models\Notifications;
use App\Models\OrderLogs;
use App\Models\Orders;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\ProductAttributes;
use App\Models\ProductAttributeValues;
use App\Models\Roles;
use App\Models\SubStatus;
use App\Models\TimelinePool;
use App\Models\User;
use App\Models\Workstations;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

//use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Facades\Excel;
use Picqer\Barcode\BarcodeGeneratorJPG;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {

        if (Auth::user()->role_id != 1) {
            return redirect()->route('my_dashboard');
        }

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
        $readyForPrintStatusId = OrderStatus::where('status_name', 'Ready for Production')->pluck('id')->toArray();
        $onHoldStatusIds = OrderStatus::where('status_name', 'On hold')->pluck('id')->toArray();
        $readyToShipStatusId = OrderStatus::where('status_name', 'Ready to Ship')->pluck('id')->toArray();
        $qualityControlStatusId = OrderStatus::where('status_name', 'Quality Control')->pluck('id')->toArray();
        $completedStatusId = OrderStatus::where('status_name', 'Completed')->pluck('id')->toArray();
        $remakeStatusId = OrderStatus::RemakeStatusIds;
        $excludedStatusIds = array_merge(
            $readyForPrintStatusId,
//            $onHoldStatusIds,
//            $readyToShipStatusId,
//            $qualityControlStatusId,
            $completedStatusId
        );
        $inProductionStatusIds = OrderStatus::whereNotIn('id', $excludedStatusIds)->pluck('id');

        // Dynamic counts for each category
        $readyForPrintOrdersCount = Orders::whereIn('status_id', $readyForPrintStatusId)->count();
        $inProductionOrdersCount = Orders::whereIn('status_id', $inProductionStatusIds)->count();
        $onHoldOrdersCount = Orders::whereIn('status_id', $onHoldStatusIds)->count();
        $readyToShipOrdersCount = Orders::whereIn('status_id', $readyToShipStatusId)->count();
        $qualityControlOrdersCount = Orders::whereIn('status_id', $qualityControlStatusId)->count();

        // Fetch orders with pagination
        $filter_date = $request->input('filter_date');
        $filter_by_time = $request->input('filter_by_time');
        $query = Orders::with([
            'activeChildren' => function ($query) use ($remakeStatusId) {
                $query->withExists(['logs as has_remake' => function ($q) use ($remakeStatusId) {
                    $q->whereIn('status_id', $remakeStatusId);
                }]);
            },
            'items',
            'status',
            'last_log',
            'last_log.status',
            'last_log.sub_status',
            'addresses',
            'station',
            'station.worker',
            'items.attributes',
            // Add first log relationship
            'first_log' => function ($query) {
                $query->whereNotNull('time_started')
                    ->orderBy('time_started', 'ASC')
                    ->limit(1);
            }
        ])
            ->withExists(['logs as has_remake' => function ($query) use ($remakeStatusId) {
                $query->whereIn('status_id', $remakeStatusId);
            }])
            ->when($filter_date, function ($q) use ($filter_date) {
                if ($filter_date == 'oldest') {
                    $q->orderBy(\Illuminate\Support\Facades\DB::raw('DATE(date_started)'), 'ASC');
                } elseif ($filter_date == 'newest') {
                    $q->orderBy(\Illuminate\Support\Facades\DB::raw('DATE(date_started)'), 'DESC');
                }
            }, function ($q) {
                $q->orderBy('is_rush', 'DESC')
                    ->orderBy(\Illuminate\Support\Facades\DB::raw('DATE(deadline)'), 'DESC')
                    ->orderBy(\Illuminate\Support\Facades\DB::raw('DATE(date_started)'), 'DESC');
            })
            ->when($filter_by_time, function ($q) use ($filter_by_time) {
                if ($filter_by_time == 'day') {
                    $q->whereDate('date_started', now()->toDateString());
                } elseif ($filter_by_time == 'week') {
                    $q->whereBetween('date_started', [
                        now()->startOfWeek()->toDateString(),
                        now()->endOfWeek()->toDateString()
                    ]);
                } elseif ($filter_by_time == 'month') {
                    $q->whereBetween('date_started', [
                        now()->startOfMonth()->toDateString(),
                        now()->endOfMonth()->toDateString()
                    ]);
                } elseif ($filter_by_time == 'year') {
                    $q->whereYear('date_started', now()->year);
                }
            })
//            ->where('orderType','=',Orders::parentType)
            ->where('status_id', '!=', $completedStatusId[0]);
        $orders = $query->get();


        $edit_statuses = OrderStatus::whereIn('status_name', OrderStatus::adminStatuses)->get();
        $sub_statuses = SubStatus::with('status')->get();
        if (Auth::user()->role_id == 1) {
            $statuses = OrderStatus::whereNotIn('status_name', OrderStatus::adminStatuses)->get();
        } else {
            $myWorkStatusIDs = auth()->user()->workstations->pluck('status_id')->toArray();
            $statuses = OrderStatus::whereIn('id', $myWorkStatusIDs)->get();
        }

//        Percentage Counts ****************************************
        $yesterdayStart = \Carbon\Carbon::yesterday()->startOfDay();
        $yesterdayEnd = \Carbon\Carbon::yesterday()->endOfDay();
        $todayStart = \Carbon\Carbon::today()->startOfDay();
        $todayEnd = \Carbon\Carbon::today()->endOfDay();
        // Initialize counts
        $orderCounts = [
            'readyForPrint' => ['yesterday' => 0, 'today' => 0],
            'onHold' => ['yesterday' => 0, 'today' => 0],
            'readyToShip' => ['yesterday' => 0, 'today' => 0],
            'qualityControl' => ['yesterday' => 0, 'today' => 0],
            'production' => ['yesterday' => 0, 'today' => 0],
        ];

        // Count orders for each status updated yesterday
        $orderCounts['readyForPrint']['yesterday'] = Orders::whereIn('status_id', $readyForPrintStatusId)
            ->whereBetween('created_at', [$yesterdayStart, $yesterdayEnd])
            ->count();

        $orderCounts['onHold']['yesterday'] = Orders::whereIn('status_id', $onHoldStatusIds)
            ->whereBetween('updated_at', [$yesterdayStart, $yesterdayEnd])
            ->count();

        $orderCounts['readyToShip']['yesterday'] = Orders::whereIn('status_id', $readyToShipStatusId)
            ->whereBetween('updated_at', [$yesterdayStart, $yesterdayEnd])
            ->count();

        $orderCounts['qualityControl']['yesterday'] = Orders::whereIn('status_id', $qualityControlStatusId)
            ->whereBetween('updated_at', [$yesterdayStart, $yesterdayEnd])
            ->count();

        $orderCounts['production']['yesterday'] = Orders::whereIn('status_id', $inProductionStatusIds)
            ->whereBetween('updated_at', [$yesterdayStart, $yesterdayEnd])
            ->count();

// Count orders for each status updated today
        $orderCounts['readyForPrint']['today'] = Orders::whereIn('status_id', $readyForPrintStatusId)
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->count();

        $orderCounts['onHold']['today'] = Orders::whereIn('status_id', $onHoldStatusIds)
            ->whereBetween('updated_at', [$todayStart, $todayEnd])
            ->count();

        $orderCounts['readyToShip']['today'] = Orders::whereIn('status_id', $readyToShipStatusId)
            ->whereBetween('updated_at', [$todayStart, $todayEnd])
            ->count();

        $orderCounts['qualityControl']['today'] = Orders::whereIn('status_id', $qualityControlStatusId)
            ->whereBetween('updated_at', [$todayStart, $todayEnd])
            ->count();
        $orderCounts['production']['today'] = Orders::whereIn('status_id', $inProductionStatusIds)
            ->whereBetween('updated_at', [$todayStart, $todayEnd])
            ->count();


        foreach ($orderCounts as $status => $counts) {
            if ($counts['yesterday'] > 0) {
                $percentageChange[$status] = round((($counts['today'] - $counts['yesterday']) / $counts['yesterday']) * 100, 0);
            } else {
                $percentageChange[$status] = $counts['today'] > 0 ? 100 : 0; // If there were no orders yesterday
            }
        }

        $userId = auth()->id();
        // Fetch the associated OrderLogs to get the time_started
        $orderLog = OrderLogs::with(['user', 'status', 'order'])
            ->where('user_id', $userId)
            ->whereNotNull('time_started')
            ->whereNull('time_end')
            ->get();
        $now = Carbon::now();
        $team_sort = $request->input('team_sort');
        $workstation_sort = $request->input('workstation_sort');
        $teamMembers = $this->getTeamCounts($team_sort, $now);
        $workstations = $this->getWorkstationCounts($workstation_sort, $now, $excludedStatusIds);
        $waitingId = OrderStatus::where('status_name', 'Waiting')->pluck('id')->first();
        return view('admin.dashboard', compact(
            'readyForPrintOrdersCount',
            'inProductionOrdersCount',
            'onHoldOrdersCount',
            'readyToShipOrdersCount',
            'qualityControlOrdersCount',
            'orders',
            'teamMembers',
            'team_sort',
            'workstations',
            'workstation_sort',
            'edit_statuses',
            'sub_statuses',
            'filter_date',
            'filter_by_time',
            'percentageChange',
            'statuses',
            'orderLog',
            'waitingId',
        ));
    }

    function getTeamCounts($filter, $now)
    {
        // Fetch all logs with time_started and time_end
        $logsQuery = OrderLogs::select(
            'order_logs.id',
            'order_logs.user_id',
            'order_logs.order_id',
            'order_logs.time_started',
            'order_logs.time_end',
            'users.name as user_name'
        )
            ->join('users', 'order_logs.user_id', '=', 'users.id')
            ->whereNotNull('order_logs.time_started')
            ->whereNotNull('order_logs.time_end');

        // Apply filter
        switch ($filter) {
            case 'day':
                $logsQuery->whereDate('order_logs.time_started', $now->toDateString());
                break;
            case 'week':
                $logsQuery->whereBetween('order_logs.time_started', [
                    $now->copy()->startOfWeek()->toDateString(),
                    $now->copy()->endOfWeek()->toDateString(),
                ]);
                break;
            case 'month':
                $logsQuery->whereMonth('order_logs.time_started', $now->month)
                    ->whereYear('order_logs.time_started', $now->year);
                break;
            case 'year':
                $logsQuery->whereYear('order_logs.time_started', $now->year);
                break;
            default:
                break;
        }

        $logs = $logsQuery->get();

        // Group by user and calculate working time
        $teamMembers = $logs->groupBy('user_id')->map(function ($userLogs, $userId) {
            $totalMinutes = 0;
            $uniqueOrders = $userLogs->pluck('order_id')->unique();

            foreach ($userLogs as $log) {
                // Use the NEW helper function
                $workingMinutes = calculateWorkingMinutes($log->time_started, $log->time_end);
                $totalMinutes += $workingMinutes;
            }

            return [
                'id' => $userId,
                'user_name' => $userLogs->first()->user_name,
                'order_count' => $uniqueOrders->count(),
                'total_minutes' => $totalMinutes,
            ];
        });

        return $teamMembers;
    }


    function getWorkstationCounts($filter, $now, $excludedStatusIds)
    {
        $periodStart = null;
        $periodEnd = null;

        // Make sure $excludedStatusIds is an array
        if (!is_array($excludedStatusIds)) {
            $excludedStatusIds = $excludedStatusIds->toArray();
        }

        // Query logs and exclude the specified status IDs
        $logsQuery = OrderLogs::whereNotIn('status_id', $excludedStatusIds)
            ->whereNotNull('time_started')
            ->whereNotNull('status_id');  // ✅ Make sure status_id exists

        if ($filter) {
            switch ($filter) {
                case 'day':
                    $periodStart = $now->copy()->startOfDay();
                    $periodEnd = $now->copy()->endOfDay();

                    $logsQuery->where(function ($q) use ($now) {
                        $q->whereDate('time_started', $now->toDateString())
                            ->orWhere(function ($subQ) use ($now) {
                                $subQ->where('time_started', '<=', $now->endOfDay())
                                    ->where(function ($endQ) use ($now) {
                                        $endQ->whereNull('time_end')
                                            ->orWhere('time_end', '>=', $now->startOfDay());
                                    });
                            });
                    });
                    break;

                case 'week':
                    $periodStart = $now->copy()->startOfWeek();
                    $periodEnd = $now->copy()->endOfWeek();

                    $logsQuery->where(function ($q) use ($now) {
                        $q->whereBetween('time_started', [
                            $now->copy()->startOfWeek(),
                            $now->copy()->endOfWeek(),
                        ])
                            ->orWhere(function ($subQ) use ($now) {
                                $subQ->where('time_started', '<=', $now->copy()->endOfWeek())
                                    ->where(function ($endQ) use ($now) {
                                        $endQ->whereNull('time_end')
                                            ->orWhere('time_end', '>=', $now->copy()->startOfWeek());
                                    });
                            });
                    });
                    break;

                case 'month':
                    $periodStart = $now->copy()->startOfMonth();
                    $periodEnd = $now->copy()->endOfMonth();

                    $logsQuery->where(function ($q) use ($now) {
                        $q->where(function ($dateQ) use ($now) {
                            $dateQ->whereMonth('time_started', $now->month)
                                ->whereYear('time_started', $now->year);
                        })
                            ->orWhere(function ($subQ) use ($now) {
                                $subQ->where('time_started', '<=', $now->copy()->endOfMonth())
                                    ->where(function ($endQ) use ($now) {
                                        $endQ->whereNull('time_end')
                                            ->orWhere('time_end', '>=', $now->copy()->startOfMonth());
                                    });
                            });
                    });
                    break;

                case 'year':
                    $periodStart = $now->copy()->startOfYear();
                    $periodEnd = $now->copy()->endOfYear();

                    $logsQuery->where(function ($q) use ($now) {
                        $q->whereYear('time_started', $now->year)
                            ->orWhere(function ($subQ) use ($now) {
                                $subQ->where('time_started', '<=', $now->copy()->endOfYear())
                                    ->where(function ($endQ) use ($now) {
                                        $endQ->whereNull('time_end')
                                            ->orWhere('time_end', '>=', $now->copy()->startOfYear());
                                    });
                            });
                    });
                    break;
            }
        }

        $logs = $logsQuery->with('status')->get();

        // Filter out any logs where status is null or excluded after loading
        $logs = $logs->filter(function ($log) use ($excludedStatusIds) {
            return $log->status && !in_array($log->status_id, $excludedStatusIds);
        });

        $workstations = $logs->groupBy('status_id')->map(function ($statusLogs) use ($now, $filter, $periodStart, $periodEnd) {
            $totalMinutes = 0;
            $orderCount = $statusLogs->pluck('order_id')->unique()->count();

            foreach ($statusLogs as $log) {
                $startTime = Carbon::parse($log->time_started);
                $endTime = $log->time_end ? Carbon::parse($log->time_end) : $now;

                if ($filter && $periodStart && $periodEnd) {
                    $effectiveStart = $startTime->copy()->max($periodStart);
                    $effectiveEnd = $endTime->copy()->min($periodEnd);

                    if ($effectiveEnd > $effectiveStart) {
                        $totalMinutes += calculateWorkingMinutes($effectiveStart, $effectiveEnd);
                    }
                } else {
                    $totalMinutes += calculateWorkingMinutes($startTime, $endTime);
                }
            }

            $status = $statusLogs->first()->status;

            return (object)[
                'id' => $status->id,
                'status_id' => $status->id,
                'status_name' => $status->status_name,
                'status_color' => $status->status_color ?? null,
                'order_count' => $orderCount,
                'unique_orders_count' => $orderCount,
                'total_minutes' => $totalMinutes,
                'time_spent_minutes' => $totalMinutes,
                'status' => $status,
            ];
        })->values();

        return $workstations;
    }

    public function orders()
    {
        $query = Orders::with(['items', 'status', 'addresses', 'station', 'station.worker', 'items.attributes']);
        $orders = $query->paginate(10);
        $workstations = Workstations::all();
        $statuses = OrderStatus::all();

        return view('admin.orders', compact('orders', 'workstations', 'statuses'));
    }

    public function updateOrderStatus(Request $request)
    {
        try {
            DB::beginTransaction();
            $order = Orders::find($request->id);
            $order->status_id = $request->edit_status;
            $order->save();

            $engravingId = OrderStatus::where('status_name', OrderStatus::ENGRAVING)->pluck('id')->first();
            $waitingId = OrderStatus::where('status_name', OrderStatus::Waiting)->pluck('id')->first();
            //If there is sub status for on-hold or any other then it should mark the previous step as an error occurred
            if ($request->edit_sub_status) {
                //            $lastLog = Orders::whereId($order->id)->with(['last_log'])->first();
                $lastLog = OrderLogs::whereOrderId($order->order_id)->orderBy('id', 'DESC')->first();
                if (!$lastLog) {
                    return response()->json([
                        'status' => 400,
                        'message' => 'No activity so far on this order'
                    ]);
                }
                if ($engravingId != $request->edit_status && $waitingId != $request->edit_status) {
                    $lastLog->error = 1;
                }
                $lastLog->save();
            }

            if (in_array($request->edit_status, OrderStatus::RemakeStatusIds)) {
                $workingDays = $this->countBusinessDays(
                    Carbon::parse($order->date_started),
                    Carbon::parse($order->deadline)
                );

                $newDeadline = $this->addBusinessDays(Carbon::now(), $workingDays);
                $order->date_started = Carbon::now()->format('Y-m-d');
                $order->deadline = $newDeadline->format('Y-m-d');
                $order->save();
            }

            $orderStatus = new OrderLogs();
            $orderStatus->order_id = $order->order_id;
            $orderStatus->status_id = $request->edit_status;
            $orderStatus->sub_status_id = $request->edit_sub_status ?? null;
            $orderStatus->user_id = Auth::user()->id;
            $orderStatus->notes = $request->notes ?? null;
            if ($engravingId != $request->edit_status && $waitingId != $request->edit_status) {
                $orderStatus->time_end = \Illuminate\Support\Carbon::now()->format('Y-m-d H:i:s');
            }
            $orderStatus->time_started = \Illuminate\Support\Carbon::now()->format('Y-m-d H:i:s');
            $orderStatus->save();

            $log = OrderLogs::whereId($orderStatus->id)->with(['user', 'status', 'updated_by'])->first();

            $notification = new Notifications();
            $notification->type = Notifications::typestatus;
            $notification->log_id = $log->id;
            $notification->save();

            $message = ['message' => 'A status was updated for order id ' . $request->order_id, 'log' => $log];
            event(new NewMessage($message));


            DB::commit();

            $this->sendIssueWithPrintEmail($order, $log->status->status_name);

            $response = [
                'status' => 200,
                'message' => 'Status updated successfully.',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 400,
                'message' => 'Something went wrong' . $e->getMessage()
            ]);
        }

        return response()->json($response);
    }

    private function countBusinessDays(Carbon $startDate, Carbon $endDate)
    {
        $currentDate = $startDate->copy();
        $days = 0;

        while ($currentDate->lt($endDate)) {
            $currentDate->addDay();
            if ($currentDate->isWeekday()) {
                $days++;
            }
        }

        return $days;
    }

    private function addBusinessDays(Carbon $startDate, int $daysToAdd)
    {
        $currentDate = $startDate->copy();

        while ($daysToAdd > 0) {
            $currentDate->addDay();
            if ($currentDate->isWeekday()) {
                $daysToAdd--;
            }
        }

        return $currentDate;
    }

    public function sendIssueWithPrintEmail($order, $status)
    {
        \Log::info('Sending email for order status ' . $status, ['order_id' => $order->id]);
        $template = EmailTemplates::where('status_id', $order->status_id)->where('status', 1)->first();
        if ($template) {
            $content = str_replace(
                ['{{ customer_name }}', '{{ order_number }}', '{{ support_email }}'],
                [$order->customer_name, $order->order_id, 'support@boudoir.com'],
                $template->content
            );
            $address = DB::table('costumer_address')->where('order_id', $order->id)->where('type', '=', 'billing_address')->first();
            Mail::to($address->email)->send(new TemplateEmail($template->subject, $content));

            \Log::info('Email sent successfully', ['order_id' => $order->id]);
        } else {
            \Log::error('No email template found for status ' . $status . '.');
        }
    }

    public function adminOverride(Request $request)
    {
        try {
            DB::beginTransaction();
            $order = Orders::find($request->order_id);
            $order->status_id = $request->override_status;
            $order->save();

            $lastLog = OrderLogs::whereOrderId($order->order_id)->whereNull('time_end')->orderBy('id', 'DESC')->first();
            if ($lastLog) {
                $lastLog->time_end = \Illuminate\Support\Carbon::now()->format('Y-m-d H:i:s');
                $lastLog->save();
            }

            $orderStatus = new OrderLogs();
            $orderStatus->order_id = $order->order_id;
            $orderStatus->status_id = $request->override_status;
            $orderStatus->user_id = Auth::user()->id;
            $orderStatus->notes = $request->notes ?? null;
            $orderStatus->time_started = \Illuminate\Support\Carbon::now()->format('Y-m-d H:i:s');
            $orderStatus->save();

            $log = OrderLogs::whereId($orderStatus->id)->with(['user', 'status', 'updated_by'])->first();

            $notification = new Notifications();
            $notification->type = Notifications::typestatus;
            $notification->log_id = $log->id;
            $notification->save();

            $message = ['message' => 'A status was updated for order id ' . $request->order_id, 'log' => $log];
            event(new NewMessage($message));

            $completed_status = OrderStatus::where('status_name', OrderStatus::COMPLETED)->pluck('id')->first();
            if ($completed_status == intval($request->override_status)) {
                syncToWooCommerce($order->order_id, 'completed');
                $orderStatus->time_end = \Illuminate\Support\Carbon::now()->format('Y-m-d H:i:s');
                $orderStatus->save();
            }

            DB::commit();

            $response = [
                'status' => 200,
                'message' => 'Status updated successfully.',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 400,
                'message' => 'Something went wrong' . $e->getMessage()
            ]);
        }

        return response()->json($response);
    }


    public function areas()
    {
        $readyForPrintStatusId = OrderStatus::where('status_name', 'Sent To Print')->pluck('id')->toArray();
        $onHoldStatusIds = OrderStatus::where('status_name', 'On hold')->pluck('id')->toArray();
        $readyToShipStatusId = OrderStatus::where('status_name', 'Ready to Ship')->pluck('id')->toArray();
        $qualityControlStatusId = OrderStatus::where('status_name', 'Quality Control')->pluck('id')->toArray();
        $productionReadyId = OrderStatus::where('status_name', 'Ready For Production')->pluck('id')->toArray();
        $excludedStatusIds = array_merge(
            $readyForPrintStatusId,
            $onHoldStatusIds,
            $readyToShipStatusId,
            $qualityControlStatusId,
            $productionReadyId
        );
        $workstations = OrderStatus::whereNotIn('id', $excludedStatusIds)->with(['first_log', 'last_log', 'logs'])->paginate(10);

        foreach ($workstations as $workstation) {
            $workstation->time_spent_minutes = 0; // Changed to minutes
            foreach ($workstation->logs as $log) {
                if ($log->time_started && $log->time_end) { // Only completed logs
                    $timeDiff = Carbon::parse($log->time_started)->diffInMinutes(Carbon::parse($log->time_end));
                    $workstation->time_spent_minutes += $timeDiff;
                }
            }
        }

        return view('admin.areas', compact('workstations'));
    }

    public function getWorkstationDetails($id, Request $request)
    {
        $sort = $request->get('sort');
        $now = Carbon::now();

        $query = OrderLogs::where('status_id', $id)
            ->whereNotNull('time_started')
            ->whereNotNull('time_end');

        if ($sort) {
            switch ($sort) {
                case 'day':
                    $query->whereDate('time_started', $now->toDateString());
                    break;

                case 'week':
                    $query->whereBetween('time_started', [
                        $now->copy()->startOfWeek(),
                        $now->copy()->endOfWeek(),
                    ]);
                    break;

                case 'month':
                    $query->whereMonth('time_started', $now->month)
                        ->whereYear('time_started', $now->year);
                    break;

                case 'year':
                    $query->whereYear('time_started', $now->year);
                    break;
            }
        }

        /**
         * IMPORTANT:
         * Group by order_id so each order is counted once per status
         */
        $orders = $query
            ->select(
                'order_id',
                DB::raw('SUM(TIMESTAMPDIFF(MINUTE, time_started, time_end)) as total_minutes')
            )
            ->groupBy('order_id')
            ->get();

        $ordersHtml = '';
        foreach ($orders as $order) {
            $timeFormatted = $this->formatMinutesToHours($order->total_minutes);

            $ordersHtml .= '
            <tr>
                <td>Order #' . $order->order_id . '</td>
                <td>' . $timeFormatted . '</td>
            </tr>';
        }

        return response()->json([
            // DISTINCT orders per status — MATCHES workstation counts
            'orderCount' => $orders->count(),

            // Sum of all order times — MATCHES workstation time
            'totalMinutes' => $orders->sum('total_minutes'),

            'ordersHtml' => $ordersHtml,
        ]);
    }

    public function team()
    {
        $roles = Roles::all();
        $statuses = OrderStatus::all();

        $users = User::with(['logs' => function ($query) {
            $query->whereNotNull('time_started')
                ->whereNotNull('time_end')
                ->select('id', 'user_id', 'order_id', 'time_started', 'time_end');
        }])->get();

        $teamMembers = $users->map(function ($user) {
            $totalMinutes = 0;

            // Group logs by order_id to get unique orders
            $logsByOrder = $user->logs->groupBy('order_id');
            $uniqueOrders = $logsByOrder->keys();

            foreach ($user->logs as $log) {
                if ($log->time_started && $log->time_end) {
                    // Use calculateWorkingMinutes helper
                    $totalMinutes += calculateWorkingMinutes($log->time_started, $log->time_end);
                }
            }

            return (object)[
                'id' => $user->id,
                'name' => $user->name,
                'product_status_id' => $user->product_status_id,
                'order_count' => $uniqueOrders->count(),
                'total_minutes' => $totalMinutes,
            ];
        })->filter(function ($member) {
            return $member->order_count > 0;
        })->values();

        return view('admin.team', compact(['teamMembers', 'roles', 'statuses']));
    }

    public function getTeamDetails($id, Request $request)
    {
        $sort = $request->get('sort');
        $now = Carbon::now();

        $query = OrderLogs::where('user_id', $id)
            ->whereNotNull('time_started')
            ->whereNotNull('time_end');

        if ($sort) {
            switch ($sort) {
                case 'day':
                    $startDate = Carbon::today();
                    $endDate = Carbon::today();
                    $query->whereBetween(DB::raw('DATE(time_started)'), [$startDate, $endDate]);
                    break;

                case 'week':
                    $startDate = Carbon::now()->startOfWeek();
                    $endDate = Carbon::now()->endOfWeek();
                    $query->whereBetween(DB::raw('DATE(time_started)'), [$startDate, $endDate]);
                    break;

                case 'month':
                    $startDate = Carbon::now()->startOfMonth();
                    $endDate = Carbon::now()->endOfMonth();
                    $query->whereBetween(DB::raw('DATE(time_started)'), [$startDate, $endDate]);
                    break;

                case 'year':
                    $startDate = Carbon::now()->startOfYear();
                    $endDate = Carbon::now()->endOfYear();
                    $query->whereBetween(DB::raw('DATE(time_started)'), [$startDate, $endDate]);
                    break;
            }
        }

        $logs = $query->get();

        // Group by order_id and calculate working minutes
        $ordersData = $logs->groupBy('order_id')->map(function ($orderLogs) {
            $orderId = $orderLogs->first()->order_id;
            $totalMinutes = 0;

            foreach ($orderLogs as $log) {
                if ($log->time_started && $log->time_end) {
                    // Use calculateWorkingMinutes helper
                    $totalMinutes += calculateWorkingMinutes($log->time_started, $log->time_end);
                }
            }

            return [
                'order_id' => $orderId,
                'total_minutes' => $totalMinutes,
            ];
        })->values();

        $ordersHtml = '';
        foreach ($ordersData as $order) {
            $timeFormatted = $this->formatMinutesToHours($order['total_minutes']);

            $ordersHtml .= '
        <tr>
            <td>Order #' . $order['order_id'] . '</td>
            <td>' . $timeFormatted . '</td>
        </tr>';
        }

        return response()->json([
            'ordersHtml' => $ordersHtml,
            'orderCount' => $ordersData->count(),
            'totalMinutes' => $ordersData->sum('total_minutes'),
        ]);
    }
//    public function getTeamDetails($id, Request $request)
//    {
//        $sort = $request->get('sort');
//
//        $query = OrderLogs::select(
//            'order_id',
//            DB::raw('SUM(TIMESTAMPDIFF(MINUTE, time_started, time_end)) AS total_minutes')
//        )
//            ->where('user_id', $id)
//            ->whereNotNull('time_started')
//            ->whereNotNull('time_end');
//
//        // Only apply date filter if sort parameter is provided
//        if ($sort) {
//            $now = Carbon::now();
//            if ($sort == 'day') {
//                $startDate = Carbon::today();
//                $endDate = Carbon::today();
//            } elseif ($sort == 'week') {
//                $startDate = Carbon::now()->startOfWeek();
//                $endDate = Carbon::now()->endOfWeek();
//            } elseif ($sort == 'month') {
//                $startDate = Carbon::now()->startOfMonth();
//                $endDate = Carbon::now()->endOfMonth();
//            } elseif ($sort == 'year') {
//                $startDate = Carbon::now()->startOfYear();
//                $endDate = Carbon::now()->endOfYear();
//            }
//
//            if (isset($startDate) && isset($endDate)) {
//                $query->whereBetween(DB::raw('DATE(time_started)'), [$startDate, $endDate]);
//            }
//        }
//
//        $teamMembersResult = $query->groupBy('order_id')->get();
//
//        if (!$teamMembersResult) {
//            return response()->json(['message' => 'Team member not found'], 404);
//        }
//
//        $ordersHtml = '';
//        foreach ($teamMembersResult as $member) {
//            $timeFormatted = $this->formatMinutesToHours($member->total_minutes);
//
//            $ordersHtml .= '
//        <tr>
//            <td>Order #' . $member->order_id . '</td>
//            <td>' . $timeFormatted . '</td>
//        </tr>';
//        }
//
//        return response()->json([
//            'ordersHtml' => $ordersHtml,
//            'orderCount' => count($teamMembersResult)
//        ]);
//    }
    private function formatMinutesToHours($minutes)
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        if ($hours > 0 && $mins > 0) {
            return $hours . 'h ' . $mins . 'm';
        } elseif ($hours > 0) {
            return $hours . 'h';
        } else {
            return $mins . 'm';
        }
    }

    private function calculateTimeSpent($workstationIds)
    {

        $totalTime = Orders::whereIn('workstation_id', $workstationIds)
            ->sum(DB::raw('TIMESTAMPDIFF(SECOND, date_started, NOW())'));

        return gmdate('H:i:s', $totalTime);
    }

    public function notification()
    {
        $data['notifications'] = Notifications::with(['log', 'log.user', 'log.status', 'log.sub_status', 'comment', 'comment.user', 'comment.order'])
            ->orderBy('created_at', 'DESC')
            ->paginate(10);

        if (request()->ajax()) {
            return view('admin.partials.notification_rows', $data)->render();
        }

        return view('admin.notifications', $data);
    }

    public function manageStatuses()
    {
        $data['statuses'] = OrderStatus::all();
        return view('admin.manage-statuses', $data);
    }

    public function productFlows()
    {
        $data['flows'] = Product::with('orderStatuses')->get();
        $data['availableSteps'] = OrderStatus::all();
        return view('admin.manage-product-flows', $data);
    }

    public function addProductFlow(Request $request)
    {
        // Fetch the product
        $product = Product::findOrFail($request->product_id);

        // Validate that the steps are selected
        $request->validate([
            'steps' => 'required|array',
            'steps.*' => 'exists:order_status,id',
        ]);

        // Attach the selected steps to the product
        foreach ($request->steps as $stepId) {
            $stepNo = $product->orderStatuses()->count() + 1; // Get the next step number
            $product->orderStatuses()->attach($stepId, ['step_no' => $stepNo]);
        }
        $response = [
            'status' => 200,
            'message' => 'Flow added successfully.',
        ];

        return response()->json($response);
    }

    public function addStatuses(Request $request)
    {
        $request->validate([
            'status-name' => 'required|string|max:255',
            'status-color' => 'required|string|max:10',
        ]);

        $status = new OrderStatus();
        $status->status_name = $request->input('status-name');
        $status->status_color = $request->input('status-color');
        $status->title = str_replace(" ", '-', $request->input('status-name'));
        $status->save();

        $response = [
            'status' => 200,
            'message' => 'Status added successfully.',
        ];

        return response()->json($response);
    }

    public function updateStatus(Request $request)
    {
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

    public function update_worker_station(Request $request)
    {
        $workerId = $request->input('active_user_id');
        $statusIds = $request->input('status_ids');
        if (empty($statusIds)) {
            $response = [
                'status' => 203,
                'message' => 'Please select at least one status to assign.'
            ];
            return response()->json($response);
        }

        $worker = User::find($workerId);
        $worker->workstations()->delete();
        foreach ($statusIds as $statusId) {
            $worker->workstations()->create([
                'status_id' => $statusId
            ]);
        }

        $response = [
            'status' => 200,
            'message' => 'Status updated successfully.'
        ];

        return response()->json($response);
    }

    public function getUserWorkstations($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'status' => 404,
                'message' => 'User not found.'
            ]);
        }

        // Assuming the user has many workstations
        $workstations = $user->workstations()->with('status')->get();

        // Return the status_ids (the user’s workstations)
        return response()->json([
            'status' => 200,
            'data' => $workstations
        ]);
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
            'rush_orders' => Orders::with(['status', 'station', 'station.worker'])
                ->where('is_rush', '=', 1)
                ->where('status_id', '!=', $completed_status)->get(),
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
            ->header('Content-Disposition', 'inline; filename="' . strtolower($status_name) . '_barcode.jpg"');
    }

//    public function import(Request $request)
//    {
//        $file = $request->file('excel_file');
//
//        if (!$file) {
//            return redirect()->back()->with('error', 'Please upload an Excel file.');
//        }
//
//        // Read the Excel file
//        $sheets = Excel::toArray([], $file);
//        $sheetNames = [
//            "New Leathers & Smooth Velvets",
//            "Luxury Swatch Box",
//            "USB",
//            "Ice Cubes",
//            "Metal Print",
//            "Presentation Boxes",
//            "Folios",
//            "The Album",
//            "Journals",
//            "Couture Box",
//            "GEM Journal Album",
//            "Prints",
//            "Matted Prints",
//            "Mats",
//            "Leather Envelopes",
//            "Fine Art Prints",
//        ];
//
//
//        // Current timestamp for created_at and updated_at columns
//        $currentTimestamp = Carbon::now();
//
//        foreach ($sheets as $sheetName => $rows) {
//            // 1. Get the Product ID using the sheet name (product name)
//            $product = Product::where('name', $sheetNames[$sheetName])->first();
//
//            if (!$product) {
//                continue; // Skip this sheet if no product is found
//            }
//
//            // 2. Insert Option Name (Option name) into product_attributes table
//            foreach ($rows as $index => $row) {
//                // Skip the first iteration
//                if ($index === 0) {
//                    continue; // Skip the first row
//                }
//
//                $optionName = $row[0];
//                $optionTitle = $row[1];
//
//                // Insert Option Name (Option name) into product_attributes table
//                $existingId = ProductAttributes::where('name', $optionName)->where('product_id', $product->id)->pluck('id')->first();
//                if(!$existingId){
//                    $attribute = ProductAttributes::create([
//                        'name' => $optionName,
//                        'product_id' => $product->id
//                    ]);
//                    $existingId = $attribute->id;
//                }
//                // Insert Option Title (Option title) into product_attribute_values table
//                ProductAttributeValues::create([
//                    'attribute_id' => $existingId,
//                    'value' => $optionTitle,
//                    'price_cad' => null, // You can add logic to handle price if needed
//                    'price_usd' => null
//                ]);
//            }
//
//        }
//
//        return redirect()->back()->with('success', 'Product attributes imported successfully.');
//    }

    public function downloadTodaysReport(Request $request)
    {
        $completed_status = OrderStatus::where('status_name', Orders::statusCompleted)->pluck('id')->first();
        $remakeStatusId = OrderStatus::where('status_name', 'Remake + Reasons')->pluck('id')->first();

        try {
            $orders = Orders::with(['status', 'last_log', 'items.attributes', 'activeChildren'])
                ->where('orderType', \App\Models\Orders::parentType)
                ->where('status_id', '!=', $completed_status)
                ->withExists(['logs as has_remake' => function ($query) use ($remakeStatusId) {
                    $query->where('status_id', $remakeStatusId);
                }])
                ->get();

            $reportData = [];
            $now = \Carbon\Carbon::now();

            foreach ($orders as $order) {
                $daysInProduction = 0;
                if ($order->date_started) {
                    $startDate = Carbon::parse($order->date_started);
                    $daysInProduction = Carbon::parse($startDate)->diffInWeekdays($now);
                }

                $totalProductionDays = Carbon::parse($order->date_started)->diffInWeekdays($order->deadline);
                $highlight = $totalProductionDays > 0 && $daysInProduction > $totalProductionDays;

                $reportData[] = [
                    'order_number' => $order->order_id,
                    'status' => $order->last_log?->status?->status_name ?? $order->status?->status_name ?? 'N/A',
                    'days_in_production' => $daysInProduction,
                    'expected_production_days' => $totalProductionDays,
                    'highlight' => $highlight ? 'YES' : 'NO',
                    'deadline' => $order->deadline,
                    'is_rush' => $order->is_rush ? 'YES' : 'NO',
                ];

                if (isset($order->activeChildren) && count($order->activeChildren)) {
                    foreach ($order->activeChildren as $child_order) {
                        $daysInProduction = 0;
                        if ($child_order->date_started) {
                            $startDate = Carbon::parse($child_order->date_started);
                            $daysInProduction = Carbon::parse($startDate)->diffInWeekdays($now);
                        }

                        $totalProductionDays = Carbon::parse($child_order->date_started)->diffInWeekdays($child_order->deadline);
                        $highlight = $totalProductionDays > 0 && $daysInProduction > ($totalProductionDays * 1.2);

                        $reportData[] = [
                            'order_number' => $child_order->order_id,
                            'status' => $child_order->last_log?->status?->status_name ?? $child_order->status?->status_name ?? 'N/A',
                            'days_in_production' => $daysInProduction,
                            'expected_production_days' => $totalProductionDays,
                            'highlight' => $highlight ? 'YES' : 'NO',
                            'deadline' => $child_order->deadline,
                            'is_rush' => $child_order->is_rush ? 'YES' : 'NO',
                        ];
                    }
                }
            }

            return Excel::download(new ProductionReportExport($reportData), 'production_report_' . date('Y-m-d_His') . '.xlsx');

        } catch (\Exception $e) {
            Log::error('Failed to generate production report: ' . $e->getMessage());
            return response()->json(['Error' => $e->getMessage()], 500);
        }
    }
}
