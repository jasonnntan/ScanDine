<?php

namespace App\Http\Controllers;

use App\Models\CustomerOrder;
use App\Models\Setting;
use App\Models\RestaurantTable;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StaffOrderController extends Controller
{
       public function index(Request $request)
    {
        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date',
        ]);

        // ----- period -----
        $range = $request->input('range', 'today');
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
        } elseif ($range === 'yesterday') {
            $start = $now->copy()->subDay()->startOfDay();
            $end = $now->copy()->subDay()->endOfDay();
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
            $range = 'today';
            $start = $now->copy()->startOfDay();
            $end = $now->copy()->endOfDay();
        }

        // ----- other filters -----
        $status = $request->input('status', 'all');
        $payment = $request->input('payment', 'all');
        $tableId = $request->input('table');

        $query = CustomerOrder::with('orderItems', 'restaurantTable');

        if ($start) {
            $query->where('created_at', '>=', $start);
        }
        if ($end) {
            $query->where('created_at', '<=', $end);
        }

        if ($status === 'open') {
            $query->whereIn('order_status', ['pending', 'preparing', 'ready']);
        } elseif (in_array($status, ['pending', 'preparing', 'ready', 'served', 'completed'])) {
            $query->where('order_status', $status);
        } else {
            $status = 'all';
        }

        if ($payment === 'paid') {
            $query->where('payment_status', 'paid');
        } elseif ($payment === 'unpaid') {
            $query->where('payment_status', '!=', 'paid');
        } else {
            $payment = 'all';
        }

        if ($tableId) {
            $query->where('restaurant_table_id', $tableId);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        if ($range === 'today') {
            $periodLabel = 'Today';
        } elseif ($start) {
            $periodLabel = $start->isSameDay($end)
                ? $start->format('d M Y')
                : $start->format('d M Y') . ' to ' . $end->format('d M Y');
        } else {
            $periodLabel = 'All time';
        }

        return view('staff.orders.index', [
            'orders' => $orders,
            'tables' => RestaurantTable::orderBy('table_number')->get(),
            'range' => $range,
            'from' => $from,
            'to' => $to,
            'status' => $status,
            'payment' => $payment,
            'tableId' => $tableId,
            'periodLabel' => $periodLabel,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $order = CustomerOrder::findOrFail($id);

        $stages = ['pending', 'preparing', 'ready', 'served', 'completed'];
        $newStatus = $request->order_status;

        if (!in_array($newStatus, $stages)) {
            return redirect()->back();
        }

        if ($newStatus !== $order->order_status) {
            $data = ['order_status' => $newStatus];
            $newIndex = array_search($newStatus, $stages);

            foreach ($stages as $index => $stage) {
                if ($stage === 'pending') {
                    continue;
                }

                if ($index === $newIndex) {
                    // reached this stage: keep the first time it was reached, otherwise use now
                    $data[$stage . '_at'] = $order->{$stage . '_at'} ?? now();
                } elseif ($index > $newIndex) {
                    // moved back before this stage: clear its time
                    $data[$stage . '_at'] = null;
                }
            }

            $order->update($data);
        }

        return redirect()->back()->with('success', 'Order status updated!');
    }

    public function markPaid($id)
    {
        $order = CustomerOrder::findOrFail($id);

        $order->update([
            'payment_status' => 'paid',
            'paid_at' => $order->paid_at ?? now(),
        ]);

        return redirect()->back()->with('success', 'Order marked as paid!');
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
