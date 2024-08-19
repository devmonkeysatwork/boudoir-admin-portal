<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    use HasFactory;
    protected $table = 'order_status';
    const adminStatuses = ['On hold','Issue with print','Remake + Reasons'];


    function sub_status(){
        return $this->hasMany(SubStatus::class,'status_id','id');
    }
}
