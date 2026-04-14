<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $table = 'Delivery';
    protected $primaryKey = 'DeliveryID';
    public $timestamps = false;

    protected $fillable = [
        'OrderID',
        'DelManID',
        'DeliveryStatus',
        'DeliveryDate',
        'Rating',
        'RatingComment',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'OrderID', 'OrderID');
    }

    public function deliveryMan()
    {
        return $this->belongsTo(DeliveryMan::class, 'DelManID', 'DelManID');
    }
}
