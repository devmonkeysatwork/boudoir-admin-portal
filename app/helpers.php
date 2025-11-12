<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

if (! function_exists('formatDuration')) {
    function formatDuration($startTime, $endTime)
    {
        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);

        $diff = $end->diff($start);

        $result = [];

        if ($diff->y) {
            $result[] = "{$diff->y}y";
        }
        if ($diff->m) {
            $result[] = "{$diff->m}mo";
        }
        if ($diff->d) {
            $result[] = "{$diff->d}d";
        }
        if ($diff->h) {
            $result[] = "{$diff->h}h";
        }
        if ($diff->i) {
            $result[] = "{$diff->i}m";
        }

        return implode(' ', $result);
    }
}

//if (! function_exists('calculateWorkingTime')) {
//    function calculateWorkingTime($startDate, $endDate)
//    {
//        $currentDate = Carbon::parse($startDate);
//        $endDate = Carbon::parse($endDate);
//
//        $totalDays = 0;
//        $totalHours = 0;
//        $totalMinutes = 0;
//
//        while ($currentDate->lessThanOrEqualTo($endDate)) {
//            // Check if the current date is a weekday (Monday to Friday)
//            if ($currentDate->isWeekday()) {
//                $totalDays++;
//                // Assuming an 8-hour workday (adjust as needed)
//                $totalHours += 8;
//            }
//            $currentDate->addDay();
//        }
//
//        // Convert total hours and days into a more detailed format
//        $months = floor($totalDays / 30);
//        $days = $totalDays % 30;
//        $hours = $totalHours % 24;
//        $minutes = 0; // If you want to track minutes as well
//        return [
//            'months' => $months,
//            'days' => $days,
//            'hours' => $hours,
//            'minutes' => $minutes,
//        ];
//    }
//}



function calculateTime($startDate, $endDate)
{
    $start = Carbon::parse($startDate);
    $end = Carbon::parse($endDate);

    // If the start date is after the end date, return zero difference
    if ($start->greaterThan($end)) {
        return [
            'months' => 0,
            'days' => 0,
            'hours' => 0,
            'minutes' => 0,
        ];
    }

    // Calculate the difference
    $diffInMinutes = $start->diffInMinutes($end);
    $totalHours = floor($diffInMinutes / 60);
    $totalDays = floor($totalHours / 24);

    // Convert total time into months, days, hours, and minutes
    $months = floor($totalDays / 30);
    $days = $totalDays % 30;
    $hours = $totalHours % 24;
    $minutes = $diffInMinutes % 60;
    if($hours>17 && $days > 0){
        $days++;
        $hours=0;
    }
    return [
        'months' => $months,
        'days' => $days,
        'hours' => $hours,
        'minutes' => $minutes,
    ];
}

function calculateWorkingTime($startDate, $endDate, $orderId = null, $waitingId = null, $has_remake = false)
{
//    \Illuminate\Support\Facades\Log::info($startDate .' : '. $endDate);
    $start = Carbon::parse($startDate);
    if($has_remake && $orderId){
        $remakeLog = \App\Models\OrderLogs::whereIn('status_id',\App\Models\OrderStatus::RemakeStatusIds)
            ->where('order_id', $orderId)
            ->orderBy('time_started', 'DESC')
            ->first();

        if($remakeLog){
            $nextLog = \App\Models\OrderLogs::where('order_id', $orderId)
                ->where('time_started', '>', $remakeLog->time_started)
                ->orderBy('time_started', 'ASC')
                ->first();

            if($nextLog && $nextLog->time_started){
                $start = Carbon::parse($nextLog->time_started);
            }
        }
    }
    $end = Carbon::parse($endDate);

    // If the start date is after the end date, return zero difference
    if ($start->greaterThan($end)) {
        return [
            'months' => 0,
            'days' => 0,
            'hours' => 0,
            'minutes' => 0,
        ];
    }

    $totalMinutes = 0;
    $fullDays = 0;

    $currentDate = $start->copy()->startOfDay();
    $endDate = $end->copy()->startOfDay();

    // Process each day
    while ($currentDate->lessThanOrEqualTo($endDate)) {
        // Only process weekdays
        if ($currentDate->isWeekday()) {
            $workStart = $currentDate->copy()->setTime(9, 0);
            $workEnd = $currentDate->copy()->setTime(17, 0);

            // Determine actual start time for this day
            if ($currentDate->isSameDay($start)) {
                $actualStart = $start->greaterThan($workStart) ? $start : $workStart;
            } else {
                $actualStart = $workStart;
            }

            // Determine actual end time for this day
            if ($currentDate->isSameDay($end)) {
                $actualEnd = $end->lessThan($workEnd) ? $end : $workEnd;
            } else {
                $actualEnd = $workEnd;
            }

            // Calculate minutes for this day if there's actual working time
            if ($actualStart->lessThan($actualEnd)) {
                $dailyMinutes = $actualStart->diffInMinutes($actualEnd);

                // Check if this constitutes a full working day (8 hours = 480 minutes)
                if ($dailyMinutes >= 480) {
                    $fullDays++;
                } else {
                    $totalMinutes += $dailyMinutes;
                }
            }
        }

        $currentDate->addDay();
    }


    // If an order was in waiting
    // Calculate waiting time to subtract if orderId is provided
    if ($orderId) {
        $waitingLogs = \App\Models\OrderLogs::select('time_started', 'time_end')
            ->where('order_id', $orderId)
            ->where('status_id', $waitingId)
            ->whereNotNull('time_started')
            ->whereNotNull('time_end')
            ->get();

        if(count($waitingLogs)){
            $totalWaitingMinutes = 0;
            $waitingFullDays = 0;

            foreach ($waitingLogs as $log) {
                $waitingTime = calculateWorkingTimeInternal($log->time_started, $log->time_end);
//                \Illuminate\Support\Facades\Log::info($waitingTime);
                $waitingFullDays += $waitingTime['full_days'];
                $totalWaitingMinutes += $waitingTime['total_minutes'];
            }

            // Subtract waiting time from total time
            $fullDays = max(0, $fullDays - $waitingFullDays);
            $totalMinutes = max(0, $totalMinutes - $totalWaitingMinutes);

            // Handle negative minutes by borrowing from full days
            while ($totalMinutes < 0 && $fullDays > 0) {
                $fullDays--;
                $totalMinutes += 480; // Add 8 hours worth of minutes
            }

            // Ensure we don't go negative
            $totalMinutes = max(0, $totalMinutes);
            $fullDays = max(0, $fullDays);
        }
    }



    // Convert remaining minutes to hours and minutes
    $hours = floor($totalMinutes / 60);
    $minutes = $totalMinutes % 60;

    // Convert full days to months and remaining days
    $months = floor($fullDays / 30); // Assuming 30 working days per month
    $days = $fullDays % 30;

    return [
        'months' => $months,
        'days' => $days,
        'hours' => $hours,
        'minutes' => $minutes,
    ];
}



// Helper function to calculate working time without subtraction
function calculateWorkingTimeInternal($startDate, $endDate)
{
    $start = Carbon::parse($startDate);
    $end = Carbon::parse($endDate);

    if ($start->greaterThan($end)) {
        return ['full_days' => 0, 'total_minutes' => 0];
    }

    $totalMinutes = 0;
    $fullDays = 0;

    $currentDate = $start->copy()->startOfDay();
    $endDate = $end->copy()->startOfDay();

    while ($currentDate->lessThanOrEqualTo($endDate)) {
        if ($currentDate->isWeekday()) {
            $workStart = $currentDate->copy()->setTime(9, 0);
            $workEnd = $currentDate->copy()->setTime(17, 0);

            if ($currentDate->isSameDay($start)) {
                $actualStart = $start->greaterThan($workStart) ? $start : $workStart;
            } else {
                $actualStart = $workStart;
            }

            if ($currentDate->isSameDay($end)) {
                $actualEnd = $end->lessThan($workEnd) ? $end : $workEnd;
            } else {
                $actualEnd = $workEnd;
            }

            if ($actualStart->lessThan($actualEnd)) {
                $dailyMinutes = $actualStart->diffInMinutes($actualEnd);

                if ($dailyMinutes >= 480) {
                    $fullDays++;
                } else {
                    $totalMinutes += $dailyMinutes;
                }
            }
        }

        $currentDate->addDay();
    }

    return ['full_days' => $fullDays, 'total_minutes' => $totalMinutes];
}




function calculateWorkingHoursLate($deadlineDate, $currentDate = null)
{
    if (!$currentDate) {
        $currentDate = Carbon::now();
    }

    $deadline = Carbon::parse($deadlineDate)->endOfDay();
    $current = Carbon::parse($currentDate);

    // If not actually late, return 0
    if ($current->lessThan($deadline)) {
        return 0;
    }

    // Use existing calculateWorkingTime function
    $workingTime = calculateWorkingTime($deadline, $current, null, null);

    // Convert to total hours
    $totalHours = ($workingTime['months'] * 30 * 8) +
        ($workingTime['days'] * 8) +
        $workingTime['hours'] +
        round($workingTime['minutes'] / 60, 1);

    return $totalHours;
}


function syncToWooCommerce($orderNumber, $status)
{
    try {
        $woocommerce_url = env('WOOCOMMERCE_URL');
        $payload = [
            'order_number' => $orderNumber,
            'status' => $status,
        ];

        $payloadJson = json_encode($payload);
        $secret = env('WOOCOMMERCE_SIGNATURE_KEY');

        $signature = 'sha256=' . hash_hmac('sha256', $payloadJson, $secret);

        $response = Http::withHeaders([
            'X-Webhook-Signature' => $signature,
        ])->withOptions(['verify' => app()->environment('local') ? false : true,])
            ->post($woocommerce_url, $payload);
        if ($response->successful()) {
            Log::info('Order synced to WooCommerce', ['order' => $orderNumber]);
        } else {
            Log::error('Failed to sync to WooCommerce', [
                'order' => $orderNumber,
                'response' => $response->body()
            ]);
        }
    } catch (\Exception $e) {
        Log::error('WooCommerce sync error', [
            'order' => $orderNumber,
            'error' => $e->getMessage()
        ]);
    }
}
