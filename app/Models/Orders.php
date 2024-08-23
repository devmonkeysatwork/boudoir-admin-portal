<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;

    const parentType = 'PARENT';
    const singleType = 'SINGLE';
    const childType = 'CHILD';
    const statusCompleted = 'Completed';
    const statusHold = 'On hold';



    function children(){
        return $this->hasMany(Orders::class,'parentOrder','id');
    }

    function items(){
        return $this->hasMany(OrderItems::class,'order_id','id');
    }
    function station(){
        return $this->belongsTo(Workstations::class,'workstation_id','id');
    }
    function status(){
        return $this->belongsTo(OrderStatus::class,'status_id','id');
    }
    function addresses(){
        return $this->hasMany(CostumerAddress::class,'order_id','id');
    }
    function logs(){
        return $this->hasMany(OrderLogs::class,'order_id','order_id');
    }
    function last_log(){
        return $this->hasOne(OrderLogs::class, 'order_id', 'order_id')
            ->latest('time_started');
    }

    public function comments()
    {
        return $this->hasMany(OrderComments::class,'order_id','id')->where('parent_id',null);
    }

    public function orderStatus()
    {
        return $this->belongsTo(OrderStatus::class, 'status_id', 'id');
    }

}
