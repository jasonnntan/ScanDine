<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Edit Food Item</title>
</head>
<body>
    <h1>Edit Food Item</h1>

    <form action="{{ route('staff.foodItems.update', $foodItem->id) }}" method="POST">
        @csrf
        @method('PUT')

        <label>Category:
            <select name="category_id">
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ $foodItem->category_id === $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </label><br>

        <label>Name: <input type="text" name="name" value="{{ $foodItem->name }}"></label><br>
        <label>Description: <textarea name="description">{{ $foodItem->description }}</textarea></label><br>
        <label>Price: <input type="number" step="0.01" name="price" value="{{ $foodItem->price }}"></label><br>
        <label>Unit Cost: <input type="number" step="0.01" name="unit_cost" value="{{ $foodItem->unit_cost }}"></label><br>

        <button type="submit">Save Changes</button>
    </form>
</body>
</html>
