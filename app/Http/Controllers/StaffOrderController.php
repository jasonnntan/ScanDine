<?php

namespace App\Http\Controllers;

use App\Models\CustomerOrder;
use App\Models\Setting;
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

    public function printTicket($id)
    {
        $order = CustomerOrder::with('orderItems', 'restaurantTable')->findOrFail($id);

        return view('staff.orders.print', [
            'order' => $order,
        ]);
    }

    public function printReceipt($id)
    {
        $order = CustomerOrder::with('orderItems', 'restaurantTable')->findOrFail($id);
        $setting = Setting::current();

        $subtotal = $order->orderItems->sum(function ($item) {
            return $item->unit_price * $item->quantity;
        });

        $serviceCharge = $setting->service_charge_enabled
            ? round($subtotal * $setting->service_charge_rate / 100, 2)
            : 0;

        $sst = $setting->sst_enabled
            ? round(($subtotal + $serviceCharge) * $setting->sst_rate / 100, 2)
            : 0;

        return view('staff.orders.receipt', [
            'order' => $order,
            'setting' => $setting,
            'subtotal' => $subtotal,
            'serviceCharge' => $serviceCharge,
            'sst' => $sst,
            'grandTotal' => $subtotal + $serviceCharge + $sst,
        ]);
    }
}
