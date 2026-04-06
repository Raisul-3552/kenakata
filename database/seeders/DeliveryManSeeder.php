<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DeliveryManSeeder extends Seeder
{
    public function run()
    {
        DB::table('DeliveryMan')->insert([
            ['DelManName' => 'Rider One', 'Phone' => '01911111111', 'Email' => 'rider1@delivery.com', 'Password' => Hash::make('password'), 'Address' => 'Motijheel'],
            ['DelManName' => 'Rider Two', 'Phone' => '01911111112', 'Email' => 'rider2@delivery.com', 'Password' => Hash::make('password'), 'Address' => 'Rampura'],
            ['DelManName' => 'Rider Three','Phone' => '01911111113', 'Email' => 'rider3@delivery.com', 'Password' => Hash::make('password'), 'Address' => 'Khilgaon'],
        ]);
    }
}
