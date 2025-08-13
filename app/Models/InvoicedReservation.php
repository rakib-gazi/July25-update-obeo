<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoicedReservation extends Model
{
    protected $fillable = [
        'reservation_id', 'hotel_invoice_id'
    ];
    protected $hidden = ['created_at', 'updated_at',];
    public function hotelInvoice()
    {
        return $this->belongsTo(HotelInvoice::class);
    }
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

}
