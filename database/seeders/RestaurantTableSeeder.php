<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RestaurantTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 5; $i++){
            \App\Models\RestaurantTable::create([
                'table_number' => $i,
                'qr_token'=> \Illuminate\Support\Str::random(20),
                'status' => 'active',
            ]);
        }
    }
}
