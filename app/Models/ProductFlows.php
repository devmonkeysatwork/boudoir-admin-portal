<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductFlows extends Model
{
    use HasFactory;

    public function status()
    {
        return $this->hasMany(OrderStatus::class,'step_id','id');
    }
}
