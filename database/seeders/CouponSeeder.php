<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CouponSeeder extends Seeder
{
    public function run()
    {
        DB::table('Coupon')->insert([
            ['CouponCode' => 'SAVE10', 'DiscountAmount' => 10.00, 'StartDate' => '2026-01-01', 'EndDate' => '2026-12-31'],
            ['CouponCode' => 'WELCOME50', 'DiscountAmount' => 50.00, 'StartDate' => '2026-04-01', 'EndDate' => '2026-04-30'],
            ['CouponCode' => 'RAMADAN24', 'DiscountAmount' => 100.00, 'StartDate' => '2026-03-01', 'EndDate' => '2026-04-15'],
        ]);
    }
}
