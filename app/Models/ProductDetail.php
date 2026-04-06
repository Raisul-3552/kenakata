<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDetail extends Model
{
    use HasFactory;

    protected $table = 'ProductDetails';
    protected $primaryKey = 'ProductID';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'ProductID',
        'Description',
        'Specification',
        'Warranty',
        'Image',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'ProductID', 'ProductID');
    }
}
