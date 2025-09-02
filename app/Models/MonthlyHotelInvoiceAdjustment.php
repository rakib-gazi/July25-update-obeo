<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyHotelInvoiceAdjustment extends Model
{
    //
    protected  $fillable = ['month','purpose', 'type','amount'];
    protected $hidden = ['created_at', 'updated_at',];
}
