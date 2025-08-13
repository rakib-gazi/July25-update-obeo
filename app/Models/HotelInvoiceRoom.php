<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelInvoiceRoom extends Model
{
    protected $fillable = [
        'hotel_invoice_id', 'room_name', 'total_room',
        'total_price', 'currency_id', 'exchange_rate',
        'commission_type', 'commission_value', 'hotel_given_price'
    ];
    protected $hidden = ['created_at', 'updated_at',];
}
