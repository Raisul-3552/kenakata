<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run()
    {
        DB::table('Category')->insert([
            ['CategoryName' => 'Electronics', 'Description' => 'Gadgets, phones, laptops and more'],
            ['CategoryName' => 'Home Appliances', 'Description' => 'Fridge, TV, AC, etc.'],
            ['CategoryName' => 'Fashion', 'Description' => 'Clothing, shoes, accessories'],
            ['CategoryName' => 'Groceries', 'Description' => 'Daily essentials'],
            ['CategoryName' => 'Books', 'Description' => 'Academic and leisure reading'],
        ]);
    }
}
