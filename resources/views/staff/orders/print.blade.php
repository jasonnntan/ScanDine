<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kitchen ticket - Order #{{ $order->id }}</title>
    <style>
        body { font-family: monospace; width: 280px; margin: 0 auto; font-size: 14px; }
        h2 { text-align: center; margin-bottom: 2px; }
        hr { border: 0; border-top: 1px dashed #000; }
        .center { text-align: center; }
        .table-no { text-align: center; font-size: 28px; font-weight: bold; margin: 6px 0; }
        .item { font-size: 16px; font-weight: bold; margin: 8px 0 2px; }
        .note { font-size: 13px; margin: 0 0 6px 18px; }
    </style>
</head>
<body onload="window.print()">
    <h2>KITCHEN</h2>
    <p class="center">Order #{{ $order->id }}</p>
    <p class="table-no">TABLE {{ $order->restaurantTable->table_number }}</p>
    <p class="center">{{ $order->created_at->format('d/m/Y H:i') }}</p>
    <hr>

    @foreach ($order->orderItems as $item)
        <p class="item">{{ $item->quantity }}x {{ $item->item_name_snapshot }}</p>
        @if ($item->item_note)
            <p class="note">Note: {{ $item->item_note }}</p>
        @endif
    @endforeach
    <hr>

    @if ($order->customer_note)
        <p><strong>Order note:</strong> {{ $order->customer_note }}</p>
        <hr>
    @endif

    <p class="center">{{ $order->orderItems->sum('quantity') }} item(s) in this order</p>
</body>
</html>
