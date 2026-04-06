<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'Product';
    protected $primaryKey = 'ProductID';
    public $timestamps = false;

    protected $fillable = [
        'EmployeeID',
        'CategoryID',
        'ProductName',
        'Brand',
        'Price',
        'Stock',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'CategoryID', 'CategoryID');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'EmployeeID', 'EmployeeID');
    }

    public function detail()
    {
        return $this->hasOne(ProductDetail::class, 'ProductID', 'ProductID');
    }

    public function offers()
    {
        return $this->hasMany(Offer::class, 'ProductID', 'ProductID');
    }
}
