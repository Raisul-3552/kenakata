<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users'; // Database-first table
    protected $fillable = ['name', 'email', 'password'];
    protected $hidden = ['password'];
}