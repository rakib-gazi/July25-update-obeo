<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationRoom extends Model
{
    //
    protected $table = 'reservation_rooms';
    protected $fillable = ['reservation_id', 'room_id'];
    protected $hidden = ['created_at', 'updated_at',];
}
