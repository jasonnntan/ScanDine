<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantTable extends Model
{
    protected $fillable = ['table_number', 'qr_token', 'status', 'is_occupied'];

    public function orders()
    {
        return $this->hasMany(CustomerOrder::class);
    }
}
