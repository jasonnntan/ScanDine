<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Add Food Item</title>
</head>
<body>
    <h1>Add New Food Item</h1>

    <form action="{{ route('staff.foodItems.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <label>Image: <input type="file" name="image"></label><br>
        <label>Category:
            <select name="category_id">
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </label><br>

        <label>Name: <input type="text" name="name"></label><br>
        <label>Description: <textarea name="description"></textarea></label><br>
        <label>Price: <input type="number" step="0.01" name="price"></label><br>
        <label>Unit Cost: <input type="number" step="0.01" name="unit_cost"></label><br>

        <button type="submit">Add Item</button>
    </form>
</body>
</html>
