<?php

namespace App\Console\Commands;

use App\Exports\DailySummaryExport;
use App\Models\Orders;
use App\Models\OrderStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class SendDailySummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-daily-summary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily summary to admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $completed_status = OrderStatus::where('status_name', Orders::statusCompleted)->pluck('id')->first();
            $hold_status = OrderStatus::where('status_name', Orders::statusHold)->pluck('id')->first();
            $issues = OrderStatus::whereIn('status_name', OrderStatus::adminStatuses)->pluck('id');

            $data = [
                'rush_orders' => Orders::with(['status', 'station', 'station.worker'])
                    ->where('is_rush', '=', 1)
                    ->where('status_id', '!=', $completed_status)->get(),
                'production_orders' => Orders::with(['activeChildren','status', 'station', 'station.worker'])
                    ->where('orderType','=',Orders::parentType)
                    ->where('status_id', '!=', $completed_status)->get(),
                'orders_on_hold' => Orders::with(['status', 'station', 'station.worker'])
                    ->where('status_id', $hold_status)->get(),
                'orders_with_issues' => Orders::with(['status', 'station', 'station.worker'])
                    ->whereIn('status_id', $issues)->get(),
            ];

            $fileName = 'daily_summary_' . Carbon::now()->format('Y_m_d') . '.xlsx';
//            $filePath = storage_path('app/temp/' . $fileName);
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }
            Excel::store(new DailySummaryExport($data), 'temp/' . $fileName);

            $productionOrdersCount = $data['production_orders']->sum(function($order) {
                return 1 + ($order->activeChildren ? $order->activeChildren->count() : 0);
            });
            // Send email with Excel attachment
            $mailData = [
                'title' => 'Daily Summary Report',
                'date' => Carbon::now()->format('Y-m-d'),
                'summary' => [
                    'rush_orders_count' => $data['rush_orders']->count(),
                    'production_orders_count' => $productionOrdersCount,
                    'orders_on_hold_count' => $data['orders_on_hold']->count(),
                    'orders_with_issues_count' => $data['orders_with_issues']->count(),
                ]
            ];

            $adminEmails = \App\Models\User::where('role_id', 1)->pluck('email')->toArray();

            if (!empty($adminEmails)) {
                Mail::to('touseefktk22@gmail.com')
                    ->cc([env('SUPPORT_EMAIL')])
                    ->send(new \App\Mail\OrderSummaryEmail($mailData, storage_path('app/temp/' . $fileName)));
            }

            // Clean up the file after sending
            Storage::delete('app/temp/' . $fileName);

            Log::info('Daily Summary Excel report sent for ' . Carbon::now()->format('Y-m-d'));

        } catch (\Exception $e) {
            Log::error('Error while sending daily summary: ' . $e->getMessage());
        }
    }
}
