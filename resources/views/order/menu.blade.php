<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>DineFlow Menu - Table {{ $table->table_number }}</title>
</head>
<body>
    <h1>Table {{ $table->table_number }}</h1>

    @foreach ($categories as $category)
        <h2>{{ $category->name }}</h2>

        @foreach ($category->foodItems as $item)
            <div>
                <strong>{{ $item->name }}</strong> - RM {{ number_format($item->price, 2) }}
                <p>{{ $item->description }}</p>
                <p>Status: {{ $item->status }}</p>
            </div>
        @endforeach
    @endforeach
</body>
</html>
