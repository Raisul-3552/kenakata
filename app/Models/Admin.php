<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table      = 'Admin';
    protected $primaryKey = 'AdminID';
    public    $incrementing = false;   // AdminID is set manually (no IDENTITY)
    public    $timestamps   = false;
    protected $keyType      = 'int';

    protected $fillable = ['AdminID', 'AdminName', 'Email', 'Password'];
    protected $hidden   = ['Password'];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'AdminID', 'AdminID');
    }
}
