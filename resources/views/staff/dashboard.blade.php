@extends('layouts.staff')

@section('title', 'Sales Dashboard')

@section('content')
<div class="max-w-7xl mx-auto p-4 md:p-6">

    <div class="mb-4">
        <h1 class="text-2xl font-bold text-gray-800">Sales Dashboard</h1>
        <p class="text-sm text-gray-500">{{ $periodLabel }}</p>
    </div>

    {{-- filters --}}
    <form method="GET" action="{{ route('staff.dashboard') }}" class="bg-white rounded-xl shadow-md p-4 mb-4">
        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3">

            <label class="block">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">Period</span>
                <select name="range"
                        onchange="this.form.elements['from'].value = ''; this.form.elements['to'].value = ''; this.form.submit()"
                        class="mt-1 w-full p-2 border border-gray-200 rounded-lg bg-white text-sm text-gray-700">
                    <option value="today" {{ $range === 'today' ? 'selected' : '' }}>Today</option>
                    <option value="7d" {{ $range === '7d' ? 'selected' : '' }}>Last 7 days</option>
                    <option value="30d" {{ $range === '30d' ? 'selected' : '' }}>Last 30 days</option>
                    <option value="month" {{ $range === 'month' ? 'selected' : '' }}>This month</option>
                    <option value="all" {{ $range === 'all' ? 'selected' : '' }}>All time</option>
                    <option value="custom" disabled {{ $range === 'custom' ? 'selected' : '' }}>Custom dates</option>
                </select>
            </label>

            <label class="block">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">From</span>
                <input type="date" name="from" value="{{ $from }}" class="mt-1 w-full p-2 border border-gray-200 rounded-lg text-sm text-gray-700">
            </label>

            <label class="block">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">To</span>
                <input type="date" name="to" value="{{ $to }}" class="mt-1 w-full p-2 border border-gray-200 rounded-lg text-sm text-gray-700">
            </label>

            <label class="block">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">Category</span>
                <select name="category" onchange="this.form.submit()" class="mt-1 w-full p-2 border border-gray-200 rounded-lg bg-white text-sm text-gray-700">
                    <option value="">All categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ (string) $categoryId === (string) $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </label>

            <label class="block">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">Table</span>
                <select name="table" onchange="this.form.submit()" class="mt-1 w-full p-2 border border-gray-200 rounded-lg bg-white text-sm text-gray-700">
                    <option value="">All tables</option>
                    @foreach ($tables as $table)
                        <option value="{{ $table->id }}" {{ (string) $tableId === (string) $table->id ? 'selected' : '' }}>Table {{ $table->table_number }}</option>
                    @endforeach
                </select>
            </label>

            <label class="block">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">Orders counted</span>
                <select name="status" onchange="this.form.submit()" class="mt-1 w-full p-2 border border-gray-200 rounded-lg bg-white text-sm text-gray-700">
                    <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed only</option>
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All except cancelled</option>
                </select>
            </label>

        </div>

        <div class="flex gap-2 mt-3">
            <button type="submit" class="bg-orange-500 text-white font-semibold px-4 py-2 rounded-lg text-sm">Apply</button>
            <a href="{{ route('staff.dashboard') }}" class="border border-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg text-sm">Reset</a>
        </div>
    </form>

    @if ($orderCount === 0 && $status === 'completed')
        <p class="bg-yellow-50 text-yellow-800 text-sm p-3 rounded-xl mb-4">
            No completed orders match these filters. Sales only count once an order is marked Completed on the Orders page.
            To include open orders, set "Orders counted" to "All except cancelled".
        </p>
    @endif

    {{-- KPI cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">

        <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-green-500">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Revenue</p>
            <p class="text-2xl font-extrabold text-gray-800 mt-1">RM {{ number_format($revenue, 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $status === 'completed' ? 'Completed orders' : 'All except cancelled' }}</p>
        </div>

        <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-blue-500">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Orders</p>
            <p class="text-2xl font-extrabold text-gray-800 mt-1">{{ $orderCount }}</p>
            <p class="text-xs text-gray-400 mt-1">In this period</p>
        </div>

        <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-teal-500">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Average order</p>
            <p class="text-2xl font-extrabold text-gray-800 mt-1">RM {{ number_format($avgOrder, 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">Revenue per order</p>
        </div>

        <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-indigo-500">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Items sold</p>
            <p class="text-2xl font-extrabold text-gray-800 mt-1">{{ $itemsSold }}</p>
            <p class="text-xs text-gray-400 mt-1">Total quantity</p>
        </div>

        <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-purple-500">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Estimated profit</p>
            <p class="text-2xl font-extrabold text-gray-800 mt-1">RM {{ number_format($profit, 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">Items without a unit cost are not counted</p>
        </div>

        <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-orange-500">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Best seller</p>
            <p class="text-lg font-extrabold text-gray-800 mt-1 leading-snug break-words">{{ $bestSeller->name ?? 'No data' }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $bestSeller->total_sold ?? 0 }} sold</p>
        </div>

    </div>

    {{-- charts --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">

        <div class="bg-white rounded-xl shadow-md p-4">
            <h2 class="font-bold text-gray-800">Sales over time</h2>
            <p class="text-xs text-gray-400 mb-3">Revenue per day</p>
            @if ($salesByDay->isEmpty())
                <p class="text-center text-gray-500 py-10">No data for these filters.</p>
            @else
                <div id="chartDay" class="w-full h-72"></div>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-md p-4">
            <h2 class="font-bold text-gray-800">Sales by category</h2>
            <p class="text-xs text-gray-400 mb-3">Revenue per category</p>
            @if ($salesByCategory->isEmpty())
                <p class="text-center text-gray-500 py-10">No data for these filters.</p>
            @else
                <div id="chartCategory" class="w-full h-72"></div>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-md p-4">
            <h2 class="font-bold text-gray-800">Top 5 items</h2>
            <p class="text-xs text-gray-400 mb-3">By quantity sold</p>
            @if ($topItems->isEmpty())
                <p class="text-center text-gray-500 py-10">No data for these filters.</p>
            @else
                <div id="chartTop" class="w-full h-72"></div>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-md p-4">
            <h2 class="font-bold text-gray-800">Orders by status</h2>
            <p class="text-xs text-gray-400 mb-3">Uses the period and table filters only, so open orders show up</p>
            @if ($statusChart->isEmpty())
                <p class="text-center text-gray-500 py-10">No data for these filters.</p>
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
    }

    var dayLabels = {!! json_encode($salesByDay->pluck('sales_day')) !!};
    var dayTotals = {!! json_encode($salesByDay->pluck('total')) !!}.map(Number);

    var categoryNames = {!! json_encode($salesByCategory->pluck('category_name')) !!};
    var categoryTotals = {!! json_encode($salesByCategory->pluck('total_sales')) !!}.map(Number);

    var topNames = {!! json_encode($topItems->pluck('name')) !!};
    var topQty = {!! json_encode($topItems->pluck('qty')) !!}.map(Number);

    var statusData = {!! json_encode($statusChart) !!};
    var statusColors = {
        pending: '#eab308',
        preparing: '#3b82f6',
        ready: '#a855f7',
        served: '#6366f1',
        completed: '#22c55e',
        cancelled: '#ef4444'
    };
    statusData.forEach(function (d) {
        d.itemStyle = { color: statusColors[d.name] || '#9ca3af' };
    });

    makeChart('chartDay', {
        grid: { left: 10, right: 20, top: 20, bottom: 10, containLabel: true },
        tooltip: { trigger: 'axis', valueFormatter: rm },
        xAxis: { type: 'category', data: dayLabels, boundaryGap: false },
        yAxis: { type: 'value', axisLabel: { formatter: 'RM {value}' } },
        series: [{
            type: 'line',
            data: dayTotals,
            smooth: true,
            symbolSize: 8,
            itemStyle: { color: '#f97316' },
            areaStyle: { opacity: 0.15 }
        }]
    });

    makeChart('chartCategory', {
        grid: { left: 10, right: 70, top: 10, bottom: 10, containLabel: true },
        tooltip: { trigger: 'axis', axisPointer: { type: 'shadow' }, valueFormatter: rm },
        xAxis: { type: 'value', axisLabel: { formatter: 'RM {value}' } },
        yAxis: { type: 'category', data: categoryNames, inverse: true },
        series: [{
            type: 'bar',
            data: categoryTotals,
            barMaxWidth: 28,
            itemStyle: { color: '#f97316', borderRadius: [0, 6, 6, 0] },
            label: { show: true, position: 'right', formatter: function (p) { return rm(p.value); } }
        }]
    });

    makeChart('chartTop', {
        grid: { left: 10, right: 40, top: 10, bottom: 10, containLabel: true },
        tooltip: { trigger: 'axis', axisPointer: { type: 'shadow' } },
        xAxis: { type: 'value' },
        yAxis: { type: 'category', data: topNames, inverse: true },
        series: [{
            type: 'bar',
            data: topQty,
            barMaxWidth: 28,
            itemStyle: { color: '#16a34a', borderRadius: [0, 6, 6, 0] },
            label: { show: true, position: 'right' }
        }]
    });

    makeChart('chartStatus', {
        tooltip: { trigger: 'item' },
        legend: { bottom: 0 },
        series: [{
            type: 'pie',
            radius: ['45%', '70%'],
            data: statusData,
            label: { formatter: '{b}: {c}' }
        }]
    });

    window.addEventListener('resize', function () {
        charts.forEach(function (c) { c.resize(); });
    });
</script>
@endpush