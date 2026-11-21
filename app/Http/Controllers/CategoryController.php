<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,NULL,id,deleted_at,NULL',
            'description' => 'nullable|string|max:500',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()
            ->route('staff.foodItems.index', ['category' => $category->id])
            ->with('success', 'Category added!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $id . ',id,deleted_at,NULL',
            'description' => 'nullable|string|max:500',
        ]);

        $category = Category::findOrFail($id);

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()
            ->route('staff.foodItems.index', ['category' => $category->id])
            ->with('success', 'Category updated!');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->foodItems()->delete();
        $category->delete();

        return redirect()
            ->route('staff.foodItems.index')
            ->with('success', 'Category deleted!');
    }
}
