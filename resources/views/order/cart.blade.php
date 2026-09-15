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
        @foreach ($cart as $foodItem => $item)
            <div>
                <strong>{{ $item['name'] }}</strong> - RM {{ number_format($item['price'], 2) }} x {{ $item['quantity'] }}
                = RM {{ number_format($item['price'] * $item['quantity'], 2) }}
            </div>
        @endforeach

        <h3>Total: RM {{ number_format($total, 2) }}</h3>
    @endif

    <a href="{{ route('order.menu', $table->qr_token) }}">Back to Menu</a>
</body>
</html>
