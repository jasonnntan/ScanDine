<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Category::create(['name' => 'Main Course', 'description' => 'Rice, noodles, main dishes']);
        \App\Models\Category::create(['name' => 'Drink', 'description' => 'Hot and cold beverage']);
        \App\Models\Category::create(['name' => 'Desserts', 'description' => 'Sweet treats']);
    }
}
