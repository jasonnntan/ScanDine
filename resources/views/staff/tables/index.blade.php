<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Manage Tables</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <h1>Restaurant Tables</h1>

    @if (session('success'))
        <p class="success-message">{{ session('success') }}</p>
    @endif

    <div style="display: flex; flex-wrap: wrap; gap: 15px;">
        @foreach ($tables as $table)
            <div class="food-card" style="width: 220px; text-align: center; {{ $table->is_occupied ? 'border: 2px solid #e74c3c;' : 'border: 2px solid #4CAF50;' }}">
                <h2 style="margin-top: 0;">Table {{ $table->table_number }}</h2>
                <p style="font-weight: bold; color: {{ $table->is_occupied ? '#e74c3c' : '#4CAF50' }};">
                    {{ $table->is_occupied ? 'Occupied' : 'Free' }}
                </p>

                @if ($table->is_occupied)
                    {!! QrCode::size(150)->generate(url('/order/' . $table->qr_token)) !!}
                    <p style="font-size: 12px; word-break: break-all;">{{ url('/order/' . $table->qr_token) }}</p>
                    <form action="{{ route('staff.tables.close', $table->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn">Close Table</button>
                    </form>
                @else
                    <form action="{{ route('staff.tables.open', $table->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn">Open Table</button>
                    </form>
                @endif
            </div>
        @endforeach
    </div>
</body>
</html>
