<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $fillable = [
        'username', 
        'password',
    ];

    protected $hidden = [
        'password', // Hide the password field when fetching the user.
    ];
}
