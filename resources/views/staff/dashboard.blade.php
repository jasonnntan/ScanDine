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
</body>
</html>
