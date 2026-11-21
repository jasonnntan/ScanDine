<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FoodItem;

class FoodItemController extends Controller
{
    public function index(Request $request)
    {
        $categories = \App\Models\Category::withCount('foodItems')->get();

        $query = FoodItem::with('category');

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $foodItems = $query->get();

        return view('staff.foodItems.index', [
            'foodItems' => $foodItems,
            'categories' => $categories,
            'selectedCategory' => $request->category,
        ]);
    }

    public function toggleStatus($id)
    {
        $foodItem = FoodItem::findOrFail($id);
        $newStatus = $foodItem->status === 'available' ? 'out_of_stock' : 'available';
        $foodItem->update(['status' => $newStatus]);

        return redirect()->route('staff.foodItems.index')->with('success', 'Food item status updated!');
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0|max:99999.99',
            'unit_cost' => 'nullable|numeric|min:0|max:99999.99',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('food-items', 'public');
        }

        FoodItem::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description ?? '',
            'price' => $request->price,
            'unit_cost' => $request->unit_cost ?? 0,
            'image' => $imagePath,
            'status' => 'available',
        ]);

        return redirect()->route('staff.foodItems.index')->with('success', 'Food item added!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0|max:99999.99',
            'unit_cost' => 'nullable|numeric|min:0|max:99999.99',
            'image' => 'nullable|image|max:2048',
        ]);

        $foodItem = FoodItem::findOrFail($id);

        $imagePath = $foodItem->image;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('food-items', 'public');
        }

        $foodItem->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description ?? '',
            'price' => $request->price,
            'unit_cost' => $request->unit_cost ?? 0,
            'image' => $imagePath,
        ]);

        return redirect()->route('staff.foodItems.index')->with('success', 'Food item updated!');
    }

    public function destroy($id)
    {
        $foodItem = FoodItem::findOrFail($id);
        $foodItem->delete();

        return redirect()->route('staff.foodItems.index')->with('success', 'Food item deleted!');
    }
}
