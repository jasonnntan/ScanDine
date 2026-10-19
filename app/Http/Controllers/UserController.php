<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::whereIn('role', ['staff', 'manager'])->get();

        return view('staff.accounts.index', [
            'users' => $users,
        ]);
    }

    public function create()
    {
        return view('staff.accounts.create');
    }

    public function store(Request $request)
    {
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('staff.accounts.index')->with('success', 'Account created!');
    }

    public function update(Request $request, $id)
    {
        $targetUser = User::findOrFail($id);
        $currentUser = auth()->user();

        if ($targetUser->role === 'owner') {
            return redirect()->route('staff.accounts.index')->with('error', 'Owner accounts cannot be edited here.');
        }

        if ($targetUser->role === 'manager' && $currentUser->role !== 'owner') {
            return redirect()->route('staff.accounts.index')->with('error', 'Only the owner can edit manager accounts.');
        }

        if (!$request->filled('name') || !$request->filled('email')) {
            return redirect()->route('staff.accounts.index')->with('error', 'Name and email are required.');
        }

        $emailTaken = User::where('email', $request->email)
            ->where('id', '!=', $targetUser->id)
            ->exists();

        if ($emailTaken) {
            return redirect()->route('staff.accounts.index')->with('error', 'That email is already used by another account.');
        }

        if ($request->filled('password') && strlen($request->password) < 8) {
            return redirect()->route('staff.accounts.index')->with('error', 'The new password must be at least 8 characters.');
        }

        $role = $targetUser->role;
        if ($currentUser->role === 'owner' && in_array($request->role, ['staff', 'manager'])) {
            $role = $request->role;
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $role,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $targetUser->update($data);

        return redirect()->route('staff.accounts.index')->with('success', 'Account updated!');
    }

    public function destroy($id)
    {
        $targetUser = User::findOrFail($id);
        $currentUser = auth()->user();

        if ($targetUser->role === 'manager' && $currentUser->role !== 'owner') {
            return redirect()->route('staff.accounts.index')->with('error', 'Only the owner can delete manager accounts.');
        }

        $targetUser->delete();

        return redirect()->route('staff.accounts.index')->with('success', 'Account deleted!');
    }
}
