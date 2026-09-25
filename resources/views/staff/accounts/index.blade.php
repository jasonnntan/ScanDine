<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Accounts</title>
</head>
<body>
    <h1>Manage Staff & Manager Accounts</h1>

    @if (session('success'))
    <p style="color: green;">{{  session('success') }}</p>
    @endif

    @if (session('error'))
    <p style="color: red;">{{  session('error') }}</p>
    @endif

    <a href="{{  route('staff.accounts.create') }}">Add New Account</a>

    @foreach ($users as $user)
    <div style="border: 1px solid #scc; margin-bottom: 10px; padding: 10px;">
        <strong>{{ $user->name }}</strong> ({{ $user->role }}) - {{ $user->email }}

        <form action="{{ route('staff.accounts.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this account?');" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="submit">Delete</button>
        </form>
    </div>
    @endforeach
</body>
</html>
