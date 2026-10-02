<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>DineFlow Menu - Table {{ $table->table_number }}</title>
    @vite(['resources/scss.customer.scss'])
</head>
<body>
    <div class="page-order-menu">
    <h1>Table {{ $table->table_number }}</h1>

    @if (session('success'))
        <p class="success-message">{{ session('success') }}</p>
    @endif

    <input type="text" id="searchBox" placeholder="Search for food or drinks..." onkeyup="filterItems()">

    <script>
    function filterItems() {
        var input = document.getElementById('searchBox').value.toLowerCase();
        var cards = document.getElementsByClassName('food-card');

        for (var i = 0; i < cards.length; i++) {
            var name = cards[i].getElementsByTagName('strong')[0].innerText.toLowerCase();
            if (name.includes(input)) {
                cards[i].style.display = '';
            } else {
                cards[i].style.display = 'none';
            }
        }
    }
    </script>

    @foreach ($categories as $category)
        <h2>{{ $category->name }}</h2>

        @foreach ($category->foodItems as $item)
            <div class="food-card {{ $item->status !== 'available' ? 'out-of-stock' : '' }}">
                @if ($item->image)
                    <img src="{{ asset('storage/' . $item->image) }}">
                @endif
                <strong>{{ $item->name }}</strong> - RM {{ number_format($item->price, 2) }}
                <p>{{ $item->description }}</p>

                @if ($item->status === 'available')
                    <form action="{{ route('order.addToCart', $table->qr_token) }}" method="POST">
                        @csrf
                        <input type="hidden" name="food_item_id" value="{{ $item->id }}">
                        <input type="number" name="quantity" value="1" min="1">
                        <button type="submit" class="btn">Add to Cart</button>
                    </form>
                @else
                    <p><em>Out of stock</em></p>
                @endif
            </div>
        @endforeach
    @endforeach
    </div>
</body>
</html>
