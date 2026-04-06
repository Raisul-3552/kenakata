<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Employee extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'Employee';
    protected $primaryKey = 'EmployeeID';
    public $timestamps = false;

    protected $fillable = [
        'AdminID',
        'EmployeeName',
        'Phone',
        'Email',
        'Password',
        'Address',
    ];

    protected $hidden = [
        'Password',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'AdminID', 'AdminID');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'EmployeeID', 'EmployeeID');
    }

    public function confirmedOrders()
    {
        return $this->hasMany(Order::class, 'EmployeeID', 'EmployeeID');
    }
}
