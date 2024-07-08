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
}
