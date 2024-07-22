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
}
