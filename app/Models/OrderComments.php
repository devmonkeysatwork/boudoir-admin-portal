<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderComments extends Model
{
    use HasFactory;

    public function getAddedOnAttribute()
    {
        return $this->created_at->format('Y-m-d h:m:s');
    }

    public function order()
    {
        return $this->belongsTo(Orders::class,'order_id','id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function replies()
    {
        return $this->hasMany(OrderComments::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(OrderComments::class, 'parent_id');
    }
}
