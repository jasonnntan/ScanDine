<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Confirmed</title>
</head>
<body>
    <h1>Order #{{ $order->id }} Confirmed!</h1>
    <p>Thank you, your order has been placed.</p>

    @foreach ($order->orderItems as $item)
        <p>{{ $item->item_name_snapshot }} x {{ $item->quantity }} - RM {{ number_format($item->unit_price, 2) }}</p>
    @endforeach

    <h3>Total: RM {{ number_format($order->total_amount, 2) }}</h3>

    <p>Order Status: {{ $order->order_status }}</p>
</body>
</html>
