<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryMan extends Model
{
    protected $table      = 'DeliveryMan';
    protected $primaryKey = 'DelManID';
    public    $incrementing = true;
    public    $timestamps   = false;

    protected $fillable = ['DelManName', 'Phone', 'Email', 'Password', 'Address'];
    protected $hidden   = ['Password'];
}
