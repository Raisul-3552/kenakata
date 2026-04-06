<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run()
    {
        // 0. Ensure Admin & Employee exist for Product FK 
        $admin = DB::table('Admin')->where('Email', 'admin@kenakata.com')->first();
        if (!$admin) {
            $adminId = DB::table('Admin')->insertGetId([
                'AdminName' => 'System Admin',
                'Email' => 'admin@kenakata.com',
                'Password' => Hash::make('password'),
            ]);
        } else {
            $adminId = $admin->AdminID;
        }

        $employee = DB::table('Employee')->where('Email', 'employee@kenakata.com')->first();
        if (!$employee) {
            $employeeId = DB::table('Employee')->insertGetId([
                'AdminID' => $adminId,
                'EmployeeName' => 'Dummy Employee',
                'Phone' => '01711122299',
                'Email' => 'employee@kenakata.com',
                'Password' => Hash::make('password'),
                'Address' => 'Employee Area, Dhaka',
            ]);
        } else {
            $employeeId = $employee->EmployeeID;
        }

        // 1. Insert Dummy Customer
        $customer = DB::table('Customer')->where('Email', 'customer@kenakata.com')->first();
        if (!$customer) {
            $customerId = DB::table('Customer')->insertGetId([
                'CustomerName' => 'Dummy Customer',
                'Phone' => '01711122233',
                'Email' => 'customer@kenakata.com',
                'Password' => Hash::make('password'),
                'Address' => '123 Fake Street, Dhaka',
            ]);
        } else {
            $customerId = $customer->CustomerID;
        }

        // 2. Insert dummy category and product if not exist to be safe
        $category = DB::table('Category')->where('CategoryName', 'Electronics')->first();
        if (!$category) {
            $categoryId = DB::table('Category')->insertGetId([
                'CategoryName' => 'Electronics',
                'Description' => 'Electronic devices'
            ]);
        } else {
            $categoryId = $category->CategoryID;
        }

        $product = DB::table('Product')->where('ProductName', 'Dummy Smartphone')->first();
        if (!$product) {
            $productId = DB::table('Product')->insertGetId([
                'EmployeeID' => $employeeId,
                'CategoryID' => $categoryId,
                'ProductName' => 'Dummy Smartphone',
                'Stock' => 100,
                'Price' => 500.00,
                'Brand' => 'Brand X'
            ]);
        } else {
            $productId = $product->ProductID;
        }

        $productDetail = DB::table('ProductDetails')->where('ProductID', $productId)->first();
        if (!$productDetail) {
            DB::table('ProductDetails')->insert([
                'ProductID' => $productId,
                'Description' => 'An amazing dummy smartphone with incredible fake features.',
                'Specification' => '6.5" OLED, 12GB RAM, 256GB Storage, 5000mAh Battery.',
                'Warranty' => '1 Year Dummy Warranty'
            ]);
        }

        // 3. Insert Dummy Orders
        if (DB::table('Order')->where('CustomerID', $customerId)->count() == 0) {
            $orderId1 = DB::table('Order')->insertGetId([
                'CustomerID' => $customerId,
                'OrderStatus' => 'Pending',
                'TotalAmount' => 1000.00,
                'OrderDate' => Carbon::now()->format('Y-m-d'),
                'Address' => '123 Fake Street, Dhaka'
            ]);

            DB::table('OrderItem')->insert([
                'OrderID' => $orderId1,
                'ProductID' => $productId,
                'Quantity' => 2,
                'UnitPrice' => 500.00
            ]);

            $orderId2 = DB::table('Order')->insertGetId([
                'CustomerID' => $customerId,
                'OrderStatus' => 'Shipped',
                'TotalAmount' => 500.00,
                'OrderDate' => Carbon::now()->subDays(2)->format('Y-m-d'),
                'Address' => '123 Fake Street, Dhaka'
            ]);

            DB::table('OrderItem')->insert([
                'OrderID' => $orderId2,
                'ProductID' => $productId,
                'Quantity' => 1,
                'UnitPrice' => 500.00
            ]);
            
            $orderId3 = DB::table('Order')->insertGetId([
                'CustomerID' => $customerId,
                'OrderStatus' => 'Cancelled',
                'TotalAmount' => 1500.00,
                'OrderDate' => Carbon::now()->subDays(5)->format('Y-m-d'),
                'Address' => '123 Fake Street, Dhaka'
            ]);

            DB::table('OrderItem')->insert([
                'OrderID' => $orderId3,
                'ProductID' => $productId,
                'Quantity' => 3,
                'UnitPrice' => 500.00
            ]);
        }
    }
}
