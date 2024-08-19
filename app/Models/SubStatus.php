<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubStatus extends Model
{
    use HasFactory;
    protected $table = 'sub_status';

    function status(){
        return $this->belongsTo(OrderStatus::class,'status_id','id');
    }
}
