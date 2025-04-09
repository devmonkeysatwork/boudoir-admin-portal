<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    const possibleNoneValueAttributes = ['Gilding','Imprinting or Logo','Second Imprinting or Logo'];
    protected $fillable = [
        'name'
    ];

    public function attributes()
    {
        return $this->hasMany(ProductAttributes::class);
    }

    public function flow()
    {
        return $this->hasMany(ProductFlows::class,'product_id','id');
    }

    public function orderStatuses()
    {
        return $this->belongsToMany(OrderStatus::class, 'product_flows', 'product_id', 'step_id')
            ->withPivot('step_no')
            ->orderBy('pivot_step_no');
    }
}
