<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table      = 'Employee';
    protected $primaryKey = 'EmployeeID';
    public    $incrementing = true;
    public    $timestamps   = false;

    protected $fillable = ['EmployeeName', 'Phone', 'Email', 'Password', 'Address', 'AdminID'];
    protected $hidden   = ['Password'];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'AdminID', 'AdminID');
    }
}
