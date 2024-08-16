<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderLogs extends Model
{
    use HasFactory;

    function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }
    function status(){
        return $this->belongsTo(OrderStatus::class,'status_id','id');
    }
    function updated_by(){
        return $this->belongsTo(User::class,'updated_by','id');
    }
}
