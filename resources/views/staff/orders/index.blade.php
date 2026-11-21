{{--
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff - Orders</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="max-w-5xl mx-auto p-4 md:p-6">

        <h1 class="text-2xl font-bold text-gray-800 mb-4">All Orders</h1>

        @if (session('success'))
            <p class="bg-green-100 text-green-800 p-3 rounded-xl mb-4">{{ session('success') }}</p>
        @endif

        @php
            $statusColors = [
                'pending' => 'bg-yellow-100 text-yellow-800',
                'preparing' => 'bg-blue-100 text-blue-800',
                'ready' => 'bg-purple-100 text-purple-800',
                'served' => 'bg-indigo-100 text-indigo-800',
                'completed' => 'bg-green-100 text-green-800',
                'cancelled' => 'bg-red-100 text-red-800',
            ];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($orders as $order)
                <div class="bg-white rounded-xl shadow-md p-4">

                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <p class="text-lg font-bold text-gray-800">Order #{{ $order->id }}</p>
                            <p class="text-sm text-gray-500">Table {{ $order->restaurantTable->table_number }}</p>
                        </div>
                        <div class="flex flex-col items-end gap-1">
                            <span class="text-xs font-semibold px-3 py-1 rounded-full {{ $statusColors[$order->order_status] ?? 'bg-gray-100 text-gray-800' }}">{{ $order->order_status }}</span>
                            <span class="text-xs font-semibold px-3 py-1 rounded-full {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">{{ $order->payment_status }}</span>
                        </div>
                    </div>

                    <div class="border-t border-b py-2 mb-3">
                        @foreach ($order->orderItems as $item)
                            <div class="flex justify-between py-1 gap-3 text-sm">
                                <span class="text-gray-800">{{ $item->quantity }}x {{ $item->item_name_snapshot }}</span>
                                <span class="whitespace-nowrap text-gray-600">RM {{ number_format($item->unit_price * $item->quantity, 2) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex justify-between font-bold text-gray-800 mb-3">
                        <span>Total</span>
                        <span>RM {{ number_format($order->total_amount, 2) }}</span>
                    </div>

                    <form action="{{ route('staff.orders.updateStatus', $order->id) }}" method="POST" class="flex gap-2 mb-2">
                        @csrf
                        @method('PUT')
                        <select name="order_status" class="flex-1 p-2 border border-gray-200 rounded-lg bg-white text-sm">
                            <option value="pending" {{ $order->order_status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="preparing" {{ $order->order_status === 'preparing' ? 'selected' : '' }}>Preparing</option>
                            <option value="ready" {{ $order->order_status === 'ready' ? 'selected' : '' }}>Ready</option>
                            <option value="served" {{ $order->order_status === 'served' ? 'selected' : '' }}>Served</option>
                            <option value="completed" {{ $order->order_status === 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                        <button type="submit" class="bg-orange-500 text-white font-semibold px-4 py-2 rounded-lg text-sm">Update</button>
                    </form>

                    <div class="flex gap-2">
                        @if ($order->payment_status !== 'paid')
                            <form action="{{ route('staff.orders.markPaid', $order->id) }}" method="POST" class="flex-1">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="w-full bg-green-600 text-white font-semibold px-4 py-2 rounded-lg text-sm">Mark as Paid</button>
                            </form>
                        @endif
                        <a href="{{ route('staff.orders.print', $order->id) }}" target="_blank" class="flex-1 text-center border border-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg text-sm">🖨️ Print Ticket</a>
                    </div>

                </div>
            @endforeach
        </div>
    </div>
</body>
</html> --}}


@extends('layouts.staff')

@section('title', 'Orders')

@section('content')
<div class="max-w-5xl mx-auto p-4 md:p-6">

        <h1 class="text-2xl font-bold text-gray-800">Orders</h1>
    <p class="text-sm text-gray-500 mb-4">{{ $orders->count() }} order(s) · {{ $periodLabel }}</p>

    {{-- filters --}}
    <form method="GET" action="{{ route('staff.orders.index') }}" class="bg-white rounded-xl shadow-md p-4 mb-4">
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">

            <label class="block">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">Period</span>
                <select name="range"
                        onchange="this.form.elements['from'].value = ''; this.form.elements['to'].value = ''; this.form.submit()"
                        class="mt-1 w-full p-2 border border-gray-200 rounded-lg bg-white text-sm text-gray-700">
                    <option value="today" {{ $range === 'today' ? 'selected' : '' }}>Today</option>
                    <option value="yesterday" {{ $range === 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                    <option value="7d" {{ $range === '7d' ? 'selected' : '' }}>Last 7 days</option>
                    <option value="month" {{ $range === 'month' ? 'selected' : '' }}>This month</option>
                    <option value="all" {{ $range === 'all' ? 'selected' : '' }}>All time</option>
                    <option value="custom" disabled {{ $range === 'custom' ? 'selected' : '' }}>Custom dates</option>
                </select>
            </label>

            <label class="block">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">From</span>
                <input type="date" name="from" value="{{ $from }}" class="mt-1 w-full p-2 border border-gray-200 rounded-lg text-sm text-gray-700">
            </label>

            <label class="block">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">To</span>
                <input type="date" name="to" value="{{ $to }}" class="mt-1 w-full p-2 border border-gray-200 rounded-lg text-sm text-gray-700">
            </label>

            <label class="block">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">Status</span>
                <select name="status" onchange="this.form.submit()" class="mt-1 w-full p-2 border border-gray-200 rounded-lg bg-white text-sm text-gray-700">
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All statuses</option>
                    <option value="open" {{ $status === 'open' ? 'selected' : '' }}>Open (pending, preparing, ready)</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="preparing" {{ $status === 'preparing' ? 'selected' : '' }}>Preparing</option>
                    <option value="ready" {{ $status === 'ready' ? 'selected' : '' }}>Ready</option>
                    <option value="served" {{ $status === 'served' ? 'selected' : '' }}>Served</option>
                    <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </label>

            <label class="block">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">Payment</span>
                <select name="payment" onchange="this.form.submit()" class="mt-1 w-full p-2 border border-gray-200 rounded-lg bg-white text-sm text-gray-700">
                    <option value="all" {{ $payment === 'all' ? 'selected' : '' }}>All payments</option>
                    <option value="unpaid" {{ $payment === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    <option value="paid" {{ $payment === 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </label>

            <label class="block">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">Table</span>
                <select name="table" onchange="this.form.submit()" class="mt-1 w-full p-2 border border-gray-200 rounded-lg bg-white text-sm text-gray-700">
                    <option value="">All tables</option>
                    @foreach ($tables as $table)
                        <option value="{{ $table->id }}" {{ (string) $tableId === (string) $table->id ? 'selected' : '' }}>Table {{ $table->table_number }}</option>
                    @endforeach
                </select>
            </label>

        </div>

        <div class="flex gap-2 mt-3">
            <button type="submit" class="bg-orange-500 text-white font-semibold px-4 py-2 rounded-lg text-sm">Apply</button>
            <a href="{{ route('staff.orders.index') }}" class="border border-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg text-sm">Reset</a>
        </div>
    </form>

    @if (session('success'))
        <p class="bg-green-100 text-green-800 p-3 rounded-xl mb-4">{{ session('success') }}</p>
    @endif

    @php
        $statusColors = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'preparing' => 'bg-blue-100 text-blue-800',
            'ready' => 'bg-purple-100 text-purple-800',
            'served' => 'bg-indigo-100 text-indigo-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
        ];
    @endphp

    @if ($orders->isEmpty())
        <p class="bg-white rounded-xl shadow-md p-6 text-center text-gray-500">No orders match these filters. Try Period: All time.</p>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach ($orders as $order)
            <div class="bg-white rounded-xl shadow-md p-4">

                <div class="flex justify-between items-start mb-3">
                    <div>
                        <p class="text-lg font-bold text-gray-800">Order #{{ $order->id }}</p>
                        <p class="text-sm text-gray-500">Table {{ $order->restaurantTable->table_number }}</p>
                    </div>
                    <div class="flex flex-col items-end gap-1">
                        <span class="text-xs font-semibold px-3 py-1 rounded-full {{ $statusColors[$order->order_status] ?? 'bg-gray-100 text-gray-800' }}">{{ $order->order_status }}</span>
                        <span class="text-xs font-semibold px-3 py-1 rounded-full {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">{{ $order->payment_status }}</span>
                    </div>
                </div>

                <p class="text-xs text-gray-400 mb-3">
                    Placed {{ $order->created_at->format('d M, H:i') }}
                    @if ($order->preparing_at) · Preparing {{ $order->preparing_at->format('H:i') }} @endif
                    @if ($order->ready_at) · Ready {{ $order->ready_at->format('H:i') }} @endif
                    @if ($order->served_at) · Served {{ $order->served_at->format('H:i') }} @endif
                    @if ($order->completed_at) · Completed {{ $order->completed_at->format('H:i') }} @endif
                    @if ($order->paid_at) · Paid {{ $order->paid_at->format('H:i') }} @endif
                    @if (in_array($order->order_status, ['pending', 'preparing', 'ready']))
                        <span class="block mt-1 font-semibold text-orange-600">Waiting {{ abs((int) $order->created_at->diffInMinutes(now())) }} min</span>
                    @endif
                </p>

                {{-- <div class="border-t border-b py-2 mb-3">
                    @foreach ($order->orderItems as $item)
                        <div class="flex justify-between py-1 gap-3 text-sm">
                            <span class="text-gray-800">{{ $item->quantity }}x {{ $item->item_name_snapshot }}</span>
                            <span class="whitespace-nowrap text-gray-600">RM {{ number_format($item->unit_price * $item->quantity, 2) }}</span>
                        </div>
                    @endforeach
                </div> --}}
                                <div class="border-t border-b py-2 mb-3">
                    @foreach ($order->orderItems as $item)
                        <div class="py-1 text-sm">
                            <div class="flex justify-between gap-3">
                                <span class="text-gray-800">{{ $item->quantity }}x {{ $item->item_name_snapshot }}</span>
                                <span class="whitespace-nowrap text-gray-600">RM {{ number_format($item->unit_price * $item->quantity, 2) }}</span>
                            </div>
                            @if ($item->item_note)
                                <p class="text-xs text-orange-600 italic">Note: {{ $item->item_note }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if ($order->customer_name || $order->customer_phone || $order->customer_note)
                    <div class="bg-gray-50 rounded-lg p-2 mb-3 text-xs text-gray-600">
                        @if ($order->customer_name) <p>Name: {{ $order->customer_name }}</p> @endif
                        @if ($order->customer_phone) <p>Phone: {{ $order->customer_phone }}</p> @endif
                        @if ($order->customer_note) <p>Order note: {{ $order->customer_note }}</p> @endif
                    </div>
                @endif

                <div class="flex justify-between font-bold text-gray-800 mb-3">
                    <span>Total</span>
                    <span>RM {{ number_format($order->total_amount, 2) }}</span>
                </div>

                <form action="{{ route('staff.orders.updateStatus', $order->id) }}" method="POST" class="flex gap-2 mb-2">
                    @csrf
                    @method('PUT')
                    <select name="order_status" class="flex-1 p-2 border border-gray-200 rounded-lg bg-white text-sm">
                        <option value="pending" {{ $order->order_status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="preparing" {{ $order->order_status === 'preparing' ? 'selected' : '' }}>Preparing</option>
                        <option value="ready" {{ $order->order_status === 'ready' ? 'selected' : '' }}>Ready</option>
                        <option value="served" {{ $order->order_status === 'served' ? 'selected' : '' }}>Served</option>
                        <option value="completed" {{ $order->order_status === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                    <button type="submit" class="bg-orange-500 text-white font-semibold px-4 py-2 rounded-lg text-sm">Update</button>
                </form>

                 @if ($order->payment_status !== 'paid')
                    <form action="{{ route('staff.orders.markPaid', $order->id) }}" method="POST" class="mb-2">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="w-full bg-green-600 text-white font-semibold px-4 py-2 rounded-lg text-sm">Mark as Paid</button>
                    </form>
                @endif

                <div class="flex gap-2">
                    <a href="{{ route('staff.orders.print', $order->id) }}" target="_blank" class="flex-1 text-center border border-gray-300 text-gray-700 font-semibold px-3 py-2 rounded-lg text-sm">🍳 Kitchen ticket</a>
                    <a href="{{ route('staff.orders.receipt', $order->id) }}" target="_blank" class="flex-1 text-center border border-gray-300 text-gray-700 font-semibold px-3 py-2 rounded-lg text-sm">🧾 Receipt</a>
                </div>

            </div>
        @endforeach
    </div>
</div>
@endsection
