<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'Customer';
    protected $primaryKey = 'CustomerID';
    public $timestamps = false;

    protected $fillable = [
        'CustomerName',
        'Phone',
        'Email',
        'Password',
        'Address',
    ];

    protected $hidden = [
        'Password',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'CustomerID', 'CustomerID');
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class, 'CustomerID', 'CustomerID');
    }
}
