<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CustomerOrder;
use App\Models\OrderItem;
use App\Models\RestaurantTable;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date',
        ]);

        // ----- period -----
        $range = $request->input('range', '30d');
        $from = $request->input('from');
        $to = $request->input('to');
        $now = now();

        if ($from && $to) {
            $start = Carbon::parse($from)->startOfDay();
            $end = Carbon::parse($to)->endOfDay();
            $range = 'custom';

            if ($start->gt($end)) {
                $swap = $start;
                $start = $end->copy()->startOfDay();
                $end = $swap->copy()->endOfDay();
            }
        } elseif ($range === 'today') {
            $start = $now->copy()->startOfDay();
            $end = $now->copy()->endOfDay();
        } elseif ($range === '7d') {
            $start = $now->copy()->subDays(6)->startOfDay();
            $end = $now->copy()->endOfDay();
        } elseif ($range === 'month') {
            $start = $now->copy()->startOfMonth();
            $end = $now->copy()->endOfDay();
        } elseif ($range === 'all') {
            $start = null;
            $end = null;
        } else {
            $range = '30d';
            $start = $now->copy()->subDays(29)->startOfDay();
            $end = $now->copy()->endOfDay();
        }

        // ----- other filters -----
        $status = $request->input('status') === 'all' ? 'all' : 'completed';
        $categoryId = $request->input('category');
        $tableId = $request->input('table');

        // ----- one place that applies every filter to the order items -----
        $itemQuery = function () use ($start, $end, $status, $categoryId, $tableId) {
            $query = OrderItem::query()
                ->join('customer_orders', 'order_items.customer_order_id', '=', 'customer_orders.id')
                ->join('food_items', 'order_items.food_item_id', '=', 'food_items.id')
                ->join('categories', 'food_items.category_id', '=', 'categories.id');

            if ($start) {
                $query->where('customer_orders.created_at', '>=', $start);
            }
            if ($end) {
                $query->where('customer_orders.created_at', '<=', $end);
            }

            if ($status === 'completed') {
                $query->where('customer_orders.order_status', 'completed');
            } else {
                $query->where('customer_orders.order_status', '!=', 'cancelled');
            }

            if ($categoryId) {
                $query->where('food_items.category_id', $categoryId);
            }
            if ($tableId) {
                $query->where('customer_orders.restaurant_table_id', $tableId);
            }

            return $query;
        };

        // ----- KPI cards -----
        $revenue = (float) $itemQuery()
            ->selectRaw('COALESCE(SUM(order_items.quantity * order_items.unit_price), 0) as v')
            ->value('v');

        $orderCount = (int) $itemQuery()
            ->selectRaw('COUNT(DISTINCT customer_orders.id) as v')
            ->value('v');

        $itemsSold = (int) $itemQuery()
            ->selectRaw('COALESCE(SUM(order_items.quantity), 0) as v')
            ->value('v');

        $profit = (float) $itemQuery()
            ->selectRaw('COALESCE(SUM((order_items.unit_price - food_items.unit_cost) * order_items.quantity), 0) as v')
            ->value('v');

        $avgOrder = $orderCount > 0 ? $revenue / $orderCount : 0;

        $bestSeller = $itemQuery()
            ->selectRaw('order_items.item_name_snapshot as name, SUM(order_items.quantity) as total_sold')
            ->groupBy('order_items.item_name_snapshot')
            ->orderByDesc('total_sold')
            ->first();

        // ----- charts -----
        $salesByDay = $itemQuery()
            ->selectRaw('DATE(customer_orders.created_at) as sales_day, SUM(order_items.quantity * order_items.unit_price) as total')
            ->groupBy('sales_day')
            ->orderBy('sales_day')
            ->get();

        $salesByCategory = $itemQuery()
            ->selectRaw('categories.name as category_name, SUM(order_items.quantity * order_items.unit_price) as total_sales')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_sales')
            ->get();

        $topItems = $itemQuery()
            ->selectRaw('order_items.item_name_snapshot as name, SUM(order_items.quantity) as qty')
            ->groupBy('order_items.item_name_snapshot')
            ->orderByDesc('qty')
            ->limit(5)
            ->get();

        // orders by status: ignores the "orders counted" and category filters on purpose,
        // so you can see how many orders are still open
        $statusQuery = CustomerOrder::query();
        if ($start) {
            $statusQuery->where('created_at', '>=', $start);
        }
        if ($end) {
            $statusQuery->where('created_at', '<=', $end);
        }
        if ($tableId) {
            $statusQuery->where('restaurant_table_id', $tableId);
        }

        $statusChart = $statusQuery
            ->selectRaw('order_status, COUNT(*) as total')
            ->groupBy('order_status')
            ->get()
            ->map(function ($row) {
                return ['name' => $row->order_status, 'value' => (int) $row->total];
            })
            ->values();

        $periodLabel = $start ? $start->format('d M Y') . ' to ' . $end->format('d M Y') : 'All time';

        return view('staff.dashboard', [
            'range' => $range,
            'from' => $from,
            'to' => $to,
            'status' => $status,
            'categoryId' => $categoryId,
            'tableId' => $tableId,
            'categories' => Category::orderBy('name')->get(),
            'tables' => RestaurantTable::orderBy('table_number')->get(),
            'periodLabel' => $periodLabel,
            'revenue' => $revenue,
            'orderCount' => $orderCount,
            'itemsSold' => $itemsSold,
            'profit' => $profit,
            'avgOrder' => $avgOrder,
            'bestSeller' => $bestSeller,
            'salesByDay' => $salesByDay,
            'salesByCategory' => $salesByCategory,
            'topItems' => $topItems,
            'statusChart' => $statusChart,
        ]);
    }
}


