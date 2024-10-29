<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItems extends Model
{
    use HasFactory;

    function attributes(){
        return $this->hasMany(ItemAttributes::class,'item_id','id');
    }
    function products(){
        return $this->hasMany(Product::class,'id','product_id');
    }
}
