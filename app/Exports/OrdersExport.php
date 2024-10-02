<?php

namespace App\Exports;

use App\Models\Orders;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersExport implements FromCollection, WithHeadings
{
    protected $filterProduct;
    protected $filterDate;
    protected $filterStatus;
    protected $filterPriority;

    public function __construct($filterProduct, $filterDate, $filterStatus, $filterPriority)
    {
        $this->filterProduct = $filterProduct;
        $this->filterDate = $filterDate;
        $this->filterStatus = $filterStatus;
        $this->filterPriority = $filterPriority;
    }
    public function collection()
    {
        $query = Orders::with(['status', 'last_log', 'last_log.status', 'last_log.sub_status', 'station', 'station.worker'])
            ->where('orderType', '=', Orders::parentType);

        // Apply filters
        if ($this->filterProduct) {
            $query->whereHas('items', function ($q) {
                $q->where('product_name', $this->filterProduct);
            });
        }

        if ($this->filterPriority) {
            $query->where('is_rush', $this->filterPriority);
        }

        if ($this->filterStatus) {
            $query->where('status_id', $this->filterStatus);
        }

        // Sort orders based on date filter
        if ($this->filterDate) {
            if ($this->filterDate == 'oldest') {
                $query->orderBy(\Illuminate\Support\Facades\DB::raw('DATE(date_started)'), 'ASC');
            } elseif ($this->filterDate == 'newest') {
                $query->orderBy(\Illuminate\Support\Facades\DB::raw('DATE(date_started)'), 'DESC');
            }
        } else {
            $query->orderBy('is_rush', 'DESC')
                ->orderBy(\Illuminate\Support\Facades\DB::raw('DATE(deadline)'), 'DESC')
                ->orderBy(\Illuminate\Support\Facades\DB::raw('DATE(date_started)'), 'DESC');
        }

        return $query->get()->map(function ($order) {
            return [
                $order->order_id,
                $order->is_rush ? 'Rush' : '-',
                $order->last_log->sub_status->name ?? $order->last_log->status->status_name ?? $order->status->status_name ?? null,
                $order->last_log->user->name ?? $order->station->worker->name ?? null,
                $order->date_started,
                $this->calculateWorkingTime($order->created_at),
                $this->getLateStatus($order)
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Order #',
            'Priority',
            'Phase',
            'Team Member',
            'Date Started',
            'Time in Production',
            'Late',
        ];
    }

    private function getLateStatus($order)
    {
        if (isset($order->deadline) && \Carbon\Carbon::now()->gte(\Carbon\Carbon::parse($order->deadline))) {
            return 'Late';
        } elseif (isset($order->deadline) && \Carbon\Carbon::now()->gte(\Carbon\Carbon::parse($order->deadline)->subDays(2))) {
            $hoursLeft = round(\Carbon\Carbon::now()->diffInHours(\Carbon\Carbon::parse($order->deadline)), 0);
            return "$hoursLeft hours left";
        } else {
            return '-';
        }
    }
    function calculateWorkingTime($created_at){
        $dateStarted = \Carbon\Carbon::parse($created_at);
        $now = \Carbon\Carbon::now();
        $workingTime =  calculateWorkingTime($dateStarted,$now);
        $time = $workingTime['months'] > 0 ? $workingTime['months'] . 'm ' : '';
        $time .= $workingTime['days'] > 0 ? $workingTime['days'] . 'd ' : '';
        $time .= $workingTime['hours'] > 0 ? $workingTime['hours'] . 'h ' : '';
        $time .= $workingTime['minutes'] > 0 ? $workingTime['minutes'] . 'm' : '';
        return $time;
    }
}
