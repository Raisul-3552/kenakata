<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run()
    {
        // Orders
        DB::table('Order')->insert([
            ['CustomerID' => 1, 'EmployeeID' => 1, 'CouponID' => 1, 'OrderStatus' => 'Confirmed', 'TotalAmount' => 119990.00, 'OrderDate' => '2026-04-05', 'Address' => 'Dhanmondi, Dhaka'],
            ['CustomerID' => 2, 'EmployeeID' => null, 'CouponID' => null, 'OrderStatus' => 'Pending', 'TotalAmount' => 95000.00, 'OrderDate' => '2026-04-05', 'Address' => 'Uttara Sector 7'],
            ['CustomerID' => 3, 'EmployeeID' => 2, 'CouponID' => 2, 'OrderStatus' => 'Confirmed', 'TotalAmount' => 44950.00, 'OrderDate' => '2026-04-04', 'Address' => 'Mirpur 10'],
            ['CustomerID' => 4, 'EmployeeID' => null, 'CouponID' => null, 'OrderStatus' => 'Cancelled', 'TotalAmount' => 5000.00, 'OrderDate' => '2026-04-03', 'Address' => 'Gulshan 2'],
            ['CustomerID' => 5, 'EmployeeID' => 3, 'CouponID' => null, 'OrderStatus' => 'Confirmed', 'TotalAmount' => 12000.00, 'OrderDate' => '2026-04-05', 'Address' => 'Mohammadpur'],
        ]);

        // OrderItems
        DB::table('OrderItem')->insert([
            ['OrderID' => 1, 'ProductID' => 1, 'Quantity' => 1, 'UnitPrice' => 120000.00],
            ['OrderID' => 2, 'ProductID' => 2, 'Quantity' => 1, 'UnitPrice' => 95000.00],
            ['OrderID' => 3, 'ProductID' => 4, 'Quantity' => 1, 'UnitPrice' => 45000.00],
            ['OrderID' => 4, 'ProductID' => 5, 'Quantity' => 1, 'UnitPrice' => 5000.00],
            ['OrderID' => 5, 'ProductID' => 6, 'Quantity' => 1, 'UnitPrice' => 12000.00],
        ]);
    }
}
