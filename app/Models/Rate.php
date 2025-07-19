<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    //
    protected $fillable = [
        'rate',

    ];
    protected $hidden = ['created_at', 'updated_at',];
}
