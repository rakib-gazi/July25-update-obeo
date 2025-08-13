<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelInvoice extends Model
{
    protected $fillable = [
        'hotel_id', 'inv_no', 'inv_date',
        'total_amount', 'total_advance', 'currency_id'
    ];
    protected $hidden = ['created_at', 'updated_at',];
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function invoicedReservation()
    {
        return $this->hasOne(InvoicedReservation::class);
    }

    public function hotelInvoiceRooms()
    {
        return $this->hasMany(HotelInvoiceRoom::class);
    }

}
