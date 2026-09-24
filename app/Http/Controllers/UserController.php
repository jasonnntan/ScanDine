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

// testing
