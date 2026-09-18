<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FoodItem;

class FoodItemController extends Controller
{
    public function index()
    {
        $foodItems = FoodItem::with('category')->get();

        return view('staff.foodItems.index', [
            'foodItems' => $foodItems,
        ]);
    }

    public function toggleStatus($id)
    {
        $foodItem = FoodItem::findOrFail($id);
        $newStatus = $foodItem->status === 'available' ? 'out_of_stock' : 'available';
        $foodItem->update(['status' => $newStatus]);

        return redirect()->route('staff.foodItems.index')->with('success', 'Food item status updated!');
    }
}
