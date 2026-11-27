<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $setting->restaurant_name }} — Table {{ $table->table_number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-stone-50 text-stone-800 antialiased selection:bg-orange-100 selection:text-orange-900">
    @php
        $initialCart = $cart ?? session()->get('cart', []);
    @endphp

    {{-- Toast Notification --}}
    <div id="toast" class="fixed top-4 left-1/2 -translate-x-1/2 z-[60] pointer-events-none opacity-0 -translate-y-3 transition-all duration-200">
        <div id="toastBox" class="bg-stone-900 text-white text-xs sm:text-sm font-semibold px-4 py-2.5 rounded-full shadow-lg flex items-center gap-2 whitespace-nowrap">
            <span id="toastIcon">✓</span>
            <span id="toastMessage">Added to cart</span>
        </div>
    </div>

    <div class="max-w-lg mx-auto min-h-screen bg-stone-50 pb-32 overflow-x-clip">
        {{-- Hero Banner --}}
        <div class="relative h-44 sm:h-48 w-full overflow-hidden bg-gradient-to-br from-red-900 via-red-800 to-orange-700">
            @if ($setting->banner_image)
                <img src="{{ asset('storage/' . $setting->banner_image) }}" loading="lazy" decoding="async" alt="{{ $setting->restaurant_name }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/30 to-transparent"></div>
            @endif

            <div class="absolute bottom-0 inset-x-0 p-4 sm:p-5 flex items-end justify-between gap-3">
                <div>
                    <span class="inline-flex items-center gap-1.5 bg-white/20 backdrop-blur-md text-white text-xs font-semibold px-2.5 py-1 rounded-full mb-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Dine-In Ordering
                    </span>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight leading-tight">
                        {{ $setting->restaurant_name }}
                    </h1>
                </div>
                <div class="bg-white text-stone-900 px-3.5 py-2 rounded-2xl shadow-md text-center flex-shrink-0">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-stone-400">Table</span>
                    <span class="text-base font-extrabold text-red-800 leading-none">{{ $table->table_number }}</span>
                </div>
            </div>
        </div>

        {{-- Sticky Search & Category Pills --}}
        <div class="sticky top-0 z-20 bg-white/95 backdrop-blur-md border-b border-stone-200/80 shadow-sm">
            <div class="px-4 pt-3 pb-2">
                <div class="relative">
                    <svg class="w-4 h-4 text-stone-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" id="menuSearch" oninput="filterMenu()" placeholder="Search dishes or ingredients..." class="w-full pl-10 pr-9 py-2.5 bg-stone-100 border border-transparent focus:bg-white focus:border-red-800 rounded-xl text-sm text-stone-800 placeholder-stone-400 focus:outline-none transition">
                    <button type="button" id="clearSearchBtn" onclick="clearSearch()" class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-600 text-sm font-bold">✕</button>
                </div>
            </div>

            {{-- Horizontal Category Tabs --}}
            <div id="categoryTabs" class="flex items-center gap-2 px-4 pb-3 overflow-x-auto no-scrollbar">
                <button type="button" onclick="selectCategory('all', this)" data-cat-btn="all" class="cat-pill active-pill px-3.5 py-1.5 rounded-full text-xs font-bold whitespace-nowrap bg-red-800 text-white shadow-sm transition">
                    All Menu
                </button>
                @foreach ($categories as $cat)
                    @if ($cat->foodItems->count() > 0)
                        <button type="button" onclick="selectCategory('{{ $cat->id }}', this)" data-cat-btn="{{ $cat->id }}" class="cat-pill px-3.5 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap bg-stone-100 text-stone-600 hover:bg-stone-200 transition">
                            {{ $cat->name }}
                        </button>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- Flash Message --}}
        @if (session('success'))
            <div class="mx-4 mt-4 bg-emerald-600 text-white text-sm font-semibold px-4 py-3 rounded-xl shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Empty Search State --}}
        <div id="emptySearchState" class="hidden px-6 py-16 text-center">
            <div class="w-12 h-12 rounded-full bg-stone-200/70 text-stone-500 flex items-center justify-center mx-auto mb-3 text-xl">🍽️</div>
            <p class="font-bold text-stone-700">No matching dishes found</p>
            <p class="text-xs text-stone-400 mt-1">Try a different keyword or clear your search filter.</p>
            <button type="button" onclick="clearSearch()" class="mt-4 px-4 py-2 bg-stone-800 text-white text-xs font-bold rounded-full">Reset Search</button>
        </div>

        {{-- Menu Sections --}}
        <div class="px-4">
            @foreach ($categories as $category)
                @if ($category->foodItems->count() > 0)
                    <section class="category-section mt-6" data-section-id="{{ $category->id }}">
                        <div class="flex items-center justify-between mb-3">
                            <h2 id="cat-{{ $category->id }}" class="text-base sm:text-lg font-extrabold text-stone-800 tracking-tight scroll-mt-32">
                                {{ $category->name }}
                            </h2>
                            <span class="text-xs font-medium text-stone-400">{{ $category->foodItems->count() }} items</span>
                        </div>

                        <div class="grid grid-cols-1 gap-3.5">
                            @foreach ($category->foodItems as $item)
                                @php
                                    $isAvailable = $item->status === 'available';
                                @endphp
                                <div class="food-card bg-white rounded-2xl border border-stone-200/80 p-3.5 shadow-sm transition hover:shadow-md {{ !$isAvailable ? 'opacity-70' : '' }}"
                                     data-category-id="{{ $category->id }}"
                                     data-name="{{ strtolower($item->name) }}"
                                     data-desc="{{ strtolower($item->description ?? '') }}">
                                    <div class="flex gap-3.5">
                                        {{-- Food Image or Placeholder --}}
                                        <div class="relative w-24 h-24 sm:w-28 sm:h-28 flex-shrink-0 rounded-xl overflow-hidden bg-stone-100">
                                            @if ($item->image)
                                                <img src="{{ asset('storage/' . $item->image) }}" loading="lazy" alt="{{ $item->name }}" class="w-full h-full object-cover {{ !$isAvailable ? 'grayscale' : '' }}">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-stone-300 text-2xl">🍲</div>
                                            @endif

                                            @if (!$isAvailable)
                                                <div class="absolute inset-0 bg-black/45 flex items-center justify-center">
                                                    <span class="bg-stone-900/90 text-white text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md">Sold Out</span>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Item Details & Action --}}
                                        <div class="flex-1 min-w-0 flex flex-col justify-between">
                                            <div>
                                                <h3 class="text-sm sm:text-base font-bold text-stone-800 leading-snug line-clamp-2">
                                                    {{ $item->name }}
                                                </h3>
                                                @if ($item->description)
                                                    <p class="text-stone-400 text-xs line-clamp-2 mt-1 leading-relaxed">
                                                        {{ $item->description }}
                                                    </p>
                                                @endif
                                            </div>

                                            <div class="flex items-center justify-between gap-2 mt-3 pt-1">
                                                <span class="text-red-800 font-extrabold text-sm sm:text-base">
                                                    RM {{ number_format($item->price, 2) }}
                                                </span>

                                                @if ($isAvailable)
                                                    <div id="item-action-{{ $item->id }}" class="flex items-center">
                                                        <button type="button" onclick="addToCartAjax({{ $item->id }}, 1)" class="bg-red-800 hover:bg-red-900 active:scale-95 text-white font-bold text-xs px-3.5 py-2 rounded-xl shadow-sm transition flex items-center gap-1">
                                                            <span class="text-sm leading-none">+</span> Add
                                                        </button>
                                                    </div>
                                                @else
                                                    <span class="text-xs font-semibold text-stone-400 bg-stone-100 px-2.5 py-1 rounded-lg">Unavailable</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif
            @endforeach
        </div>
    </div>

    {{-- Floating Cart Bar --}}
    <div id="floatingCartBar" class="fixed bottom-5 inset-x-0 px-4 hidden z-40 pointer-events-none">
        <div class="max-w-lg mx-auto pointer-events-auto">
            <button type="button" onclick="openCartModal()" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-3.5 px-5 rounded-2xl flex items-center justify-between shadow-xl shadow-orange-900/20 transition-transform active:scale-[0.98]">
                <div class="flex items-center gap-2.5">
                    <span id="cartCount" class="bg-white text-orange-600 text-xs font-extrabold min-w-[26px] h-[26px] px-1.5 rounded-full flex items-center justify-center">0</span>
                    <span class="text-sm sm:text-base font-extrabold tracking-tight">View Order</span>
                </div>
                <span id="cartTotal" class="text-sm sm:text-base font-extrabold">RM 0.00</span>
            </button>
        </div>
    </div>

    {{-- Checkout Bottom Sheet Modal --}}
    <div id="cartModal" onclick="handleBackdropClick(event)" class="fixed inset-0 bg-stone-950/60 backdrop-blur-[2px] z-50 hidden items-end sm:items-center justify-center">
        <div class="bg-white rounded-t-3xl sm:rounded-3xl w-full max-w-lg max-h-[88vh] flex flex-col shadow-2xl overflow-hidden">
            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-stone-100">
                <div>
                    <h2 class="text-lg font-extrabold text-stone-800">Your Order</h2>
                    <p class="text-xs text-stone-400">Table {{ $table->table_number }} • Review items & notes</p>
                </div>
                <button type="button" onclick="closeCartModal()" class="w-8 h-8 rounded-full bg-stone-100 hover:bg-stone-200 text-stone-500 flex items-center justify-center text-lg font-bold transition">&times;</button>
            </div>

            {{-- Scrollable Body --}}
            <div class="overflow-y-auto flex-1 px-5 py-3">
                <div id="cartModalItems" class="divide-y divide-stone-100"></div>

                {{-- Customer Optional Info --}}
                <div class="mt-4 pt-4 border-t border-stone-100 space-y-2.5">
                    <p class="text-xs font-bold uppercase tracking-wider text-stone-400">Optional Details</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <input type="text" id="customerName" maxlength="100" placeholder="Your name (optional)" class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm focus:outline-none">
                        <input type="tel" id="customerPhone" maxlength="20" placeholder="Phone number (optional)" class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm focus:outline-none">
                    </div>
                    <textarea id="customerNote" rows="2" maxlength="500" placeholder="Kitchen note for the whole order (e.g. serve drinks first)" class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm focus:outline-none"></textarea>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="p-5 bg-stone-50 border-t border-stone-200/80">
                <div class="flex justify-between items-center mb-3.5">
                    <span class="text-sm font-semibold text-stone-500">Total Amount</span>
                    <span id="cartModalTotal" class="text-xl font-extrabold text-stone-900">RM 0.00</span>
                </div>
                <button type="button" id="placeOrderBtn" onclick="confirmOrder()" class="w-full bg-orange-600 hover:bg-orange-700 disabled:opacity-60 text-white font-extrabold py-3.5 rounded-2xl shadow-lg shadow-orange-600/20 transition active:scale-[0.99]">
                    Place Order
                </button>
            </div>
        </div>
    </div>

    <script>
        var currentCart = @json($initialCart);
        var itemNotes = {};
        var selectedCategoryId = 'all';
        var placing = false;
        var toastTimer = null;

        function escapeHtml(text) {
            var div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function showToast(message, isError) {
            var toast = document.getElementById('toast');
            var box = document.getElementById('toastBox');
            document.getElementById('toastIcon').textContent = isError ? '!' : '✓';
            document.getElementById('toastMessage').textContent = message;

            box.className = (isError ? 'bg-red-700' : 'bg-stone-900') +
                ' text-white text-xs sm:text-sm font-semibold px-4 py-2.5 rounded-full shadow-lg flex items-center gap-2 whitespace-nowrap';

            toast.classList.remove('opacity-0', '-translate-y-3');
            toast.classList.add('opacity-100', 'translate-y-0');

            clearTimeout(toastTimer);
            toastTimer = setTimeout(function () {
                toast.classList.add('opacity-0', '-translate-y-3');
                toast.classList.remove('opacity-100', 'translate-y-0');
            }, 2200);
        }

        function syncCartUI() {
            var totalCount = 0;
            var totalPrice = 0;

            for (var key in currentCart) {
                totalCount += parseInt(currentCart[key].quantity, 10);
                totalPrice += parseFloat(currentCart[key].price) * parseInt(currentCart[key].quantity, 10);
            }

            document.getElementById('cartCount').textContent = totalCount;
            document.getElementById('cartTotal').textContent = 'RM ' + totalPrice.toFixed(2);
            document.getElementById('cartModalTotal').textContent = 'RM ' + totalPrice.toFixed(2);

            var bar = document.getElementById('floatingCartBar');
            if (totalCount > 0) {
                bar.classList.remove('hidden');
            } else {
                bar.classList.add('hidden');
            }

            // Sync every food card's Add / Stepper buttons
            document.querySelectorAll('[id^="item-action-"]').forEach(function (container) {
                var id = container.id.replace('item-action-', '');
                var entry = currentCart[id];

                if (entry && entry.quantity > 0) {
                    container.innerHTML =
                        '<div class="inline-flex items-center bg-red-50 border border-red-200 rounded-xl p-0.5">' +
                            '<button type="button" onclick="changeQuantity(' + id + ', ' + (entry.quantity - 1) + ')" class="w-7 h-7 rounded-lg bg-white text-red-800 font-extrabold text-sm shadow-xs hover:bg-red-100 transition">−</button>' +
                            '<span class="px-2.5 text-xs font-extrabold text-red-900">' + entry.quantity + '</span>' +
                            '<button type="button" onclick="changeQuantity(' + id + ', ' + (entry.quantity + 1) + ')" class="w-7 h-7 rounded-lg bg-red-800 text-white font-extrabold text-sm hover:bg-red-900 transition">+</button>' +
                        '</div>';
                } else {
                    container.innerHTML =
                        '<button type="button" onclick="addToCartAjax(' + id + ', 1)" class="bg-red-800 hover:bg-red-900 active:scale-95 text-white font-bold text-xs px-3.5 py-2 rounded-xl shadow-sm transition flex items-center gap-1">' +
                            '<span class="text-sm leading-none">+</span> Add' +
                        '</button>';
                }
            });
        }

        function setActivePill(catId) {
            document.querySelectorAll('.cat-pill').forEach(function (btn) {
                if (btn.dataset.catBtn === String(catId)) {
                    btn.className = 'cat-pill active-pill px-3.5 py-1.5 rounded-full text-xs font-bold whitespace-nowrap bg-red-800 text-white shadow-sm transition';
                } else {
                    btn.className = 'cat-pill px-3.5 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap bg-stone-100 text-stone-600 hover:bg-stone-200 transition';
                }
            });
        }

        function selectCategory(catId, btn) {
            selectedCategoryId = String(catId);
            setActivePill(selectedCategoryId);
            filterMenu();

            if (catId !== 'all') {
                var heading = document.getElementById('cat-' + catId);
                if (heading) {
                    heading.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            } else {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }

        function clearSearch() {
            var input = document.getElementById('menuSearch');
            input.value = '';
            document.getElementById('clearSearchBtn').classList.add('hidden');
            filterMenu();
        }

        function filterMenu() {
            var query = document.getElementById('menuSearch').value.trim().toLowerCase();
            var clearBtn = document.getElementById('clearSearchBtn');
            clearBtn.classList.toggle('hidden', query === '');

            var visibleCards = 0;

            document.querySelectorAll('.category-section').forEach(function (section) {
                var secId = section.dataset.sectionId;
                var matchesCat = selectedCategoryId === 'all' || secId === selectedCategoryId;
                var sectionHasVisible = false;

                section.querySelectorAll('.food-card').forEach(function (card) {
                    var matchesQuery = query === '' ||
                        card.dataset.name.includes(query) ||
                        card.dataset.desc.includes(query);

                    if (matchesCat && matchesQuery) {
                        card.style.display = '';
                        sectionHasVisible = true;
                        visibleCards++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                section.style.display = sectionHasVisible ? '' : 'none';
            });

            document.getElementById('emptySearchState').classList.toggle('hidden', visibleCards > 0);
        }

        function addToCartAjax(foodItemId, quantity) {
            fetch("{{ route('order.addToCart', $table->qr_token) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    food_item_id: foodItemId,
                    quantity: quantity
                })
            })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (!data.cart) {
                    showToast(data.message || 'Could not add this item.', true);
                    return;
                }
                currentCart = data.cart;
                syncCartUI();
                var addedItem = currentCart[foodItemId];
                showToast(addedItem ? 'Added ' + addedItem.name : 'Added to cart', false);
            })
            .catch(function () {
                showToast('Network error. Please try again.', true);
            });
        }

        function setItemNote(id, value) {
            itemNotes[id] = value;
        }

        function renderCartModal() {
            var itemsHtml = '';
            var total = 0;

            for (var key in currentCart) {
                var item = currentCart[key];
                var subtotal = item.price * item.quantity;
                total += subtotal;

                itemsHtml +=
                    '<div class="py-3.5">' +
                        '<div class="flex items-start justify-between gap-3">' +
                            '<div class="flex-1 min-w-0">' +
                                '<div class="text-sm font-bold text-stone-800">' + escapeHtml(item.name) + '</div>' +
                                '<div class="text-xs text-stone-400 mt-0.5">RM ' + parseFloat(item.price).toFixed(2) + ' each</div>' +
                                '<div class="flex items-center gap-2 mt-2.5">' +
                                    '<button type="button" onclick="changeQuantity(' + key + ', ' + (item.quantity - 1) + ')" class="w-7 h-7 rounded-lg bg-stone-100 hover:bg-stone-200 text-stone-700 font-bold transition">−</button>' +
                                    '<span class="min-w-[1.75rem] text-center text-sm font-extrabold text-stone-800">' + item.quantity + '</span>' +
                                    '<button type="button" onclick="changeQuantity(' + key + ', ' + (item.quantity + 1) + ')" class="w-7 h-7 rounded-lg bg-orange-100 hover:bg-orange-200 text-orange-700 font-bold transition">+</button>' +
                                    '<button type="button" onclick="removeFromCart(' + key + ')" class="ml-2 text-xs font-semibold text-stone-400 hover:text-red-600 transition">Remove</button>' +
                                '</div>' +
                            '</div>' +
                            '<span class="text-sm font-extrabold text-stone-800 whitespace-nowrap">RM ' + subtotal.toFixed(2) + '</span>' +
                        '</div>' +
                        '<input type="text" maxlength="200" data-note-for="' + key + '" oninput="setItemNote(' + key + ', this.value)" placeholder="Special request (e.g. less spicy, no ice)" class="mt-2.5 w-full px-3 py-2 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-xs focus:outline-none">' +
                    '</div>';
            }

            document.getElementById('cartModalItems').innerHTML = itemsHtml;

            document.querySelectorAll('[data-note-for]').forEach(function (input) {
                input.value = itemNotes[input.dataset.noteFor] || '';
            });

            document.getElementById('cartModalTotal').textContent = 'RM ' + total.toFixed(2);
        }

        function openCartModal() {
            fetch("{{ route('order.viewCartJson', $table->qr_token) }}")
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    currentCart = data.cart || {};
                    syncCartUI();
                    renderCartModal();
                    document.getElementById('cartModal').classList.remove('hidden');
                    document.getElementById('cartModal').classList.add('flex');
                });
        }

        function closeCartModal() {
            document.getElementById('cartModal').classList.add('hidden');
            document.getElementById('cartModal').classList.remove('flex');
        }

        function handleBackdropClick(event) {
            if (event.target.id === 'cartModal') {
                closeCartModal();
            }
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
            .then(function (response) { return response.json(); })
            .then(function (data) {
                currentCart = data.cart || {};
                if (!currentCart[foodItemId]) {
                    delete itemNotes[foodItemId];
                }
                syncCartUI();

                if (Object.keys(currentCart).length === 0) {
                    closeCartModal();
                } else if (!document.getElementById('cartModal').classList.contains('hidden')) {
                    renderCartModal();
                }
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
            .then(function (response) { return response.json(); })
            .then(function (data) {
                currentCart = data.cart || {};
                delete itemNotes[foodItemId];
                syncCartUI();

                if (Object.keys(currentCart).length === 0) {
                    closeCartModal();
                } else {
                    renderCartModal();
                }
            });
        }

        function confirmOrder() {
            if (placing) { return; }
            placing = true;

            var btn = document.getElementById('placeOrderBtn');
            btn.disabled = true;
            btn.textContent = 'Sending Order to Kitchen...';

            fetch("{{ route('order.placeOrder', $table->qr_token) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    customer_name: document.getElementById('customerName').value,
                    customer_phone: document.getElementById('customerPhone').value,
                    customer_note: document.getElementById('customerNote').value,
                    item_notes: itemNotes
                })
            })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (!data.orderId) {
                    placing = false;
                    btn.disabled = false;
                    btn.textContent = 'Place Order';
                    showToast(data.message || 'Could not place the order.', true);
                    return;
                }
                window.location.href = data.redirectUrl || ('/order/confirmation/' + data.orderId);
            })
            .catch(function () {
                placing = false;
                btn.disabled = false;
                btn.textContent = 'Place Order';
                showToast('Could not place the order. Please try again.', true);
            });
        }

        // Initialize cart state on page load
        document.addEventListener('DOMContentLoaded', function () {
            syncCartUI();
        });
    </script>
</body>
</html>
