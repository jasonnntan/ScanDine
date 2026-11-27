@php
    $displayNumber = $order->daily_order_number ?? $order->id;
    $status = strtolower($order->order_status);
    $step = match ($status) {
        'pending' => 1,
        'preparing', 'cooking', 'in_progress' => 2,
        'completed', 'served', 'ready' => 3,
        default => 1,
    };
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #{{ $displayNumber }} Confirmed</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-stone-50 text-stone-800 antialiased">
    <div class="max-w-lg mx-auto min-h-screen px-4 py-6 flex flex-col justify-between">
        <div>
            {{-- Confirmation Card --}}
            <div class="bg-white rounded-3xl border border-stone-200/80 shadow-sm p-6 text-center mb-4">
                <div class="mx-auto mb-3 w-14 h-14 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-2xl font-extrabold">
                    ✓
                </div>
                <span class="inline-block bg-stone-100 text-stone-600 text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full mb-2">
                    Table {{ $order->restaurantTable->table_number }}
                </span>
                <h1 class="text-2xl font-extrabold text-stone-900">Order #{{ $displayNumber }} Sent!</h1>
                <p class="text-sm text-stone-500 mt-1">
                    Placed at {{ $order->created_at->format('h:i A') }} • Kitchen has received your order.
                </p>

                {{-- Status Stepper --}}
                @if ($status === 'cancelled')
                    <div class="mt-5 bg-red-50 border border-red-200 text-red-700 text-xs font-bold px-4 py-2.5 rounded-xl">
                        Order Cancelled
                    </div>
                @else
                    <div class="mt-6 pt-5 border-t border-stone-100">
                        <div class="grid grid-cols-3 gap-2 text-xs">
                            <div class="flex flex-col items-center">
                                <span class="w-6 h-6 rounded-full flex items-center justify-center font-bold {{ $step >= 1 ? 'bg-emerald-600 text-white' : 'bg-stone-200 text-stone-500' }}">1</span>
                                <span class="mt-1.5 font-bold {{ $step >= 1 ? 'text-stone-800' : 'text-stone-400' }}">Received</span>
                            </div>
                            <div class="flex flex-col items-center">
                                <span class="w-6 h-6 rounded-full flex items-center justify-center font-bold {{ $step >= 2 ? 'bg-orange-500 text-white' : 'bg-stone-200 text-stone-500' }}">2</span>
                                <span class="mt-1.5 font-bold {{ $step >= 2 ? 'text-stone-800' : 'text-stone-400' }}">Preparing</span>
                            </div>
                            <div class="flex flex-col items-center">
                                <span class="w-6 h-6 rounded-full flex items-center justify-center font-bold {{ $step >= 3 ? 'bg-emerald-600 text-white' : 'bg-stone-200 text-stone-500' }}">3</span>
                                <span class="mt-1.5 font-bold {{ $step >= 3 ? 'text-stone-800' : 'text-stone-400' }}">Served</span>
                            </div>
                        </div>
                        <div class="mt-3 inline-flex items-center gap-1.5 bg-amber-50 text-amber-800 border border-amber-200/80 text-xs font-bold px-3 py-1 rounded-full capitalize">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            Current Status: {{ str_replace('_', ' ', $order->order_status) }}
                        </div>
                    </div>
                @endif
            </div>

            {{-- Order Summary Receipt --}}
            <div class="bg-white rounded-3xl border border-stone-200/80 shadow-sm p-5 mb-4">
                <div class="flex items-center justify-between pb-3 mb-2 border-b border-stone-100">
                    <h2 class="text-sm font-extrabold uppercase tracking-wider text-stone-400">Order Summary</h2>
                    <span class="text-xs font-semibold text-stone-400">{{ $order->orderItems->sum('quantity') }} items</span>
                </div>

                <div class="divide-y divide-stone-100">
                    @foreach ($order->orderItems as $item)
                        <div class="py-3 flex justify-between items-start gap-3">
                            <div class="min-w-0">
                                <div class="text-sm font-bold text-stone-800">
                                    <span class="text-orange-600 font-extrabold mr-1">{{ $item->quantity }}×</span>
                                    {{ $item->item_name_snapshot }}
                                </div>
                                @if ($item->item_note)
                                    <p class="text-xs text-stone-500 italic mt-0.5">Note: {{ $item->item_note }}</p>
                                @endif
                            </div>
                            <span class="text-sm font-bold text-stone-800 whitespace-nowrap">
                                RM {{ number_format($item->unit_price * $item->quantity, 2) }}
                            </span>
                        </div>
                    @endforeach
                </div>

                @if ($order->customer_name || $order->customer_note)
                    <div class="mt-3 pt-3 border-t border-dashed border-stone-200 space-y-1 text-xs text-stone-500">
                        @if ($order->customer_name)
                            <p><span class="font-bold text-stone-700">Customer:</span> {{ $order->customer_name }}</p>
                        @endif
                        @if ($order->customer_note)
                            <p><span class="font-bold text-stone-700">Order Note:</span> {{ $order->customer_note }}</p>
                        @endif
                    </div>
                @endif

                <div class="flex justify-between items-center font-extrabold text-base sm:text-lg pt-4 mt-3 border-t border-stone-200">
                    <span class="text-stone-800">Total</span>
                    <span class="text-red-800">RM {{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="space-y-2.5 pt-2">
            <a href="{{ route('order.menu', $order->restaurantTable->qr_token) }}" class="block w-full text-center bg-orange-600 hover:bg-orange-700 text-white font-extrabold py-3.5 rounded-2xl shadow-lg shadow-orange-600/20 transition">
                + Order More Items
            </a>
            <button type="button" onclick="window.location.reload()" class="block w-full text-center bg-white hover:bg-stone-100 text-stone-700 font-bold text-sm py-3 rounded-2xl border border-stone-200 transition">
                Refresh Order Status
            </button>
        </div>
    </div>
</body>
</html>
