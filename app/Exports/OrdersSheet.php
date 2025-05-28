<?php

namespace App\Exports;

use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrdersSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    protected $orders;
    protected $title;

    public function __construct($orders, $title)
    {
        $this->orders = $orders;
        $this->title = $title;
    }

    public function collection()
    {
        return $this->orders->map(function ($order) {
            $remarks = '';

            // Calculate remarks based on deadline
            if (isset($order->deadline)) {
                $deadline = Carbon::parse($order->deadline);
                $now = Carbon::now();

                if ($now->gte($deadline)) {
                    $remarks = 'Order is late';
                } elseif ($now->gte($deadline->subDays(2))) {
                    $hoursLeft = round($now->diffInHours($deadline), 0);
                    $remarks = "Order is due in {$hoursLeft} hrs";
                }
            }

            return [
                'order_id' => $order->order_id,
                'status' => $order->status?->status_name,
                'team_member' => $order->station?->worker?->name,
                'time_in_production' => $order->date_started ? Carbon::parse($order->date_started)->diffForHumans() : '',
                'deadline' => $order->deadline ? Carbon::parse($order->deadline)->format('Y-m-d H:i:s') : '',
                'remarks' => $remarks,
                'is_rush' => $order->is_rush ? 'Yes' : 'No',
                'created_at' => $order->created_at ? $order->created_at->format('Y-m-d H:i:s') : '',
                'updated_at' => $order->updated_at ? $order->updated_at->format('Y-m-d H:i:s') : '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Order ID',
            'Status',
            'Team Member',
            'Time in Production',
            'Deadline',
            'Remarks',
            'Is Rush',
            'Created At',
            'Updated At',
        ];
    }

    public function title(): string
    {
        return $this->title;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'E0E0E0']],'font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // Order ID
            'B' => 20, // Status
            'C' => 20, // Team Member
            'D' => 25, // Time in Production
            'E' => 20, // Deadline
            'F' => 30, // Remarks
            'G' => 12, // Is Rush
            'H' => 20, // Created At
            'I' => 20, // Updated At
        ];
    }
}
