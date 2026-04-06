<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'Admin';
    protected $primaryKey = 'AdminID';
    public $timestamps = false;

    protected $fillable = [
        'AdminName',
        'Email',
        'Photo',
        'Password',
    ];

    protected $hidden = [
        'Password',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'AdminID', 'AdminID');
    }
}
