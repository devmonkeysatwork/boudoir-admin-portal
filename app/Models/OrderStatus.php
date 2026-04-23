<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    use HasFactory;
    protected $table = 'order_status';
    const adminStatuses = ['On hold','Issue with print','Remake + Reasons','Internal Reprint','Engraving','Waiting'];
    const exceptionStatuses = [13,14,15,16,21];
    const possibleNoneValueStatus = ['Gilding','Imprinting'];
    const COMPLETED = 'Completed';
    const ENGRAVING = 'Engraving';
    const Waiting = 'Waiting';
    const RemakeStatusIds = [10,11,12];


    function sub_status(){
        return $this->hasMany(SubStatus::class,'status_id','id');
    }

    public function orders()
    {
        return $this->hasMany(Orders::class, 'status_id', 'id');
    }

    public function orders_count()
    {
        return $this->hasMany(OrderLogs::class, 'status_id', 'id')->select('order_id')->distinct()->count('order_id');
    }

    public function workstation()
    {
        return $this->belongsTo(Workstations::class, 'id', 'id');
    }

    function logs(){
        return $this->hasMany(OrderLogs::class, 'status_id', 'id');
    }

    function first_log(){
        return $this->hasOne(OrderLogs::class, 'status_id', 'id')
            ->oldest('time_started');
    }
    function last_log(){
        return $this->hasOne(OrderLogs::class, 'status_id', 'id')
            ->latest('time_started');
    }


    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_flows', 'step_id', 'product_id')
            ->withPivot('step_no')
            ->orderBy('pivot_step_no');
    }

    // Fetch IDs dynamically by name so they always match the DB
// regardless of what ID they were assigned
    public static function getRemakeStatusIds(): array
    {
        return self::whereIn('status_name', [
            'Issue with print',
            'Remake + Reasons',
            'Internal Reprint',
        ])->pluck('id')->toArray();
    }
}
