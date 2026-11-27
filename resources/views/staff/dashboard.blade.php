@extends('layouts.staff')

@section('title', 'Sales Dashboard')

@section('content')
<div class="max-w-7xl mx-auto p-4 md:p-6 space-y-5">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Sales Dashboard</h1>
            <div class="flex flex-wrap items-center gap-2 mt-1">
                <span class="inline-flex items-center gap-1.5 bg-white border border-stone-200 text-stone-600 text-xs font-semibold px-2.5 py-1 rounded-full">
                    <i class="fa-regular fa-calendar text-orange-600" aria-hidden="true"></i>
                    {{ $periodLabel }}
                </span>
                <span class="text-xs text-stone-400">
                    Showing {{ $status === 'completed' ? 'completed orders only' : 'all active & completed orders' }}
                </span>
            </div>
        </div>

        <a href="{{ route('staff.orders.index') }}" class="inline-flex items-center justify-center gap-2 bg-stone-900 hover:bg-stone-800 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm transition self-start sm:self-auto">
            <i class="fa-solid fa-receipt text-orange-400" aria-hidden="true"></i>
            <span>Manage Live Orders</span>
        </a>
    </div>

    {{-- Filters Card --}}
    <form method="GET" action="{{ route('staff.dashboard') }}" class="bg-white rounded-2xl border border-stone-200/80 shadow-sm p-4 sm:p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-extrabold uppercase tracking-wider text-stone-400 flex items-center gap-1.5">
                <i class="fa-solid fa-sliders text-orange-600" aria-hidden="true"></i>
                Filter Analytics
            </span>
            <a href="{{ route('staff.dashboard') }}" class="text-xs font-bold text-stone-400 hover:text-orange-600 transition">
                Reset all
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3">
            <label class="block">
                <span class="text-[11px] font-bold text-stone-500 uppercase tracking-wide">Period</span>
                <select name="range"
                        onchange="this.form.elements['from'].value = ''; this.form.elements['to'].value = ''; this.form.submit()"
                        class="mt-1 w-full px-3 py-2 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm font-medium text-stone-700 focus:outline-none">
                    <option value="today" {{ $range === 'today' ? 'selected' : '' }}>Today</option>
                    <option value="7d" {{ $range === '7d' ? 'selected' : '' }}>Last 7 days</option>
                    <option value="30d" {{ $range === '30d' ? 'selected' : '' }}>Last 30 days</option>
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
                <span class="text-[11px] font-bold text-stone-500 uppercase tracking-wide">Category</span>
                <select name="category" onchange="this.form.submit()" class="mt-1 w-full px-3 py-2 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm font-medium text-stone-700 focus:outline-none">
                    <option value="">All categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ (string) $categoryId === (string) $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
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

            <label class="block">
                <span class="text-[11px] font-bold text-stone-500 uppercase tracking-wide">Orders Counted</span>
                <select name="status" onchange="this.form.submit()" class="mt-1 w-full px-3 py-2 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm font-medium text-stone-700 focus:outline-none">
                    <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed only</option>
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All except cancelled</option>
                </select>
            </label>
        </div>

        <div class="flex items-center justify-end gap-2 mt-3.5 pt-3 border-t border-stone-100">
            <a href="{{ route('staff.dashboard') }}" class="px-3.5 py-2 rounded-xl border border-stone-200 hover:bg-stone-50 text-stone-600 font-semibold text-xs transition">
                Reset
            </a>
            <button type="submit" class="px-4 py-2 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs shadow-sm transition">
                Apply Date Range
            </button>
        </div>
    </form>

    {{-- Empty Completed Orders Hint --}}
    @if ($orderCount === 0 && $status === 'completed')
        <div class="bg-amber-50 border border-amber-200/80 text-amber-900 text-sm p-4 rounded-2xl flex items-start gap-3">
            <i class="fa-solid fa-circle-info text-amber-600 mt-0.5" aria-hidden="true"></i>
            <div>
                <p class="font-bold">No completed orders match these filters</p>
                <p class="text-xs text-amber-800/90 mt-0.5">
                    Sales only count once an order is marked <strong>Completed</strong> on the Orders page. To include open orders, switch <strong>Orders Counted</strong> to <strong>All except cancelled</strong>.
                </p>
            </div>
        </div>
    @endif

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">

        {{-- Revenue --}}
        <div class="bg-white rounded-2xl border border-stone-200/80 p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-bold text-stone-400 uppercase tracking-wider">Total Revenue</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-stone-900 tabular-nums mt-1.5">RM {{ number_format($revenue, 2) }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-sack-dollar" aria-hidden="true"></i>
                </div>
            </div>
            <p class="text-xs text-stone-400 mt-3">{{ $status === 'completed' ? 'From completed orders' : 'From all non-cancelled orders' }}</p>
        </div>

        {{-- Orders --}}
        <div class="bg-white rounded-2xl border border-stone-200/80 p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-bold text-stone-400 uppercase tracking-wider">Total Orders</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-stone-900 tabular-nums mt-1.5">{{ number_format($orderCount) }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-bag-shopping" aria-hidden="true"></i>
                </div>
            </div>
            <p class="text-xs text-stone-400 mt-3">Orders recorded in selected period</p>
        </div>

        {{-- Average Order --}}
        <div class="bg-white rounded-2xl border border-stone-200/80 p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-bold text-stone-400 uppercase tracking-wider">Average Order Value</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-stone-900 tabular-nums mt-1.5">RM {{ number_format($avgOrder, 2) }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-receipt" aria-hidden="true"></i>
                </div>
            </div>
            <p class="text-xs text-stone-400 mt-3">Mean spend per table order</p>
        </div>

        {{-- Items Sold --}}
        <div class="bg-white rounded-2xl border border-stone-200/80 p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-bold text-stone-400 uppercase tracking-wider">Dishes Sold</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-stone-900 tabular-nums mt-1.5">{{ number_format($itemsSold) }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-bowl-food" aria-hidden="true"></i>
                </div>
            </div>
            <p class="text-xs text-stone-400 mt-3">Total item quantity ordered</p>
        </div>

        {{-- Estimated Profit --}}
        <div class="bg-white rounded-2xl border border-stone-200/80 p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-bold text-stone-400 uppercase tracking-wider">Estimated Profit</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-stone-900 tabular-nums mt-1.5">RM {{ number_format($profit, 2) }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-chart-pie" aria-hidden="true"></i>
                </div>
            </div>
            <p class="text-xs text-stone-400 mt-3">Excludes items without a unit cost</p>
        </div>

        {{-- Best Seller --}}
        <div class="bg-white rounded-2xl border border-stone-200/80 p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-stone-400 uppercase tracking-wider">Top Selling Dish</p>
                    <p class="text-lg sm:text-xl font-extrabold text-stone-900 mt-1.5 leading-snug line-clamp-1" title="{{ $bestSeller->name ?? 'No data' }}">
                        {{ $bestSeller->name ?? 'No sales yet' }}
                    </p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-fire" aria-hidden="true"></i>
                </div>
            </div>
            <p class="text-xs text-stone-400 mt-3">
                <span class="font-bold text-orange-600">{{ $bestSeller->total_sold ?? 0 }}</span> units sold in period
            </p>
        </div>

    </div>

    {{-- Charts Grid --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">

        {{-- Sales Over Time --}}
        <div class="bg-white rounded-2xl border border-stone-200/80 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-extrabold text-stone-800">Sales Over Time</h2>
                    <p class="text-xs text-stone-400">Daily revenue trend</p>
                </div>
                <span class="text-xs font-bold text-orange-600 bg-orange-50 px-2.5 py-1 rounded-lg">RM</span>
            </div>
            @if ($salesByDay->isEmpty())
                <div class="h-72 flex flex-col items-center justify-center text-stone-400">
                    <i class="fa-solid fa-chart-area text-2xl mb-2 text-stone-300" aria-hidden="true"></i>
                    <p class="text-sm font-semibold">No sales data for these filters</p>
                </div>
            @else
                <div id="chartDay" class="w-full h-72"></div>
            @endif
        </div>

        {{-- Sales by Category --}}
        <div class="bg-white rounded-2xl border border-stone-200/80 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-extrabold text-stone-800">Sales by Category</h2>
                    <p class="text-xs text-stone-400">Revenue contribution per menu category</p>
                </div>
                <span class="text-xs font-bold text-stone-500 bg-stone-100 px-2.5 py-1 rounded-lg">Category</span>
            </div>
            @if ($salesByCategory->isEmpty())
                <div class="h-72 flex flex-col items-center justify-center text-stone-400">
                    <i class="fa-solid fa-layer-group text-2xl mb-2 text-stone-300" aria-hidden="true"></i>
                    <p class="text-sm font-semibold">No category revenue for these filters</p>
                </div>
            @else
                <div id="chartCategory" class="w-full h-72"></div>
            @endif
        </div>

        {{-- Top 5 Items --}}
        <div class="bg-white rounded-2xl border border-stone-200/80 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-extrabold text-stone-800">Top 5 Best Sellers</h2>
                    <p class="text-xs text-stone-400">Ranked by total quantity sold</p>
                </div>
                <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg">Units</span>
            </div>
            @if ($topItems->isEmpty())
                <div class="h-72 flex flex-col items-center justify-center text-stone-400">
                    <i class="fa-solid fa-trophy text-2xl mb-2 text-stone-300" aria-hidden="true"></i>
                    <p class="text-sm font-semibold">No item sales for these filters</p>
                </div>
            @else
                <div id="chartTop" class="w-full h-72"></div>
            @endif
        </div>

        {{-- Orders by Status --}}
        <div class="bg-white rounded-2xl border border-stone-200/80 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-extrabold text-stone-800">Orders by Status</h2>
                    <p class="text-xs text-stone-400">Includes open & cancelled orders for selected period/table</p>
                </div>
                <span class="text-xs font-bold text-stone-500 bg-stone-100 px-2.5 py-1 rounded-lg">Breakdown</span>
            </div>
            @if ($statusChart->isEmpty())
                <div class="h-72 flex flex-col items-center justify-center text-stone-400">
                    <i class="fa-solid fa-chart-pie text-2xl mb-2 text-stone-300" aria-hidden="true"></i>
                    <p class="text-sm font-semibold">No order activity for these filters</p>
                </div>
            @else
                <div id="chartStatus" class="w-full h-72"></div>
            @endif
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
<script>
    var charts = [];

    function rm(value) {
        return 'RM ' + Number(value).toFixed(2);
    }

    function makeChart(id, option) {
        var el = document.getElementById(id);
        if (!el) { return; }

        var chart = echarts.init(el);
        chart.setOption(option);
        charts.push(chart);

        if (typeof ResizeObserver !== 'undefined') {
            new ResizeObserver(function () {
                chart.resize();
            }).observe(el);
        }
    }

    var dayLabels = {!! json_encode($salesByDay->pluck('sales_day')) !!};
    var dayTotals = {!! json_encode($salesByDay->pluck('total')) !!}.map(Number);

    var categoryNames = {!! json_encode($salesByCategory->pluck('category_name')) !!};
    var categoryTotals = {!! json_encode($salesByCategory->pluck('total_sales')) !!}.map(Number);

    var topNames = {!! json_encode($topItems->pluck('name')) !!};
    var topQty = {!! json_encode($topItems->pluck('qty')) !!}.map(Number);

    var statusData = {!! json_encode($statusChart) !!};
    var statusColors = {
        pending: '#f59e0b',
        preparing: '#3b82f6',
        ready: '#a855f7',
        served: '#6366f1',
        completed: '#10b981',
        cancelled: '#ef4444'
    };

    var totalStatusOrders = 0;
    statusData.forEach(function (d) {
        totalStatusOrders += Number(d.value || 0);
        d.name = String(d.name).charAt(0).toUpperCase() + String(d.name).slice(1);
        d.itemStyle = { color: statusColors[String(d.name).toLowerCase()] || '#9ca3af' };
    });

    makeChart('chartDay', {
        grid: { left: 10, right: 18, top: 18, bottom: 10, containLabel: true },
        tooltip: {
            trigger: 'axis',
            backgroundColor: '#1c1917',
            borderWidth: 0,
            textStyle: { color: '#fff', fontSize: 12 },
            valueFormatter: rm
        },
        xAxis: {
            type: 'category',
            data: dayLabels,
            boundaryGap: false,
            axisLine: { lineStyle: { color: '#e7e5e4' } },
            axisLabel: { color: '#78716c', fontSize: 11 }
        },
        yAxis: {
            type: 'value',
            splitLine: { lineStyle: { color: '#f5f5f4', type: 'dashed' } },
            axisLabel: { color: '#78716c', fontSize: 11, formatter: 'RM {value}' }
        },
        series: [{
            type: 'line',
            data: dayTotals,
            smooth: 0.35,
            symbol: 'circle',
            symbolSize: 7,
            lineStyle: { width: 3, color: '#ea580c' },
            itemStyle: { color: '#ea580c', borderWidth: 2, borderColor: '#fff' },
            areaStyle: {
                color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                    { offset: 0, color: 'rgba(234, 88, 12, 0.22)' },
                    { offset: 1, color: 'rgba(234, 88, 12, 0.01)' }
                ])
            }
        }]
    });

    makeChart('chartCategory', {
        grid: { left: 8, right: 85, top: 8, bottom: 8, containLabel: true },
        tooltip: {
            trigger: 'axis',
            axisPointer: { type: 'shadow' },
            backgroundColor: '#1c1917',
            borderWidth: 0,
            textStyle: { color: '#fff', fontSize: 12 },
            valueFormatter: rm
        },
        xAxis: {
            type: 'value',
            splitLine: { lineStyle: { color: '#f5f5f4', type: 'dashed' } },
            axisLabel: { color: '#78716c', fontSize: 11, formatter: 'RM {value}' }
        },
        yAxis: {
            type: 'category',
            data: categoryNames,
            inverse: true,
            axisLine: { show: false },
            axisTick: { show: false },
            axisLabel: { color: '#44403c', fontWeight: 600, fontSize: 12, width: 100, overflow: 'truncate' }
        },
        series: [{
            type: 'bar',
            data: categoryTotals,
            barMaxWidth: 24,
            showBackground: true,
            backgroundStyle: { color: '#fafaf9', borderRadius: [0, 8, 8, 0] },
            itemStyle: { color: '#ea580c', borderRadius: [0, 8, 8, 0] },
            label: {
                show: true,
                position: 'right',
                color: '#44403c',
                fontWeight: 700,
                fontSize: 11,
                formatter: function (p) { return rm(p.value); }
            }
        }]
    });

    makeChart('chartTop', {
        grid: { left: 8, right: 50, top: 8, bottom: 8, containLabel: true },
        tooltip: {
            trigger: 'axis',
            axisPointer: { type: 'shadow' },
            backgroundColor: '#1c1917',
            borderWidth: 0,
            textStyle: { color: '#fff', fontSize: 12 }
        },
        xAxis: {
            type: 'value',
            splitLine: { lineStyle: { color: '#f5f5f4', type: 'dashed' } },
            axisLabel: { color: '#78716c', fontSize: 11 }
        },
        yAxis: {
            type: 'category',
            data: topNames,
            inverse: true,
            axisLine: { show: false },
            axisTick: { show: false },
            axisLabel: { color: '#44403c', fontWeight: 600, fontSize: 12, width: 115, overflow: 'truncate' }
        },
        series: [{
            type: 'bar',
            data: topQty,
            barMaxWidth: 24,
            showBackground: true,
            backgroundStyle: { color: '#fafaf9', borderRadius: [0, 8, 8, 0] },
            itemStyle: { color: '#10b981', borderRadius: [0, 8, 8, 0] },
            label: {
                show: true,
                position: 'right',
                color: '#44403c',
                fontWeight: 700,
                fontSize: 11
            }
        }]
    });

    makeChart('chartStatus', {
        tooltip: {
            trigger: 'item',
            backgroundColor: '#1c1917',
            borderWidth: 0,
            textStyle: { color: '#fff', fontSize: 12 },
            formatter: '{b}: <strong>{c}</strong> ({d}%)'
        },
        legend: {
            bottom: 0,
            icon: 'circle',
            itemWidth: 8,
            itemHeight: 8,
            textStyle: { color: '#57534e', fontSize: 11, fontWeight: 600 }
        },
        title: {
            text: String(totalStatusOrders),
            subtext: 'Orders',
            left: 'center',
            top: '35%',
            itemGap: 2,
            textStyle: { fontSize: 22, fontWeight: 800, color: '#1c1917' },
            subtextStyle: { fontSize: 11, fontWeight: 600, color: '#78716c' }
        },
        series: [{
            type: 'pie',
            radius: ['50%', '74%'],
            center: ['50%', '43%'],
            avoidLabelOverlap: true,
            itemStyle: { borderRadius: 6, borderColor: '#fff', borderWidth: 3 },
            label: { show: false },
            data: statusData
        }]
    });

    window.addEventListener('resize', function () {
        charts.forEach(function (c) { c.resize(); });
    });
</script>
@endpush
