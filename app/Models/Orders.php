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
}
