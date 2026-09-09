<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FoodItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mainCourse = \App\Models\Category::where('name', 'Main Course')->first();
        $drinks = \App\Models\Category::where('name', 'Drink')->first();
        $desserts = \App\Models\Category::where('name', 'Desserts')->first();

        \App\Models\FoodItem::create([
            'category_id' => $mainCourse->id,
            'name' => 'Fried Rice',
            'description' => 'Classic fried rice with egg',
            'price' => '8.50',
            'unit_cost' => '3.00',
            'status' => 'available',
        ]);

        \App\Models\FoodItem::create([
            'category_id' => $drinks->id,
            'name' => 'Iced Lemon Tea',
            'description' => 'Refreshing iced tea',
            'price' => '4.00',
            'unit_cost' => '1.20',
            'status' => 'available',
        ]);

        \App\Models\FoodItem::create([
            'category_id' => $desserts->id,
            'name' => 'Chendol',
            'description' => 'Classic fried rice with egg',
            'price' => '5.50',
            'unit_cost' => '2.00',
            'status' => 'out_of_stock',
        ]);
    }
}
