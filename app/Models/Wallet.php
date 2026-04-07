<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $table = 'Wallet';
    protected $primaryKey = 'WalletID';
    public $timestamps = false;

    protected $fillable = [
        'CustomerID',
        'Balance',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'CustomerID', 'CustomerID');
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class, 'WalletID', 'WalletID');
    }
}
