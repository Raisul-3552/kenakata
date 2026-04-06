<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $table = 'WalletTransaction';
    protected $primaryKey = 'TransactionID';
    public $timestamps = false;

    protected $fillable = [
        'WalletID',
        'Amount',
        'TransactionType',
        'Description',
        'TransactionDate',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class, 'WalletID', 'WalletID');
    }
}
