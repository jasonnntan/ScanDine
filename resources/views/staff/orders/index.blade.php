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
@endsection
