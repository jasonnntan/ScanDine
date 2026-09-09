<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['customer_order_id', 'food_item_id', 'item_name_snapshot', 'unit_price', 'quantity', 'item_note'];

    public function customerOrder()
    {
        return $this->belongsTo(CustomerOrder::class);
    }
    public function foodItem ()
    {
        return $this->belongsTo(FoodItem::class);
    }
}
