<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Staff - Orders</title>
</head>
<body>
    <h1>All Orders</h1>
    @if (session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    @foreach ($orders as $order)
        <div style="border: 1px solid #ccc; margin-bottom: 15px; padding: 10px;">
            <strong>Order #{{ $order->id }}</strong> - Table {{ $order->restaurantTable->table_number }}
            <p>Status: {{ $order->order_status }}</p>
            <p>Payment: {{ $order->payment_status }}</p>

            @foreach ($order->orderItems as $item)
                <p>{{ $item->item_name_snapshot }} x {{ $item->quantity }} - RM {{ number_format($item->unit_price, 2) }}</p>
            @endforeach

            <p><strong>Total: RM {{ number_format($order->total_amount, 2) }}</strong></p>

            {{-- form --}}
            <form action="{{ route('staff.orders.updateStatus', $order->id) }}" method="POST">
                @csrf
                @method('PUT')
                <select name="order_status">
                    <option value="pending" {{ $order->order_status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="preparing" {{ $order->order_status === 'preparing' ? 'selected' : '' }}>Preparing</option>
                    <option value="ready" {{ $order->order_status === 'ready' ? 'selected' : '' }}>Ready</option>
                    <option value="served" {{ $order->order_status === 'served' ? 'selected' : '' }}>Served</option>
                    <option value="completed" {{ $order->order_status === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
                <button type="submit">Update Status</button>
            </form>

            @if ($order->payment_status !== 'paid')
                <form action="{{ route('staff.orders.markPaid', $order->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <button type="submit">Mark as Paid</button>
                </form>
            @endif
        </div>
    @endforeach
</body>
</html>
