@extends('layouts.staff')

@section('title', 'Orders')

@section('content')
<div class="max-w-7xl mx-auto p-4 md:p-6 space-y-5">

    {{-- Header & Live Refresh Toggle --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Orders Queue</h1>
            <p class="text-xs sm:text-sm text-stone-500 mt-0.5">
                <span class="font-bold text-stone-700">{{ $orders->count() }}</span> order(s) · {{ $periodLabel }}
            </p>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto">
            <button type="button" id="autoRefreshBtn" onclick="toggleAutoRefresh()"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-stone-200 bg-white hover:bg-stone-50 text-xs font-bold text-stone-700 shadow-sm transition">
                <span id="autoRefreshDot" class="w-2 h-2 rounded-full bg-stone-300"></span>
                <span id="autoRefreshLabel">Auto-refresh: Off</span>
            </button>

            <a href="{{ request()->fullUrl() }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl border border-stone-200 bg-white hover:bg-stone-50 text-stone-600 shadow-sm transition" title="Refresh now" aria-label="Refresh now">
                <i class="fa-solid fa-rotate-right text-xs" aria-hidden="true"></i>
            </a>
        </div>
    </div>

    {{-- Quick Status Pills --}}
    @php
        $quickStatuses = [
            'all' => 'All',
            'open' => 'Open Queue',
            'pending' => 'Pending',
            'preparing' => 'Preparing',
            'ready' => 'Ready',
            'served' => 'Served',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
    @endphp
    <div class="flex items-center gap-2 overflow-x-auto pb-1">
        @foreach ($quickStatuses as $key => $label)
            <a href="{{ route('staff.orders.index', array_merge(request()->except('page'), ['status' => $key])) }}"
               class="px-3.5 py-1.5 rounded-full text-xs font-bold whitespace-nowrap transition {{ $status === $key ? 'bg-stone-900 text-white shadow-sm' : 'bg-white border border-stone-200 text-stone-600 hover:bg-stone-100' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- Filters Card --}}
    <form method="GET" action="{{ route('staff.orders.index') }}" class="bg-white rounded-2xl border border-stone-200/80 shadow-sm p-4 sm:p-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3">

            <label class="block">
                <span class="text-[11px] font-bold text-stone-500 uppercase tracking-wide">Period</span>
                <select name="range"
                        onchange="this.form.elements['from'].value = ''; this.form.elements['to'].value = ''; this.form.submit()"
                        class="mt-1 w-full px-3 py-2 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm font-medium text-stone-700 focus:outline-none">
                    <option value="today" {{ $range === 'today' ? 'selected' : '' }}>Today</option>
                    <option value="yesterday" {{ $range === 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                    <option value="7d" {{ $range === '7d' ? 'selected' : '' }}>Last 7 days</option>
                    <option value="month" {{ $range === 'month' ? 'selected' : '' }}>This month</option>
                    <option value="all" {{ $range === 'all' ? 'selected' : '' }}>All time</option>
                    <option value="custom" disabled {{ $range === 'custom' ? 'selected' : '' }}>Custom dates</option>
                </select>
            </label>

            <label class="block">
                <span class="text-[11px] font-bold text-stone-500 uppercase tracking-wide">From Date</span>
                <input type="date" name="from" value="{{ $from }}" class="mt-1 w-full px-3 py-2 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm font-medium text-stone-700 focus:outline-none">
            </label>

            <label class="block">
                <span class="text-[11px] font-bold text-stone-500 uppercase tracking-wide">To Date</span>
                <input type="date" name="to" value="{{ $to }}" class="mt-1 w-full px-3 py-2 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm font-medium text-stone-700 focus:outline-none">
            </label>

            <label class="block">
                <span class="text-[11px] font-bold text-stone-500 uppercase tracking-wide">Status</span>
                <select name="status" onchange="this.form.submit()" class="mt-1 w-full px-3 py-2 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm font-medium text-stone-700 focus:outline-none">
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All statuses</option>
                    <option value="open" {{ $status === 'open' ? 'selected' : '' }}>Open (pending, preparing, ready)</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="preparing" {{ $status === 'preparing' ? 'selected' : '' }}>Preparing</option>
                    <option value="ready" {{ $status === 'ready' ? 'selected' : '' }}>Ready</option>
                    <option value="served" {{ $status === 'served' ? 'selected' : '' }}>Served</option>
                    <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </label>

            <label class="block">
                <span class="text-[11px] font-bold text-stone-500 uppercase tracking-wide">Payment</span>
                <select name="payment" onchange="this.form.submit()" class="mt-1 w-full px-3 py-2 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm font-medium text-stone-700 focus:outline-none">
                    <option value="all" {{ $payment === 'all' ? 'selected' : '' }}>All payments</option>
                    <option value="unpaid" {{ $payment === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    <option value="paid" {{ $payment === 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </label>

            <label class="block">
                <span class="text-[11px] font-bold text-stone-500 uppercase tracking-wide">Table</span>
                <select name="table" onchange="this.form.submit()" class="mt-1 w-full px-3 py-2 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm font-medium text-stone-700 focus:outline-none">
                    <option value="">All tables</option>
                    @foreach ($tables as $table)
                        <option value="{{ $table->id }}" {{ (string) $tableId === (string) $table->id ? 'selected' : '' }}>Table {{ $table->table_number }}</option>
                    @endforeach
                </select>
            </label>

        </div>

        <div class="flex items-center justify-end gap-2 mt-3.5 pt-3 border-t border-stone-100">
            <a href="{{ route('staff.orders.index') }}" class="px-3.5 py-2 rounded-xl border border-stone-200 hover:bg-stone-50 text-stone-600 font-semibold text-xs transition">
                Reset
            </a>
            <button type="submit" class="px-4 py-2 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs shadow-sm transition">
                Apply Date Range
            </button>
        </div>
    </form>

    @php
        $setting = $setting ?? \App\Models\Setting::current();

        $statusStyles = [
            'pending'   => ['badge' => 'bg-amber-100 text-amber-800 border-amber-200',     'bar' => 'bg-amber-400'],
            'preparing' => ['badge' => 'bg-blue-100 text-blue-800 border-blue-200',        'bar' => 'bg-blue-500'],
            'ready'     => ['badge' => 'bg-purple-100 text-purple-800 border-purple-200',  'bar' => 'bg-purple-500'],
            'served'    => ['badge' => 'bg-indigo-100 text-indigo-800 border-indigo-200',  'bar' => 'bg-indigo-500'],
            'completed' => ['badge' => 'bg-emerald-100 text-emerald-800 border-emerald-200','bar' => 'bg-emerald-500'],
            'cancelled' => ['badge' => 'bg-red-100 text-red-800 border-red-200',           'bar' => 'bg-red-500'],
        ];

        $nextStepMap = [
            'pending'   => ['status' => 'preparing', 'label' => 'Start Preparing', 'icon' => 'fa-fire-burner',  'class' => 'bg-blue-600 hover:bg-blue-700 text-white'],
            'preparing' => ['status' => 'ready',     'label' => 'Mark Ready',      'icon' => 'fa-bell-concierge','class' => 'bg-purple-600 hover:bg-purple-700 text-white'],
            'ready'     => ['status' => 'served',    'label' => 'Mark Served',     'icon' => 'fa-utensils',     'class' => 'bg-indigo-600 hover:bg-indigo-700 text-white'],
            'served'    => ['status' => 'completed', 'label' => 'Complete Order',  'icon' => 'fa-check-double', 'class' => 'bg-emerald-600 hover:bg-emerald-700 text-white'],
        ];
    @endphp

    {{-- Empty State --}}
    @if ($orders->isEmpty())
        <div class="bg-white rounded-2xl border border-stone-200/80 shadow-sm p-12 text-center">
            <div class="w-12 h-12 rounded-full bg-stone-100 text-stone-400 flex items-center justify-center mx-auto mb-3 text-lg">
                <i class="fa-solid fa-receipt" aria-hidden="true"></i>
            </div>
            <p class="font-bold text-stone-800">No orders match these filters</p>
            <p class="text-xs text-stone-400 mt-1">Try switching the status tab or selecting "All time" under Period.</p>
            <a href="{{ route('staff.orders.index', ['range' => 'all']) }}" class="inline-block mt-4 px-4 py-2 bg-stone-900 text-white text-xs font-bold rounded-xl">
                View All Time Orders
            </a>
        </div>
    @else
        {{-- Orders Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 2xl:grid-cols-3 gap-4">
            @foreach ($orders as $order)
                @php
                    $displayNumber = $order->daily_order_number ?? $order->id;
                    $isCancelled = $order->order_status === 'cancelled';
                    $canChange = $order->canBeChanged();
                    $canRemoveItems = $canChange && $order->orderItems->count() > 1;
                    $style = $statusStyles[$order->order_status] ?? ['badge' => 'bg-stone-100 text-stone-800 border-stone-200', 'bar' => 'bg-stone-400'];
                    $nextStep = $nextStepMap[$order->order_status] ?? null;
                    $waitMinutes = abs((int) $order->created_at->diffInMinutes(now()));
                    $isActiveQueue = in_array($order->order_status, ['pending', 'preparing', 'ready']);

                    $subtotal = (float) $order->total_amount;
                    $serviceCharge = $setting->service_charge_enabled ? round($subtotal * ((float) $setting->service_charge_rate / 100), 2) : 0;
                    $sstAmount = $setting->sst_enabled ? round($subtotal * ((float) $setting->sst_rate / 100), 2) : 0;
                    $grandTotal = $subtotal + $serviceCharge + $sstAmount;
                    $hasExtraCharges = $setting->service_charge_enabled || $setting->sst_enabled;
                @endphp

                <div class="bg-white rounded-2xl border border-stone-200/80 shadow-sm overflow-hidden flex flex-col justify-between transition hover:shadow-md {{ $isCancelled ? 'opacity-70' : '' }}">
                    {{-- Status Accent Top Bar --}}
                    <div class="h-1.5 w-full {{ $style['bar'] }}"></div>

                    <div class="p-4 sm:p-5 flex-1 flex flex-col justify-between">
                        <div>
                            {{-- Card Header --}}
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg font-extrabold text-stone-900">Order #{{ $displayNumber }}</span>
                                        <span class="bg-stone-100 text-stone-800 text-xs font-extrabold px-2.5 py-0.5 rounded-lg">
                                            Table {{ $order->restaurantTable->table_number }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-stone-400 mt-1">
                                        Placed {{ $order->created_at->format('d M, h:i A') }}
                                    </p>
                                </div>

                                <div class="flex flex-col items-end gap-1.5">
                                    <span class="text-[11px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full border {{ $style['badge'] }}">
                                        {{ $order->order_status }}
                                    </span>
                                    <span class="text-[11px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full {{ $order->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                        {{ $order->payment_status }}
                                    </span>
                                </div>
                            </div>

                            {{-- Wait Time & Timeline Pills --}}
                            <div class="flex flex-wrap items-center gap-1.5 mb-3 text-[11px]">
                                @if ($isActiveQueue)
                                    <span class="inline-flex items-center gap-1 font-bold px-2.5 py-0.5 rounded-md {{ $waitMinutes >= 15 ? 'bg-red-50 text-red-700' : 'bg-orange-50 text-orange-700' }}">
                                        <i class="fa-regular fa-clock" aria-hidden="true"></i>
                                        Waiting {{ $waitMinutes }} min
                                    </span>
                                @endif
                                @if ($order->preparing_at)
                                    <span class="bg-stone-100 text-stone-500 px-2 py-0.5 rounded-md">Prep {{ $order->preparing_at->format('H:i') }}</span>
                                @endif
                                @if ($order->ready_at)
                                    <span class="bg-stone-100 text-stone-500 px-2 py-0.5 rounded-md">Ready {{ $order->ready_at->format('H:i') }}</span>
                                @endif
                                @if ($order->served_at)
                                    <span class="bg-stone-100 text-stone-500 px-2 py-0.5 rounded-md">Served {{ $order->served_at->format('H:i') }}</span>
                                @endif
                                @if ($order->completed_at)
                                    <span class="bg-stone-100 text-stone-500 px-2 py-0.5 rounded-md">Done {{ $order->completed_at->format('H:i') }}</span>
                                @endif
                                @if ($order->paid_at)
                                    <span class="bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-md">Paid {{ $order->paid_at->format('H:i') }}</span>
                                @endif
                                @if ($order->cancelled_at)
                                    <span class="bg-red-50 text-red-700 px-2 py-0.5 rounded-md">Cancelled {{ $order->cancelled_at->format('H:i') }}</span>
                                @endif
                            </div>

                            {{-- Order Items --}}
                            <div class="border-t border-b border-stone-100 py-2.5 mb-3 divide-y divide-stone-50">
                                @foreach ($order->orderItems as $item)
                                    <div class="py-1.5 text-sm">
                                        <div class="flex justify-between items-center gap-3">
                                            <span class="text-stone-800 font-medium">
                                                <span class="font-extrabold text-orange-600 mr-1">{{ $item->quantity }}×</span>
                                                {{ $item->item_name_snapshot }}
                                            </span>

                                            <span class="flex items-center gap-2">
                                                <span class="whitespace-nowrap font-semibold text-stone-700 tabular-nums">
                                                    RM {{ number_format($item->unit_price * $item->quantity, 2) }}
                                                </span>

                                                @if ($canRemoveItems)
                                                    <button type="button"
                                                            onclick="openRemoveItemModal(this)"
                                                            data-action="{{ route('staff.orders.removeItem', [$order->id, $item->id]) }}"
                                                            data-order="{{ $displayNumber }}"
                                                            data-name="{{ $item->quantity }}x {{ $item->item_name_snapshot }}"
                                                            class="text-stone-300 hover:text-red-600 transition"
                                                            title="Remove this item" aria-label="Remove this item">
                                                        <i class="fa-solid fa-circle-minus" aria-hidden="true"></i>
                                                    </button>
                                                @endif
                                            </span>
                                        </div>

                                        @if ($item->item_note)
                                            <p class="text-xs text-orange-700 bg-orange-50/80 inline-block px-2 py-0.5 rounded mt-1">
                                                Note: {{ $item->item_note }}
                                            </p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            {{-- Customer Info & Notes --}}
                            @if ($order->customer_name || $order->customer_phone || $order->customer_note)
                                <div class="bg-stone-50 border border-stone-100 rounded-xl p-2.5 mb-3 text-xs text-stone-600 space-y-0.5">
                                    @if ($order->customer_name)
                                        <p><span class="font-bold text-stone-700">Customer:</span> {{ $order->customer_name }}</p>
                                    @endif
                                    @if ($order->customer_phone)
                                        <p><span class="font-bold text-stone-700">Phone:</span> {{ $order->customer_phone }}</p>
                                    @endif
                                    @if ($order->customer_note)
                                        <p class="text-amber-900 font-medium"><span class="font-bold">Order Note:</span> {{ $order->customer_note }}</p>
                                    @endif
                                </div>
                            @endif

                            {{-- Staff Log --}}
                            @if ($order->staff_note)
                                <div class="bg-amber-50/80 border border-amber-200/60 text-amber-900 rounded-xl p-2.5 mb-3 text-xs">
                                    <p class="font-bold mb-1 flex items-center gap-1.5">
                                        <i class="fa-solid fa-clock-rotate-left text-amber-600" aria-hidden="true"></i>
                                        <span>Staff Log</span>
                                    </p>
                                    @foreach (explode("\n", $order->staff_note) as $noteLine)
                                        <p class="text-[11px] leading-relaxed">{{ $noteLine }}</p>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Footer Total & Actions --}}
                        <div class="pt-1">
                            @if ($hasExtraCharges)
                                <div class="space-y-1 text-xs text-stone-500 mb-2 pb-2 border-b border-stone-100">
                                    <div class="flex justify-between items-center">
                                        <span>Subtotal</span>
                                        <span class="font-semibold text-stone-700 tabular-nums">RM {{ number_format($subtotal, 2) }}</span>
                                    </div>

                                    @if ($setting->service_charge_enabled)
                                        <div class="flex justify-between items-center">
                                            <span>Service Charge ({{ rtrim(rtrim(number_format($setting->service_charge_rate, 2), '0'), '.') }}%)</span>
                                            <span class="font-semibold text-stone-700 tabular-nums">RM {{ number_format($serviceCharge, 2) }}</span>
                                        </div>
                                    @endif

                                    @if ($setting->sst_enabled)
                                        <div class="flex justify-between items-center">
                                            <span>SST ({{ rtrim(rtrim(number_format($setting->sst_rate, 2), '0'), '.') }}%)</span>
                                            <span class="font-semibold text-stone-700 tabular-nums">RM {{ number_format($sstAmount, 2) }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <div class="flex justify-between items-center font-extrabold text-base text-stone-900 mb-3">
                                <span>Total</span>
                                <span class="tabular-nums text-lg">RM {{ number_format($grandTotal, 2) }}</span>
                            </div>

                            @if (!$isCancelled)
                                {{-- One-Tap Next Status Action --}}
                                @if ($nextStep)
                                    <form action="{{ route('staff.orders.updateStatus', $order->id) }}" method="POST" class="mb-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="order_status" value="{{ $nextStep['status'] }}">
                                        <button type="submit" class="w-full flex items-center justify-center gap-2 font-bold px-4 py-2.5 rounded-xl text-xs sm:text-sm shadow-sm transition {{ $nextStep['class'] }}">
                                            <i class="fa-solid {{ $nextStep['icon'] }}" aria-hidden="true"></i>
                                            <span>{{ $nextStep['label'] }}</span>
                                        </button>
                                    </form>
                                @endif

                                {{-- Mark Paid Button --}}
                                @if ($order->payment_status !== 'paid')
                                    <form action="{{ route('staff.orders.markPaid', $order->id) }}" method="POST" class="mb-2">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="w-full flex items-center justify-center gap-2 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-800 font-bold px-4 py-2 rounded-xl text-xs transition">
                                            <i class="fa-solid fa-circle-check text-emerald-600" aria-hidden="true"></i>
                                            <span>Mark as Paid (RM {{ number_format($grandTotal, 2) }})</span>
                                        </button>
                                    </form>
                                @endif

                                {{-- Manual Status Override --}}
                                <form action="{{ route('staff.orders.updateStatus', $order->id) }}" method="POST" class="flex gap-2 mb-2">
                                    @csrf
                                    @method('PUT')
                                    <select name="order_status" class="flex-1 px-3 py-2 bg-stone-50 border border-stone-200 rounded-xl text-xs font-semibold text-stone-700 focus:outline-none focus:border-orange-500">
                                        <option value="pending" {{ $order->order_status === 'pending' ? 'selected' : '' }}>Status: Pending</option>
                                        <option value="preparing" {{ $order->order_status === 'preparing' ? 'selected' : '' }}>Status: Preparing</option>
                                        <option value="ready" {{ $order->order_status === 'ready' ? 'selected' : '' }}>Status: Ready</option>
                                        <option value="served" {{ $order->order_status === 'served' ? 'selected' : '' }}>Status: Served</option>
                                        <option value="completed" {{ $order->order_status === 'completed' ? 'selected' : '' }}>Status: Completed</option>
                                    </select>
                                    <button type="submit" class="bg-stone-800 hover:bg-stone-900 text-white font-bold px-3.5 py-2 rounded-xl text-xs transition">
                                        Set
                                    </button>
                                </form>

                                {{-- Print Ticket & Receipt --}}
                                <div class="flex gap-2">
                                    <a href="{{ route('staff.orders.print', $order->id) }}" target="_blank"
                                       class="flex-1 flex items-center justify-center gap-1.5 border border-stone-200 hover:bg-stone-50 text-stone-700 font-semibold px-3 py-2 rounded-xl text-xs transition">
                                        <i class="fa-solid fa-fire-burner text-stone-400" aria-hidden="true"></i>
                                        <span>Kitchen Ticket</span>
                                    </a>
                                    <a href="{{ route('staff.orders.receipt', $order->id) }}" target="_blank"
                                       class="flex-1 flex items-center justify-center gap-1.5 border border-stone-200 hover:bg-stone-50 text-stone-700 font-semibold px-3 py-2 rounded-xl text-xs transition">
                                        <i class="fa-solid fa-receipt text-stone-400" aria-hidden="true"></i>
                                        <span>Receipt</span>
                                    </a>
                                </div>

                                @if ($canChange)
                                    <button type="button"
                                            onclick="openCancelOrderModal(this)"
                                            data-action="{{ route('staff.orders.cancel', $order->id) }}"
                                            data-order="{{ $displayNumber }}"
                                            class="w-full mt-2.5 flex items-center justify-center gap-1.5 text-xs font-semibold text-stone-400 hover:text-red-600 py-1 transition">
                                        <i class="fa-solid fa-ban" aria-hidden="true"></i>
                                        <span>Cancel order</span>
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- Cancel Order Modal --}}
<div id="cancelOrderModal" class="fixed inset-0 bg-stone-950/60 backdrop-blur-[1px] z-50 hidden items-center justify-center p-4" onclick="if (event.target === this) closeCancelOrderModal()">
    <div class="bg-white rounded-2xl w-full max-w-sm overflow-hidden shadow-2xl">
        <div class="p-5 text-center">
            <div class="mx-auto mb-3 w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-ban" aria-hidden="true"></i>
            </div>
            <h2 class="text-lg font-extrabold text-stone-900">Cancel order?</h2>
            <p class="text-xs sm:text-sm text-stone-500 mt-1.5">
                Order #<span id="cancelOrderNumber" class="font-bold text-stone-800"></span>
                will be marked as cancelled and excluded from sales.
            </p>
        </div>

        <form id="cancelOrderForm" action="" method="POST" class="px-5 pb-5 flex flex-col gap-3">
            @csrf
            @method('PUT')

            <label class="text-xs font-bold text-stone-600 uppercase tracking-wide">Reason (optional)
                <textarea name="reason" rows="2" maxlength="200" placeholder="e.g. customer left"
                          class="mt-1 w-full p-2.5 bg-stone-50 border border-stone-200 focus:bg-white focus:border-red-500 rounded-xl text-sm font-normal focus:outline-none"></textarea>
            </label>

            <div class="flex gap-2 pt-1">
                <button type="button" onclick="closeCancelOrderModal()" class="flex-1 border border-stone-200 hover:bg-stone-50 text-stone-700 font-bold px-4 py-2.5 rounded-xl text-xs transition">Keep order</button>
                <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs transition">Yes, cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- Remove Item Modal --}}
<div id="removeItemModal" class="fixed inset-0 bg-stone-950/60 backdrop-blur-[1px] z-50 hidden items-center justify-center p-4" onclick="if (event.target === this) closeRemoveItemModal()">
    <div class="bg-white rounded-2xl w-full max-w-sm overflow-hidden shadow-2xl">
        <div class="p-5 text-center">
            <div class="mx-auto mb-3 w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-circle-minus" aria-hidden="true"></i>
            </div>
            <h2 class="text-lg font-extrabold text-stone-900">Remove item?</h2>
            <p class="text-xs sm:text-sm text-stone-500 mt-1.5">
                Remove <span id="removeItemName" class="font-bold text-stone-800"></span>
                from order #<span id="removeItemOrder"></span>? The order total will adjust automatically.
            </p>
        </div>

        <form id="removeItemForm" action="" method="POST" class="px-5 pb-5 flex flex-col gap-3">
            @csrf
            @method('DELETE')

            <label class="text-xs font-bold text-stone-600 uppercase tracking-wide">Reason (optional)
                <textarea name="reason" rows="2" maxlength="200" placeholder="e.g. item sold out"
                          class="mt-1 w-full p-2.5 bg-stone-50 border border-stone-200 focus:bg-white focus:border-red-500 rounded-xl text-sm font-normal focus:outline-none"></textarea>
            </label>

            <div class="flex gap-2 pt-1">
                <button type="button" onclick="closeRemoveItemModal()" class="flex-1 border border-stone-200 hover:bg-stone-50 text-stone-700 font-bold px-4 py-2.5 rounded-xl text-xs transition">Cancel</button>
                <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs transition">Yes, remove</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    var autoRefreshTimer = null;

    function applyAutoRefreshState(enabled) {
        var dot = document.getElementById('autoRefreshDot');
        var label = document.getElementById('autoRefreshLabel');

        if (enabled) {
            dot.className = 'w-2 h-2 rounded-full bg-emerald-500 animate-pulse';
            label.textContent = 'Auto-refresh: 30s';
            clearInterval(autoRefreshTimer);
            autoRefreshTimer = setInterval(function () {
                var cancelOpen = !document.getElementById('cancelOrderModal').classList.contains('hidden');
                var removeOpen = !document.getElementById('removeItemModal').classList.contains('hidden');
                if (!cancelOpen && !removeOpen) {
                    if (typeof ajaxVisit === 'function') {
                        ajaxVisit(window.location.href);
                    } else {
                        window.location.reload();
                    }
                }
            }, 30000);
        } else {
            dot.className = 'w-2 h-2 rounded-full bg-stone-300';
            label.textContent = 'Auto-refresh: Off';
            clearInterval(autoRefreshTimer);
        }
    }

    function toggleAutoRefresh() {
        var current = localStorage.getItem('scandine_orders_autorefresh') === '1';
        var next = !current;
        localStorage.setItem('scandine_orders_autorefresh', next ? '1' : '0');
        applyAutoRefreshState(next);
    }

    document.addEventListener('DOMContentLoaded', function () {
        applyAutoRefreshState(localStorage.getItem('scandine_orders_autorefresh') === '1');
    });

    function openCancelOrderModal(button) {
        var form = document.getElementById('cancelOrderForm');
        form.action = button.dataset.action;
        form.querySelector('[name="reason"]').value = '';
        document.getElementById('cancelOrderNumber').innerText = button.dataset.order;

        document.getElementById('cancelOrderModal').classList.remove('hidden');
        document.getElementById('cancelOrderModal').classList.add('flex');
    }

    function closeCancelOrderModal() {
        document.getElementById('cancelOrderModal').classList.add('hidden');
        document.getElementById('cancelOrderModal').classList.remove('flex');
    }

    function openRemoveItemModal(button) {
        var form = document.getElementById('removeItemForm');
        form.action = button.dataset.action;
        form.querySelector('[name="reason"]').value = '';
        document.getElementById('removeItemName').innerText = button.dataset.name;
        document.getElementById('removeItemOrder').innerText = button.dataset.order;

        document.getElementById('removeItemModal').classList.remove('hidden');
        document.getElementById('removeItemModal').classList.add('flex');
    }

    function closeRemoveItemModal() {
        document.getElementById('removeItemModal').classList.add('hidden');
        document.getElementById('removeItemModal').classList.remove('flex');
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeCancelOrderModal();
            closeRemoveItemModal();
        }
    });
</script>
@endpush
