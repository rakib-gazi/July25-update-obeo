<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationStatus extends Model
{
    //
    protected $fillable = ['status'];
    protected $hidden = ['created_at', 'updated_at',];
}
