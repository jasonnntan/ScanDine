<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Add Category</title>
</head>
<body>
    <h1>Add New Category</h1>

    <form action="{{ route('staff.categories.store') }}" method="POST">
        @csrf
        <label>Name: <input type="text" name="name"></label><br>
        <label>Description: <textarea name="description"></textarea></label><br>
        <button type="submit">Add Category</button>
    </form>
</body>
</html>
