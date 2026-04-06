<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $table = 'Coupon';
    protected $primaryKey = 'CouponID';
    public $timestamps = false;

    protected $fillable = [
        'CouponCode',
        'DiscountAmount',
        'StartDate',
        'EndDate',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'CouponID', 'CouponID');
    }
}
