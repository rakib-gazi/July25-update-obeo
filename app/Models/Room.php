<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    //
    protected $fillable = [
        'name',
        'total_room',
        'total_night',
        'total_price',
        'currency_id',
    ];
    public function reservations() {
        return $this->belongsToMany(Reservation::class, 'reservation_rooms');
    }
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
    protected $hidden = ['created_at', 'updated_at',];
}
