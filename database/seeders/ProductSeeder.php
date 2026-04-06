<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // Products
        DB::table('Product')->insert([
            ['EmployeeID' => 1, 'CategoryID' => 1, 'ProductName' => 'iPhone 15 Pro', 'Brand' => 'Apple', 'Price' => 120000.00, 'Stock' => 50],
            ['EmployeeID' => 1, 'CategoryID' => 1, 'ProductName' => 'Galaxy S24', 'Brand' => 'Samsung', 'Price' => 95000.00, 'Stock' => 40],
            ['EmployeeID' => 1, 'CategoryID' => 2, 'ProductName' => 'Smart TV 55"', 'Brand' => 'Sony', 'Price' => 75000.00, 'Stock' => 15],
            ['EmployeeID' => 2, 'CategoryID' => 2, 'ProductName' => 'Washing Machine', 'Brand' => 'LG', 'Price' => 45000.00, 'Stock' => 10],
            ['EmployeeID' => 2, 'CategoryID' => 3, 'ProductName' => 'Leather Jacket', 'Brand' => 'Generic', 'Price' => 5000.00, 'Stock' => 100],
            ['EmployeeID' => 3, 'CategoryID' => 3, 'ProductName' => 'Sneakers X', 'Brand' => 'Nike', 'Price' => 12000.00, 'Stock' => 60],
            ['EmployeeID' => 3, 'CategoryID' => 4, 'ProductName' => 'Basmati Rice 5kg', 'Brand' => 'Pran', 'Price' => 650.00, 'Stock' => 500],
            ['EmployeeID' => 1, 'CategoryID' => 4, 'ProductName' => 'Olive Oil 1L', 'Brand' => 'Borgess', 'Price' => 1200.00, 'Stock' => 200],
            ['EmployeeID' => 2, 'CategoryID' => 5, 'ProductName' => 'Laravel for Beginners', 'Brand' => 'Addie', 'Price' => 800.00, 'Stock' => 30],
            ['EmployeeID' => 3, 'CategoryID' => 1, 'ProductName' => 'MacBook Air M3', 'Brand' => 'Apple', 'Price' => 145000.00, 'Stock' => 20],
        ]);

        // ProductDetails
        DB::table('ProductDetails')->insert([
            ['ProductID' => 1, 'Description' => 'Latest iPhone', 'Specification' => '8GB RAM, 128GB Storage', 'Warranty' => '1 Year Apple International', 'Image' => 'iphone15.jpg'],
            ['ProductID' => 2, 'Description' => 'Flagship Samsung', 'Specification' => '12GB RAM, 256GB Storage', 'Warranty' => '1 Year Samsung BD', 'Image' => 's24.jpg'],
            ['ProductID' => 3, 'Description' => '4K Ultra HD', 'Specification' => '55 Inch Android TV', 'Warranty' => '2 Years Panel Warranty', 'Image' => 'tv55.jpg'],
            ['ProductID' => 4, 'Description' => 'Front Load', 'Specification' => '9KG Capacity', 'Warranty' => '10 Years Motor Warranty', 'Image' => 'lgwm.jpg'],
            ['ProductID' => 5, 'Description' => 'Pure Leather', 'Specification' => 'Black, XL size available', 'Warranty' => 'None', 'Image' => 'jacket.jpg'],
            ['ProductID' => 6, 'Description' => 'Running Shoes', 'Specification' => 'White/Blue colorway', 'Warranty' => '6 Months Manufacturing', 'Image' => 'nike_sneakers.jpg'],
            ['ProductID' => 7, 'Description' => 'Premium Rice', 'Specification' => 'High quality extra long grain', 'Warranty' => 'None', 'Image' => 'rice5kg.jpg'],
            ['ProductID' => 8, 'Description' => 'Spanish Olive Oil', 'Specification' => 'Extra Virgin, Glass bottle', 'Warranty' => 'None', 'Image' => 'oil1l.jpg'],
            ['ProductID' => 9, 'Description' => 'Web Dev book', 'Specification' => 'Comprehensive guide to Laravel', 'Warranty' => 'None', 'Image' => 'laravel_book.jpg'],
            ['ProductID' => 10, 'Description' => 'Lightweight powerful', 'Specification' => 'M3 Chip, 16GB RAM, 512GB SSD', 'Warranty' => '1 Year Apple Care', 'Image' => 'macbookm3.jpg'],
        ]);
    }
}
