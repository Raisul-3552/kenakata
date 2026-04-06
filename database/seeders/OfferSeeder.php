<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OfferSeeder extends Seeder
{
    public function run()
    {
        DB::table('Offer')->insert([
            ['ProductID' => 1, 'DiscountAmount' => 5000.00, 'StartDate' => '2026-04-01', 'EndDate' => '2026-04-10'],
            ['ProductID' => 3, 'DiscountAmount' => 2000.00, 'StartDate' => '2026-04-01', 'EndDate' => '2026-04-15'],
            ['ProductID' => 10, 'DiscountAmount' => 8000.00, 'StartDate' => '2026-04-05', 'EndDate' => '2026-04-12'],
        ]);
    }
}
