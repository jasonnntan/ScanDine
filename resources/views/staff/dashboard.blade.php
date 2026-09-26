<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Dashboard</title>
</head>
<body>
    <h1>Sales Dashboard</h1>

    <p>Today's Revenue: RM {{ number_format($todayRevenue, 2) }}</p>
    <p>Today's Orders: {{ $todayOrderCount }}</p>
    <p>Best Seller: {{ $bestSeller->item_name_snapshot ?? 'No orders yet' }} ({{ $bestSeller->total_sold ?? 0 }} sold)</p>

        <div id="salesChart" style="width: 600px; height: 400px;"></div>

    <script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
    <script>
        var chart = echarts.init(document.getElementById('salesChart'));

        var categoryNames = {!! json_encode($salesByCategory->pluck('category_name')) !!};
        var salesData = {!! json_encode($salesByCategory->pluck('total_sales')) !!};

        var option = {
            title: { text: 'Sales by Category' },
            xAxis: { type: 'category', data: categoryNames },
            yAxis: { type: 'value' },
            series: [{
                data: salesData,
                type: 'bar'
            }]
        };

        chart.setOption(option);
    </script>
</body>
</html>
