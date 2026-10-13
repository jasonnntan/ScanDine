<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tables</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="max-w-5xl mx-auto p-4 md:p-6">

        <h1 class="text-2xl font-bold text-gray-800 mb-4">Restaurant Tables</h1>

        @if (session('success'))
            <p class="bg-green-100 text-green-800 p-3 rounded-xl mb-4 break-all">{{ session('success') }}</p>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            @foreach ($tables as $table)
                <div class="bg-white rounded-xl shadow-md p-4 text-center border-2 {{ $table->is_occupied ? 'border-red-500' : 'border-green-500' }}">

                    <h2 class="text-xl font-bold text-gray-800">Table {{ $table->table_number }}</h2>
                    <p class="font-semibold mb-3 {{ $table->is_occupied ? 'text-red-600' : 'text-green-600' }}">
                        {{ $table->is_occupied ? 'Occupied' : 'Free' }}
                    </p>

                    @if ($table->is_occupied)
                        <div class="flex justify-center mb-2">
                            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)->generate(url('/order/' . $table->qr_token)) !!}
                        </div>
                        <p class="text-xs text-gray-400 break-all mb-3">{{ url('/order/' . $table->qr_token) }}</p>

                        <a href="{{ route('staff.tables.print', $table->id) }}" target="_blank" class="block w-full border border-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg text-sm mb-2">🖨️ Print QR Slip</a>

                        <form action="{{ route('staff.tables.close', $table->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-red-600 text-white font-semibold px-4 py-2 rounded-lg text-sm">Close Table</button>
                        </form>
                    @else
                        <form action="{{ route('staff.tables.open', $table->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-green-600 text-white font-semibold px-4 py-2 rounded-lg text-sm">Open Table</button>
                        </form>
                    @endif

                </div>
            @endforeach
        </div>
    </div>
</body>
</html>
