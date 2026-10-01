<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Confirmed</title>
    @vite(['resources/scss/app.scss'])
</head>
<body>
    <div class="food-card" style="text-align: center;">
        <h1>Order #{{ $order->id }} Confirmed!</h1>
        <p>Thank you, your order has been placed.</p>
    </div>

    @foreach ($order->orderItems as $item)
        <div class="food-card">
            {{ $item->item_name_snapshot }} x {{ $item->quantity }} - RM {{ number_format($item->unit_price, 2) }}
        </div>
    @endforeach

    <h3>Total: RM {{ number_format($order->total_amount, 2) }}</h3>

    <p>Order Status: <span class="badge badge-pending">{{ $order->order_status }}</span></p>
</body>
</html>
