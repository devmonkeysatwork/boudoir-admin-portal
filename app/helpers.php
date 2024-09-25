<?php

use Carbon\Carbon;

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

if (! function_exists('calculateWorkingTime')) {
    function calculateWorkingTime($startDate, $endDate)
    {
        $currentDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        $totalDays = 0;
        $totalHours = 0;
        $totalMinutes = 0;

        while ($currentDate->lessThanOrEqualTo($endDate)) {
            // Check if the current date is a weekday (Monday to Friday)
            if ($currentDate->isWeekday()) {
                $totalDays++;
                // Assuming an 8-hour workday (adjust as needed)
                $totalHours += 8;
            }
            $currentDate->addDay();
        }

        // Convert total hours and days into a more detailed format
        $months = floor($totalDays / 30);
        $days = $totalDays % 30;
        $hours = $totalHours % 24;
        $minutes = 0; // If you want to track minutes as well
        return [
            'months' => $months,
            'days' => $days,
            'hours' => $hours,
            'minutes' => $minutes,
        ];
    }
}
