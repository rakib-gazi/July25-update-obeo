<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'fullName',
        'userName',
        'phone',
        'email',
        'role',
        'password',
    ];

    protected $attributes = [
        'otp' => '0',
    ];

    protected $hidden = [
        'remember_token',
        'created_at',
        'updated_at',
    ];
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',

        ];
    }
}
