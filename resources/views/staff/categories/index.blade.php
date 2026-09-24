<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Manage Categories</title>
</head>
<body>
    <h1>Manage Categories</h1>

    @if (session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <a href="{{ route('staff.categories.create') }}">Add New Category</a>

    @foreach ($categories as $category)
        <div style="border: 1px solid #ccc; margin-bottom: 10px; padding: 10px;">
            <strong>{{ $category->name }}</strong>
            <p>{{ $category->description }}</p>

            <a href="{{ route('staff.categories.edit', $category->id) }}">Edit</a>

            <form action="{{ route('staff.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Deleting this category will also delete ALL food items inside it. Are you sure?');" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit">Delete</button>
            </form>
        </div>
    @endforeach
</body>
</html>
