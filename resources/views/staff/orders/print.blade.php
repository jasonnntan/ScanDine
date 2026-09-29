<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order #{{ $order->id }} Ticket</title>
    <style>
        body { font-family: monospace; width: 280px; margin: 0 auto; }
        h2 { text-align: center; }
        hr { border-top: 1px dashed #000; }
    </style>
</head>
<body onload="window.print()">
    <h2>DineFlow</h2>
    <p>Order #{{ $order->id }}<br>
    Table {{ $order->restaurantTable->table_number }}<br>
    {{ $order->created_at->format('d/m/Y H:i') }}</p>
    <hr>
    @foreach ($order->orderItems as $item)
        <p>{{ $item->quantity }}x {{ $item->item_name_snapshot }}
        @if ($item->item_note) <br><em>Note: {{ $item->item_note }}</em> @endif
        </p>
    @endforeach
    <hr>
    <p><strong>Total: RM {{ number_format($order->total_amount, 2) }}</strong></p>
    @if ($order->customer_note)
        <p>Customer Note: {{ $order->customer_note }}</p>
    @endif
</body>
</html>
