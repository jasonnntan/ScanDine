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

        return view('staff.dashboard', [
            'todayRevenue' => $todayRevenue,
            'todayOrderCount' => $todayOrderCount,
        ]);
    }
}
