<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DineFlow Menu - Table {{ $table->table_number }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="max-w-md mx-auto p-4">
            <div class="relative">
            <img src="{{ asset('images/asa2.jpg') }}" class="w-full h-40 object-cover rounded-xl">
                <div class="absolute bottom-0 left-0 right-0 bg-black/50 px-4 py-2">
                </div>
            </div>

        <div class="sticky top-0 z-10 bg-white shadow-md p-4 rounded-b-xl">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">The Restaurant</h1>
            <span class="bg-gray-100 text-gray-600 text-sm font-semibold px-3 py-1 rounded-full">Table {{ $table->table_number }}</span>

        {{-- dropdown and filter --}}
            <div class="flex gap-3">
                <select id="categoryFilter" onchange="filterItems()" class="w-full mt-4 p-3 border border-gray-200 rounded-xl bg-white text-gray-700 font-medium focus:outline-none focus:ring-2 focus:ring-red-800 appearance-none">
                    <option value="">All Categories</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->name }}" data-id="{{ $cat->id }}">{{ $cat->name }}</option>
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
        <h2 id="cat-{{ $category->id }}" class="text-xl font-semibold text-gray-700 mt-8 mb-4">{{ $category->name }}</h2>
        <div class="grid grid-cols-1 gap-4">
        @foreach ($category->foodItems as $item)
            <div class="food-card bg-white rounded-xl shadow-md p-4 {{ $item->status !== 'available' ? 'out-of-stock' : '' }}" data-category="{{ $category->name }}">
                <div class="flex flex-row gap-4">
                @if ($item->image)
                    <img src="{{ asset('storage/' . $item->image) }}" class="w-24 h-24 flex-shrink-0 object-cover rounded-lg">
                @endif
                <div class="flex-1 min-w-0 flex flex-col justify-between">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-1 mb-1">
                        <strong class="text-base sm:text-lg font-bold text-gray-800 leading-snug break-words">{{ $item->name }}</strong>
                        <span class="text-red-800 font-extrabold text-sm sm:text-base whitespace-nowrap">RM {{ number_format($item->price, 2) }}</span>
                    </div>
                    <p class="text-gray-400 text-xs sm:text-sm line-clamp-2 mb-2">{{ $item->description }}</p>

                @if ($item->status === 'available')
                    <div class="flex gap-2 items-center">
                        <input type="number" class="qty-input w-14 p-2 border border-gray-200 rounded-lg" value="1" min="1">
                        <button type="button" onclick="addToCartAjax({{ $item->id }}, this)" class="bg-red-800 text-white font-semibold py-2 px-3 rounded-xl text-sm whitespace-nowrap">Add to cart</button>
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
    <div id="floatingCartBar" class="fixed bottom-6 inset-x-0 px-4 hidden z-50 pointer-events-none">
        <div class="max-w-md mx-auto px-4 pointer-events-auto">
            <!-- The single brown floating action button -->
            <button onclick="openCartModal()" class="w-full bg-orange-500 text-white font-bold py-4 px-6 rounded-2xl flex items-center justify-between shadow-2xl transition-transform active:scale-[0.98]">

                <!-- Left element: Item Count -->
                <span id="cartCount" class="text-lg font-extrabold min-w-[32px] text-left">0</span>

                <!-- Middle element: Action Text -->
                <span class="text-base font-extrabold tracking-wide">Review order</span>

                <!-- Right element: Price Display -->
                <span id="cartTotal" class="text-lg font-extrabold text-right">RM 0.00</span>

            </button>
        </div>
    </div>

    {{-- modal --}}
    <div id="cartModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-md max-h-[80vh] overflow-y-auto">
            <div class="flex justify-between items-center p-4 border-b">
                <h2 class="text-lg font-bold">Confirm Order</h2>
                <button onclick="closeCartModal()" class="text-2xl text-gray-400">&times;</button>
            </div>
            <div id="cartModalItems" class="p-4">
            </div>
            <div class="p-4 border-t">
                <div class="flex justify-between font-bold text-lg mb-4">
                    <span>Total</span>
                    <span id="cartModalTotal">RM 0.00</span>
                </div>
                <button onclick="confirmOrder()" class="w-full bg-orange-500 text-white font-bold py-3 rounded-full">Confirm Order</button>
            </div>
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

    window.addEventListener('scroll', function(){
        var headings = document.querySelectorAll('h2[id^="cat-"]');
        var currentCategoryId = null;

        headings.forEach(function(heading){
            var rect = heading.getBoundingClientRect();
            if (rect.top <= 150){
                currentCategoryId = heading.id.replace('cat-', '');
            }
        });

        if (currentCategoryId){
            var dropdown = document.getElementById('categoryFilter');
            var options = dropdown.querySelectorAll('option');
            options.forEach(function(opt){
                if(opt.dataset.id === currentCategoryId){
                    dropdown.value = opt.value;
                }
            });
        }
    });

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

            var totalPrice = 0;
            for (var key in cart) {
                totalPrice += cart[key].price * cart[key].quantity;
            }
            document.getElementById('cartTotal').innerText = 'RM ' + totalPrice.toFixed(2);
            document.getElementById('floatingCartBar').classList.remove('hidden');
        });
    }

    function openCartModal(){
        fetch("{{ route('order.viewCartJson', $table->qr_token) }}")
            .then(response => response.json())
            .then(data => {
                var cart = data.cart;
                var itemsHtml = '';
                var total = 0;

                // for (var key in cart) {
                //     var item = cart[key];
                //     var subtotal = item.price * item.quantity;
                //     total += subtotal;
                //     itemsHtml += '<div class="flex items-center justify-between py-3 border-b">' +
                //         '<div class="flex items-center gap-2 flex-1">' +
                //             '<span class="bg-orange-500 text-white font-bold text-sm rounded-full w-8 h-8 flex items-center justify-center flex-shrink-0">' + item.quantity + 'x</span>' +
                //             '<span class="text-gray-800">' + item.name + '</span>' +
                //         '</div>' +
                //         '<button onclick="removeFromCart(' + key + ')" class="text-gray-400 hover:text-red-600 px-2">🗑️</button>' +
                //         '<span class="font-semibold ml-2">RM ' + subtotal.toFixed(2) + '</span>' +
                //         '</div>';
                // }

                for (var key in cart) {
                    var item = cart[key];
                    var subtotal = item.price * item.quantity;
                    total += subtotal;
                    itemsHtml += '<div class="flex items-start justify-between py-3 border-b gap-3">' +
                        '<div class="flex-1">' +
                            '<div class="text-gray-800">' + item.name + '</div>' +
                            '<div class="flex items-center gap-2 mt-2">' +
                                '<button onclick="changeQuantity(' + key + ', ' + (item.quantity - 1) + ')" class="w-8 h-8 rounded-full bg-gray-200 font-bold">−</button>' +
                                '<span class="bg-orange-500 text-white font-bold text-sm rounded-full min-w-[2rem] h-8 px-2 flex items-center justify-center">' + item.quantity + 'x</span>' +
                                '<button onclick="changeQuantity(' + key + ', ' + (item.quantity + 1) + ')" class="w-8 h-8 rounded-full bg-gray-200 font-bold">+</button>' +
                                '<button onclick="removeFromCart(' + key + ')" class="text-gray-400 hover:text-red-600 px-2">🗑️</button>' +
                            '</div>' +
                        '</div>' +
                        '<span class="font-semibold whitespace-nowrap">RM ' + subtotal.toFixed(2) + '</span>' +
                        '</div>';
                }

            document.getElementById('cartModalItems').innerHTML = itemsHtml;
            document.getElementById('cartModalTotal').innerText = 'RM ' + total.toFixed(2);
            document.getElementById('cartModal').classList.remove('hidden');
            document.getElementById('cartModal').classList.add('flex');
            });
    }

    function removeFromCart(foodItemId) {
        fetch("{{ route('order.removeFromCart', $table->qr_token) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                food_item_id: foodItemId
            })
        })
        .then(response => response.json())
        .then(data => {
            openCartModal();

            var totalCount = 0;
            var totalPrice = 0;
            for (var key in data.cart) {
                totalCount += data.cart[key].quantity;
                totalPrice += data.cart[key].price * data.cart[key].quantity;
            }
            document.getElementById('cartCount').innerText = totalCount;
            document.getElementById('cartTotal').innerText = 'RM ' + totalPrice.toFixed(2);

            if (totalCount === 0) {
                document.getElementById('floatingCartBar').classList.add('hidden');
                closeCartModal();
            }
        });
    }

    function closeCartModal(){
        document.getElementById('cartModal').classList.add('hidden');
        document.getElementById('cartModal').classList.remove('flex');
    }

    function confirmOrder(){
        fetch("{{ route('order.placeOrder', $table->qr_token) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
        })
        .then(response => response.json())
        .then(data => {
            // console.log('Order place:', data);
            window.location.href = '/order/confirmation/' + data.orderId;
        });
    }

    function changeQuantity(foodItemId, newQuantity) {
        fetch("{{ route('order.updateCartQuantity', $table->qr_token) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                food_item_id: foodItemId,
                quantity: newQuantity
            })
        })
        .then(response => response.json())
        .then(data => {
            var totalCount = 0;
            var totalPrice = 0;
            for (var key in data.cart) {
                totalCount += data.cart[key].quantity;
                totalPrice += data.cart[key].price * data.cart[key].quantity;
            }
            document.getElementById('cartCount').innerText = totalCount;
            document.getElementById('cartTotal').innerText = 'RM ' + totalPrice.toFixed(2);

            if (totalCount === 0) {
                document.getElementById('floatingCartBar').classList.add('hidden');
                closeCartModal();
            } else {
                openCartModal();
            }
        });
    }
    </script>
</body>
</html>
