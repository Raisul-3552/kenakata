<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        DB::table('Customer')->insert([
            ['CustomerName' => 'Customer Alpha', 'Phone' => '01811111111', 'Email' => 'cust1@gmail.com', 'Password' => Hash::make('password'), 'Address' => 'Dhanmondi, Dhaka'],
            ['CustomerName' => 'Customer Beta',  'Phone' => '01811111112', 'Email' => 'cust2@gmail.com', 'Password' => Hash::make('password'), 'Address' => 'Uttara Sector 7'],
            ['CustomerName' => 'Customer Gamma', 'Phone' => '01811111113', 'Email' => 'cust3@gmail.com', 'Password' => Hash::make('password'), 'Address' => 'Mirpur 10'],
            ['CustomerName' => 'Customer Delta', 'Phone' => '01811111114', 'Email' => 'cust4@gmail.com', 'Password' => Hash::make('password'), 'Address' => 'Gulshan 2'],
            ['CustomerName' => 'Customer Epsilon','Phone' => '01811111115', 'Email' => 'cust5@gmail.com', 'Password' => Hash::make('password'), 'Address' => 'Mohammadpur'],
        ]);
    }
}
