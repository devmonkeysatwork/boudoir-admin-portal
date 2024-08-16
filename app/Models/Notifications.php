<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    use HasFactory;
    const typeComment = 'COMMENT';
    const typestatus = 'STATUS_UPDATED';



    function comment(){
        return $this->belongsTo(OrderComments::class,'comment_id','id');
    }
    function log(){
        return $this->belongsTo(OrderLogs::class,'log_id','id');
    }
}
