<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DineFlow Menu - Table {{ $table->table_number }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="max-w-4xl mx-auto p-4 md:p-6">
            <div class="relative">
            <img src="{{ asset('images/banner.jpg') }}" class="w-full h-40 object-cover rounded-xl">
                <div class="absolute bottom-0 left-0 right-0 bg-black/50 px-4 py-2">
                </div>
            </div>

        <div class="sticky top-0 z-10 bg-white shadow-md p-4 rounded-b-xl">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">The Restaurant</h1>
            <span class="bg-gray-100 text-gray-600 text-sm font-semibold px-3 py-1 rounded-full">Table {{ $table->table_number }}</span>

        {{-- dropdown and filter --}}
            <div class="flex gap-3">
                <select id="categoryFilter" onchange="filterItems()" class="w-full mt-4 ">
                    <option value="">All Categories</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                        @endforeach
                </select>
                    {{-- <input type="text" id="searchBox" placeholder="Search for food or drinks..." onkeyup="filterItems()"
                    class="w-full p-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary"> --}}
            </div>
        </div>

    @if (session('success'))
        <p class="bg-primary text-white p-3 rounded-xl mb-4 shadow">{{ session('success') }}</p>
    @endif



    @foreach ($categories as $category)
        <h2 class="text-xl font-semibold text-gray-700 mt-8 mb-4">{{ $category->name }}</h2>
        <div class="grid grid-cols-1 gap-4">
        @foreach ($category->foodItems as $item)
            <div class="food-card bg-white rounded-xl shadow-md p-4 {{ $item->status !== 'available' ? 'out-of-stock' : '' }}" data-category="{{ $category->name }}">
                <div class="flex flex-row gap-4">
                @if ($item->image)
                    <img src="{{ asset('storage/' . $item->image) }}" class="w-24 h-24 flex-shrink-0 object-cover rounded-lg">
                @endif
                <div class="flex-1">
                <div class="flex justify-between items-center mb-2">
                <strong class="text-lg font-semibold text-gray-800">{{ $item->name }}</strong>
                <span class="text-primary font-bold">RM {{ number_format($item->price, 2) }}</span>
                </div>
                <p class="text-gray-500 text-sm mb-4">{{ $item->description }}</p>

                @if ($item->status === 'available')
                    <div class="flex gap-3 items-center">
                        <input type="number" class="qty-input w-20 p-2 border border-gray-200 rounded-lg" value="1" min="1">
                        <button type="button" onclick="addToCartAjax({{ $item->id }}, this)" class="bg-red-800 text-white font-semibold py-2 px-4 rounded-xl">Add to cart</button>
                    </div>
                @else
                    <p class="text-red-500 font-semibold">Out of stock</p>
                @endif
                </div>
            </div>
        </div>
        @endforeach
        </div>
    @endforeach
    </div>

    {{-- floating button --}}
    <div id="floatingCartBar" class="fixed bottom-0 left-0 right-0 bg-white shadow-lg p-4 hidden">
        <div class="max-w-4xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-2xl">🛒</span>
                <span id="cartCount" class="bg-red-600 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center">0</span>
            </div>
            <button class="bg-orange-500 text-white font-bold py-3 px-6 rounded-full">Review My Order</button>
        </div>
    </div>

    <script>
    function filterItems() {
        var selectedCategory = document.getElementById('categoryFilter').value;
        var cards = document.getElementsByClassName('food-card');

        for (var i = 0; i < cards.length; i++) {
            var name = cards[i].getElementsByTagName('strong')[0].innerText.toLowerCase();
            var cardCategory = cards[i].dataset.category;
            var matchesCategory = selectedCategory === '' || cardCategory === selectedCategory;

            if (matchesCategory) {
                cards[i].style.display = '';
            } else {
                cards[i].style.display = 'none';
            }
        }
    }

    function addToCartAjax(foodItemId, button){
        var quantity = parseInt(button.previousElementSibling.value);

        fetch("{{ route('order.addToCart', $table->qr_token) }}",{
            method: 'POST',
            headers:{
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                food_item_id: foodItemId,
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data =>{
            var cart = data.cart;
            var totalCount = 0;
            for (var key in cart){
                totalCount += cart[key].quantity;
            }
            document.getElementById('cartCount').innerText = totalCount;
            document.getElementById('floatingCartBar').classList.remove('hidden');
        });
    }
    </script>
</body>
</html>
