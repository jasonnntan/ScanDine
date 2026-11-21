<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FoodItem extends Model
{
    use SoftDeletes;
    protected $fillable = ['category_id', 'name', 'description', 'price', 'unit_cost', 'image', 'status'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
