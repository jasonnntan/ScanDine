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

    // crud
    public function create()
    {
        $categories = \App\Models\Category::all();
        return view('staff.foodItems.create',[
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('food-items', 'public');
        }

        \App\Models\FoodItem::create([
        'category_id' => $request->category_id,
        'name' => $request->name,
        'description' => $request->description,
        'price' => $request->price,
        'unit_cost' => $request->unit_cost,
        'image' => $imagePath,
        'status' => 'available',
        ]);

        return redirect()->route('staff.foodItems.index')->with('success', 'Food item added!');
    }

    public function edit($id)
    {
        $foodItem = \App\Models\FoodItem::findOrFail($id);
        $categories = \App\Models\Category::all();

        return view('staff.foodItems.edit', [
            'foodItem' => $foodItem,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, $id)
    {
        $foodItem = \App\Models\FoodItem::findOrFail($id);

        $imagePath = $foodItem->image;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('food-items', 'public');
        }

        $foodItem->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'unit_cost' => $request->unit_cost
        ]);

        return redirect()->route('staff.foodItems.index')->with('success', 'Food item updated!');
    }

    public function destroy($id)
    {
        $foodItem = \App\Models\FoodItem::findOrFail($id);
        $foodItem->delete();

        return redirect()->route('staff.foodItems.index')->with('success', 'Food item deleted!');
    }
}
