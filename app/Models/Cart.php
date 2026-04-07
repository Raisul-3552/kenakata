<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'Cart';
    protected $primaryKey = 'CartID';
    public $timestamps = false;

    protected $fillable = [
        'CustomerID',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'CustomerID', 'CustomerID');
    }

    public function items()
    {
        return $this->hasMany(CartItem::class, 'CartID', 'CartID');
    }
}
