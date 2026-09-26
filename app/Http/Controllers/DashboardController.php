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

        $estimatedProfit = \App\Models\OrderItem::join('food_items', 'order_items.food_item_id', '=', 'food_items.id')
            ->join('customer_orders', 'order_items.customer_order_id', '=', 'customer_orders.id')
            ->where('customer_orders.order_status', 'completed')
            ->selectRaw('SUM((order_items.unit_price - food_items.unit_cost) * order_items.quantity) as total_profit')
            ->value('total_profit');

        return view('staff.dashboard', [
            'todayRevenue' => $todayRevenue,
            'todayOrderCount' => $todayOrderCount,
            'bestSeller' => $bestSeller,
            'salesByCategory' => $salesByCategory,
            'estimatedProfit' => $estimatedProfit,
        ]);
    }
}
