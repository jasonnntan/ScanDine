<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerOrder extends Model
{
    protected $fillable = ['restaurant_table_id', 'customer_name', 'customer_phone', 'customer_note', 'total_amount', 'order_status', 'payment_status'];

    public function restaurantTable()
    {
        return $this->belongsTo(RestaurantTable::class);
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
