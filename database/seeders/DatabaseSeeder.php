<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            AdminSeeder::class,
            EmployeeSeeder::class,
            CustomerSeeder::class,
            DeliveryManSeeder::class,
            CategorySeeder::class,
            CouponSeeder::class,
            ProductSeeder::class,
            OfferSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
