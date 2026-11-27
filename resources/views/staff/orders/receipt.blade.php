@php
    $displayNumber = $order->daily_order_number ?? $order->id;
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt - Order #{{ $displayNumber }}</title>
    <style>
        body { font-family: monospace; width: 280px; margin: 0 auto; font-size: 13px; }
        h2 { text-align: center; margin-bottom: 4px; }
        hr { border: 0; border-top: 1px dashed #000; }
        .row { display: flex; justify-content: space-between; gap: 8px; }
        .total { font-weight: bold; font-size: 15px; }
        .center { text-align: center; }
    </style>
</head>
<body onload="window.print()">
    <h2>{{ $setting->restaurant_name }}</h2>
    <p class="center">Payment receipt</p>

    <p>
        Order #{{ $displayNumber }}<br>
        Table {{ $order->restaurantTable->table_number }}<br>
        {{ $order->created_at->format('d/m/Y H:i') }}
    </p>
    <hr>

    @foreach ($order->orderItems as $item)
        <div class="row">
            <span>{{ $item->quantity }}x {{ $item->item_name_snapshot }}</span>
            <span>{{ number_format($item->unit_price * $item->quantity, 2) }}</span>
        </div>
    @endforeach
    <hr>

    <div class="row"><span>Subtotal</span><span>{{ number_format($subtotal, 2) }}</span></div>

    @if ($setting->service_charge_enabled)
        <div class="row"><span>Service charge ({{ $setting->service_charge_rate }}%)</span><span>{{ number_format($serviceCharge, 2) }}</span></div>
    @endif

    @if ($setting->sst_enabled)
        <div class="row"><span>SST ({{ $setting->sst_rate }}%)</span><span>{{ number_format($sst, 2) }}</span></div>
    @endif
    <hr>

    <div class="row total"><span>TOTAL (RM)</span><span>{{ number_format($grandTotal, 2) }}</span></div>
    <hr>

    <p class="center">{{ $order->payment_status === 'paid' ? 'PAID' : 'UNPAID' }}</p>
    <p class="center">Thank you!</p>
</body>
</html>
