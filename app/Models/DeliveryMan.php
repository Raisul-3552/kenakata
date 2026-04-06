<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class DeliveryMan extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'DeliveryMan';
    protected $primaryKey = 'DelManID';
    public $timestamps = false;

    protected $fillable = [
        'DelManName',
        'Phone',
        'Email',
        'Password',
        'Address',
        'Status',
    ];

    protected $hidden = [
        'Password',
    ];

    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'DelManID', 'DelManID');
    }
}
