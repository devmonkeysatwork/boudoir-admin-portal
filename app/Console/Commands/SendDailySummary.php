<?php

namespace App\Console\Commands;

use App\Models\Orders;
use App\Models\OrderStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
        try{
            $completed_status = OrderStatus::where('status_name',Orders::statusCompleted)->pluck('id')->first();
            $hold_status = OrderStatus::where('status_name',Orders::statusHold)->pluck('id')->first();
            $issues = OrderStatus::whereIn('status_name',OrderStatus::adminStatuses)->pluck('id');

            $mailData['rush_orders'] = Orders::with(['status','station','station.worker'])
                ->where('is_rush','=',1)
                ->where('status_id','!=',$completed_status)->get();
            $mailData['production_order'] = Orders::with(['status','station','station.worker'])
                ->where('status_id','!=',$completed_status)->get();
            $mailData['orders_on_hold'] = Orders::with(['status','station','station.worker'])
                ->where('status_id',$hold_status)->get();
            $mailData['order_with_issues'] = Orders::with(['status','station','station.worker'])
                ->whereIn('status_id',$issues)->get();

            $mailData['title']='Daily Summary Report';
            Mail::to(env('ADMIN_EMAIL'))->send(new \App\Mail\OrderSummaryEmail($mailData));
            Log::info('Daily Summary Sent for ' . Carbon::now()->format('Y-m-d'));
        }catch (\Exception $e){
            Log::error('Error while sending daily summary' . $e->getMessage());
        }
    }
}
