<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table      = 'Customer';
    protected $primaryKey = 'CustomerID';
    public    $incrementing = true;
    public    $timestamps   = false;

    protected $fillable = ['CustomerName', 'Phone', 'Email', 'Password', 'Address'];
    protected $hidden   = ['Password'];
}
