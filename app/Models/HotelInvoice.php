<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelInvoice extends Model
{
    protected $fillable = [
        'hotel_id', 'inv_no', 'inv_date',
        'total_amount', 'total_advance', 'currency_id'
    ];

}
