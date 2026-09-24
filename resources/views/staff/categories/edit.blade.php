<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Edit Category</title>
</head>
<body>
    <h1>Edit Category</h1>

    <form action="{{ route('staff.categories.update', $category->id) }}" method="POST">
        @csrf
        @method('PUT')
        <label>Name: <input type="text" name="name" value="{{ $category->name }}"></label><br>
        <label>Description: <textarea name="description">{{ $category->description }}</textarea></label><br>
        <button type="submit">Save Changes</button>
    </form>
</body>
</html>
