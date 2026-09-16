<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Your Cart - Table {{ $table->table_number }}</title>
</head>
<body>
    <h1>Your Cart - Table {{ $table->table_number }}</h1>

    @if (count($cart) === 0)
        <p>Your cart is empty.</p>
    @else
        @foreach ($cart as $foodItemId => $item)
            <div>
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
            <button type="submit">Place Order</button>
        </form>
    @endif

    <a href="{{ route('order.menu', $table->qr_token) }}">Back to Menu</a>
</body>
</html>
