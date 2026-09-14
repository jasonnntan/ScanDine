<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>DineFlow Menu - Table {{ $table->table_number }}</title>
</head>
<body>
    <h1>Table {{ $table->table_number }}</h1>
    @if (session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    @foreach ($categories as $category)
        <h2>{{ $category->name }}</h2>

        @foreach ($category->foodItems as $item)
            <div>
                <strong>{{ $item->name }}</strong> - RM {{ number_format($item->price, 2) }}
                <p>{{ $item->description }}</p>
                <p>Status: {{ $item->status }}</p>

                    @if ($item->status === 'available')
                        <form action="{{ route('order.addToCart', $table->qr_token) }}" method="POST">
                            @csrf
                            <input type="hidden" name="food_item_id" value="{{ $item->id }}">
                            <input type="number" name="quantity" value="1" min="1">
                            <button type="submit">Add to Cart</button>
                        </form>
                    @else
                        <p><em>Out of stock</em></p>
                    @endif
            </div>
        @endforeach
    @endforeach
</body>
</html>

//not yet understand
