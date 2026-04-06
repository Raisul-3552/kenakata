<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'Order';
    protected $primaryKey = 'OrderID';
    public $timestamps = false; // Prompt specify OrderDate, not usual timestamps

    protected $fillable = [
        'CustomerID',
        'EmployeeID',
        'CouponID',
        'OrderStatus',
        'TotalAmount',
        'OrderDate',
        'Address',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'CustomerID', 'CustomerID');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'EmployeeID', 'EmployeeID');
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'CouponID', 'CouponID');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'OrderID', 'OrderID');
    }

    public function delivery()
    {
        return $this->hasOne(Delivery::class, 'OrderID', 'OrderID');
    }
}
