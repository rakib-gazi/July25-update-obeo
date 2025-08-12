<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoicedReservation extends Model
{
    protected $fillable = [
        'reservation_id', 'hotel_invoice_id'
    ];
    public function hotelInvoice()
    {
        return $this->belongsTo(HotelInvoice::class);
    }

}
