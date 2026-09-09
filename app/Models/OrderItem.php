<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    public function customerOrder()
    {
        return $this->belongsTo(CustomerOrder::class);
    }
    public function foodItem ()
    {
        return $this->belongsTo(FoodItem::class);
    }
}
