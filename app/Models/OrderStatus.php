<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    use HasFactory;
    protected $table = 'order_status';
    const adminStatuses = ['On hold','Issue with print','Remake + Reasons'];
    const COMPLETED = 'Completed';


    function sub_status(){
        return $this->hasMany(SubStatus::class,'status_id','id');
    }

    public function orders()
    {
        return $this->hasMany(Orders::class, 'status_id', 'id');
    }

    public function orders_count()
    {
        return $this->hasMany(OrderLogs::class, 'status_id', 'id')->select('order_id')->distinct()->count('order_id');
    }

    public function workstation()
    {
        return $this->belongsTo(Workstations::class, 'id', 'id');
    }

    function logs(){
        return $this->hasMany(OrderLogs::class, 'status_id', 'id');
    }

    function first_log(){
        return $this->hasOne(OrderLogs::class, 'status_id', 'id')
            ->oldest('time_started');
    }
    function last_log(){
        return $this->hasOne(OrderLogs::class, 'status_id', 'id')
            ->latest('time_started');
    }
}
