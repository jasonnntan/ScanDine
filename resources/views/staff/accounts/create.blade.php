<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Account</title>
</head>
<body>
    <h1>Add New Account</h1>

    <form action="{{ route('staff.accounts.store') }}" method="POST">
        @csrf
        <label>Name: <input type="text" name="name"></label><br>
        <label>Email: <input type="email" name="email"></label><br>
        <label>Password: <input type="password" name="password"></label><br>
        <label>Role:
            <select name="role">
                <option value="staff">Staff</option>
                <option value="manager">Manager</option>
            </select>
        <label><br>
        <button type="submit">Create Account</button>
    </form>
</body>
</html>
