<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RestaurantTable;
use App\Models\Category;

class OrderController extends Controller
{
    public function menu($qr_token)
    {
        $table = RestaurantTable::where('qr_token', $qr_token)->firstOrFail();
        $categories = Category::with('foodItems')->get();

        return view('order.menu',[
            'table' => $table,
            'categories' => $categories,
        ]);
    }
}
