<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Child extends Model
{
    //
    protected $fillable = ['age'];
    protected $hidden = ['created_at', 'updated_at',];
    public function reservations() {
        return $this->belongsToMany(Reservation::class, 'reservation_children');
    }

}
