<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $todayRevenue = \App\Models\CustomerOrder::whereDate('created_at', today())
            ->where('order_status', 'completed')
            ->sum('total_amount');

        $todayOrderCount = \App\Models\CustomerOrder::whereDate('created_at', today())->count();

        $bestSeller = \App\Models\OrderItem::selectRaw('item_name_snapshot, SUM(quantity) as total_sold')
            ->groupBy('item_name_snapshot')
            ->orderByDesc('total_sold')
            ->first();

        $salesByCategory = \App\Models\OrderItem::join('food_items', 'order_items.food_item_id', '=', 'food_items.id')
            ->join('categories', 'food_items.category_id', '=', 'categories.id')
            ->selectRaw('categories.name as category_name, SUM(order_items.quantity * order_items.unit_price) as total_sales')
            ->groupBy('categories.name')
            ->get();

        return view('staff.dashboard', [
            'todayRevenue' => $todayRevenue,
            'todayOrderCount' => $todayOrderCount,
            'bestSeller' => $bestSeller,
            'salesByCategory' => $salesByCategory,
        ]);
    }
}
