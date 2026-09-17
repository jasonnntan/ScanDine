<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Staff - Orders</title>
</head>
<body>
    <h1>All Orders</h1>

    @foreach ($orders as $order)
        <div style="border: 1px solid #ccc; margin-bottom: 15px; padding: 10px;">
            <strong>Order #{{ $order->id }}</strong> - Table {{ $order->restaurantTable->table_number }}
            <p>Status: {{ $order->order_status }}</p>
            <p>Payment: {{ $order->payment_status }}</p>

            @foreach ($order->orderItems as $item)
                <p>{{ $item->item_name_snapshot }} x {{ $item->quantity }} - RM {{ number_format($item->unit_price, 2) }}</p>
            @endforeach

            <p><strong>Total: RM {{ number_format($order->total_amount, 2) }}</strong></p>
        </div>
    @endforeach
</body>
</html>
