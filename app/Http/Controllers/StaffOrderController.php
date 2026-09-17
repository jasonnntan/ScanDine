<?php

namespace App\Http\Controllers;

use App\Models\CustomerOrder;
use Illuminate\Http\Request;

class StaffOrderController extends Controller
{
    public function index()
    {
        $orders = CustomerOrder::with('orderItems', 'restaurantTable')->orderBy('created_at', 'desc')->get();

        return view('staff.orders.index', [
            'orders' => $orders,
        ]);
    }
}
