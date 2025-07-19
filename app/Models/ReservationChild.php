<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationChild extends Model
{
    //

    protected $fillable = ['reservation_id', 'child_id'];
    protected $hidden = ['created_at', 'updated_at',];
}
