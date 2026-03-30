<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmploymentCode extends Model
{
    protected $table      = 'EmploymentCode';
    protected $primaryKey = 'CodeID';
    public    $incrementing = true;
    public    $timestamps   = false; // Handled manually by SQL

    protected $fillable = [
        'RegCode',
        'AdminID',
        'IsUsed',
        'CreatedAt'
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'AdminID', 'AdminID');
    }
}
