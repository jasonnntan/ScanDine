<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodItem extends Model
{
    protected $fillable = ['category_id', 'name', 'description', 'price', 'unit_cost', 'image', 'status'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
