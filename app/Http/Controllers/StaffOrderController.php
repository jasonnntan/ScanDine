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

    public function updateStatus(Request $request, $id)
    {
    $order = CustomerOrder::findOrFail($id);
    $order->update(['order_status' => $request->order_status]);

    return redirect()->route('staff.orders.index')->with('success', 'Order status updated!');
    }

    public function markPaid($id)
    {
    $order = CustomerOrder::findOrFail($id);
    $order->update(['payment_status' => 'paid']);

    return redirect()->route('staff.orders.index')->with('success', 'Order marked as paid!');
    }
}
