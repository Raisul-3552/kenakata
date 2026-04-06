<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $table = 'Offer';
    protected $primaryKey = 'OfferID';
    public $timestamps = false;

    protected $fillable = [
        'ProductID',
        'DiscountAmount',
        'StartDate',
        'EndDate',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'ProductID', 'ProductID');
    }
}
