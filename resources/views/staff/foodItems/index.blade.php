<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Staff - Food Items</title>
</head>
<body>
    <h1>Manage Food Items</h1>

    @if (session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    @foreach ($foodItems as $item)
        <div style="border: 1px solid #ccc; margin-bottom: 10px; padding: 10px;">
            <strong>{{ $item->name }}</strong> ({{ $item->category->name }}) - RM {{ number_format($item->price, 2) }}
            <p>Status: {{ $item->status }}</p>

            <form action="{{ route('staff.foodItems.toggleStatus', $item->id) }}" method="POST">
                @csrf
                @method('PUT')
                <button type="submit">
                    Mark as {{ $item->status === 'available' ? 'Out of Stock' : 'Available' }}
                </button>
            </form>

            <a href="{{ route('staff.foodItems.edit', $item->id) }}">Edit</a>

            <form action="{{ route('staff.foodItems.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this item?');" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit">Delete</button>
            </form>

        </div>
    @endforeach
</body>
</html>
