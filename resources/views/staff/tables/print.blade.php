<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Table {{ $table->table_number }} QR</title>
</head>
<body onload="window.print()" style="text-align: center; font-family: monospace;">
    <h2>DineFlow</h2>
    <h3>Table {{ $table->table_number }}</h3>
    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate(url('/order/' . $table->qr_token)) !!}
    <p>Scan to order</p>
</body>
</html>
