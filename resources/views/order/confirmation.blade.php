<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Confirmed</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="max-w-md mx-auto p-4">

        <div class="bg-white rounded-2xl shadow-md p-6 text-center mb-4">
            <div class="=mx-auto mb-2 w-14 h-14 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-3xl">✓</div>
            <h1 class="text-2xl font-bold text-gray-800">Order #{{ $order->id }} Confirmed!</h1>
            <p class="text-gray-500 mt-1">Thank you, your order has been placed.</p>
            <span class="inline-block mt-3 bg-yellow-100 text-yellow-800 text-sm font-semibold px-3 py-1 rounded-full">{{ $order->order_status }}</span>
        </div>

    <div class="bg-white rounded-2xl shadow-md p-4 mb-4">
    <h2 class="font-bold text-gray-700 mb-2">Your order</h2>
        @foreach ($order->orderItems as $item)
            <div class="flex justify-between py-2 border-b last:border-b-0 gap-3">
                <span class="text-gray-800">{{ $item->quantity }}x {{ $item->item_name_snapshot }}</span>
                <span class="font-semibold whitespace-nowrap">RM {{ number_format($item->unit_price * $item->quantity, 2) }}</span>
            </div>
        @endforeach

    <div class="flex justify-between font-bold text-lg pt-3 mt-1 border-t">
                <span>Total</span>
                <span>RM {{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>

        <a href="{{ route('order.menu', $order->restaurantTable->qr_token) }}" class="block w-full text-center bg-orange-500 text-white font-bold py-3 rounded-full">Order more</a>

    </div>
</body>
</html>
