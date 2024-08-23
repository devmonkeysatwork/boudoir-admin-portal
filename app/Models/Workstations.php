<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workstations extends Model
{
    use HasFactory;

    function worker(){
        return $this->belongsTo(User::class,'assigned_to_id','id');
    }

    protected $casts = [
        'order_count' => 'integer', // Define order_count as an integer
    ];

    public function orders()
    {
        return $this->hasMany(Orders::class, 'workstation_id', 'id');
    }

    public function getOrderCount()
    {
        return $this->orders()->count();
    }
    public function activeOrder()
    {
        return $this->orders()->where('status_id', 1)->pluck('order_id')->first();
    }

    public function getTimeInProductionAttribute()
    {
        // Assuming you have a relationship between workstation and orders
        $totalTime = $this->orders->reduce(function ($carry, $order) {
            $start = \Carbon\Carbon::parse($order->created_at);
            $end = \Carbon\Carbon::now(); // Or the time the order was completed
            $timeSpent = $start->diffInMinutes($end);
            return $carry + $timeSpent;
        }, 0);

        return gmdate('H:i:s', $totalTime * 60); // Convert to H:i:s format
    }

}
