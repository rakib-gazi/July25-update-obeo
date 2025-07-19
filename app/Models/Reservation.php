<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'obeo_sl',
        'status_id',
        'reservation_no',
        'check_in',
        'check_out',
        'reservation_date',
        'hotel_id',
        'guest_name',
        'rate_id',
        'total_advance',
        'currency_id',
        'source_id',
        'payment_method_id',
        'phone',
        'email',
        'total_adult',
        'request',
        'comment',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hotel() {
        return $this->belongsTo(Hotel::class);
    }

    public function rate() {
        return $this->belongsTo(Rate::class);
    }

    public function currency() {
        return $this->belongsTo(Currency::class);
    }

    public function source() {
        return $this->belongsTo(Source::class);
    }

    public function paymentMethod() {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function children() {
        return $this->belongsToMany(Child::class, 'reservation_children');
    }

    public function rooms() {
        return $this->belongsToMany(Room::class, 'reservation_rooms');
    }
    public function reservation_status()
    {
        return $this->belongsTo(ReservationStatus::class, 'status_id');
    }
    protected $hidden = ['created_at', 'updated_at',];
}
