<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        DB::table('Employee')->insert([
            ['AdminID' => 1, 'EmployeeName' => 'Employee One', 'Phone' => '01711111111', 'Email' => 'emp1@kenakata.com', 'Password' => Hash::make('password'), 'Address' => 'AUST Campus House 1'],
            ['AdminID' => 1, 'EmployeeName' => 'Employee Two', 'Phone' => '01711111112', 'Email' => 'emp2@kenakata.com', 'Password' => Hash::make('password'), 'Address' => 'Tejgaon Industrial Area'],
            ['AdminID' => 1, 'EmployeeName' => 'Employee Three', 'Phone' => '01711111113', 'Email' => 'emp3@kenakata.com', 'Password' => Hash::make('password'), 'Address' => 'Banani, Dhaka'],
        ]);
    }
}
