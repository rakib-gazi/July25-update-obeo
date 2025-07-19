<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    //
    protected $fillable = [
        'hotelName',
        'hotelAddress',
        'commissionType',
        'expediaCollectsCommission',
        'hotelCollectsCommission',
    ];
    protected $hidden = ['created_at', 'updated_at',];
}
