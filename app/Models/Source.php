<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    //
    protected $fillable = [
        'source',

    ];
    protected $hidden = ['created_at', 'updated_at',];
}
