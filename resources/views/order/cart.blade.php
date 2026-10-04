<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Your Cart - Table {{ $table->table_number }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

    <div class="page-order-cart">
    <h1>Your Cart - Table {{ $table->table_number }}</h1>

    @if (count($cart) === 0)
        <p>Your cart is empty.</p>
    @else
        @foreach ($cart as $foodItemId => $item)
            <div class="food-card">
                <strong>{{ $item['name'] }}</strong> - RM {{ number_format($item['price'], 2) }} x {{ $item['quantity'] }}
                = RM {{ number_format($item['price'] * $item['quantity'], 2) }}
            </div>
        @endforeach

        <h3>Total: RM {{ number_format($total, 2) }}</h3>

        <form action="{{ route('order.placeOrder', $table->qr_token) }}" method="POST">
            @csrf
            <label>Name (optional): <input type="text" name="customer_name"></label><br>
            <label>Phone (optional): <input type="text" name="customer_phone"></label><br>
            <label>Note (optional): <textarea name="customer_note"></textarea></label><br>
            <button type="submit" class="btn">Place Order</button>
        </form>
    @endif

    <a href="{{ route('order.menu', $table->qr_token) }}">Back to Menu</a>
    </div>
</body>
</html>
