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
</body>
</html>
