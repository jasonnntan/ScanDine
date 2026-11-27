@extends('layouts.staff')

@section('title', 'My Profile')

@section('content')
<div class="max-w-3xl mx-auto p-4 md:p-6 space-y-5">

    @php
        $user = $user ?? auth()->user();
        $initials = collect(explode(' ', trim($user->name)))
            ->filter()
            ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->take(2)
            ->implode('');
    @endphp

    {{-- Header Card --}}
    <div class="bg-white rounded-2xl border border-stone-200/80 shadow-sm p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-orange-500 to-red-700 text-white font-extrabold text-lg flex items-center justify-center shadow-sm flex-shrink-0">
                {{ $initials ?: 'ST' }}
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-xl font-extrabold text-stone-900 tracking-tight">{{ $user->name }}</h1>
                    <span class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full {{ $user->role === 'owner' ? 'bg-orange-100 text-orange-700' : ($user->role === 'manager' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800') }}">
                        {{ $user->role }}
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-stone-500 mt-0.5">{{ $user->email }}</p>
            </div>
        </div>

        <a href="{{ route('staff.orders.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-stone-200 hover:bg-stone-50 text-xs font-bold text-stone-700 transition self-start sm:self-auto">
            <i class="fa-solid fa-arrow-left text-stone-400" aria-hidden="true"></i>
            <span>Back to Orders</span>
        </a>
    </div>

    {{-- 1. Profile Information --}}
    @if (Route::has('profile.update'))
        <div class="bg-white rounded-2xl border border-stone-200/80 shadow-sm p-5 sm:p-6">
            <div class="flex items-center gap-2.5 pb-4 mb-4 border-b border-stone-100">
                <div class="w-8 h-8 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-user-pen" aria-hidden="true"></i>
                </div>
                <div>
                    <h2 class="text-base font-extrabold text-stone-900">Profile Information</h2>
                    <p class="text-xs text-stone-400">Update your display name and login email address</p>
                </div>
            </div>

            @if (session('status') === 'profile-updated')
                <div class="mb-4 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-600" aria-hidden="true"></i>
                    <span>Profile details saved successfully.</span>
                </div>
            @endif

            @if (Route::has('verification.send') && $user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                    @csrf
                </form>
            @endif

            <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf
                @method('patch')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-xs font-bold text-stone-600 uppercase tracking-wide mb-1.5">Full Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name"
                               class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm font-medium text-stone-900 focus:outline-none transition">
                        @error('name')
                            <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold text-stone-600 uppercase tracking-wide mb-1.5">Email Address</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                               class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm font-medium text-stone-900 focus:outline-none transition">
                        @error('email')
                            <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                @if (Route::has('verification.send') && $user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800 space-y-1">
                        <p class="font-semibold">Your email address is unverified.</p>
                        <button form="send-verification" class="underline font-bold hover:text-amber-950">
                            Click here to re-send the verification email.
                        </button>
                        @if (session('status') === 'verification-link-sent')
                            <p class="font-bold text-emerald-700 pt-1">A new verification link has been sent to your email.</p>
                        @endif
                    </div>
                @endif

                <div class="flex justify-end pt-1">
                    <button type="submit" class="inline-flex items-center gap-2 bg-orange-600 hover:bg-orange-700 text-white font-bold px-5 py-2.5 rounded-xl text-xs shadow-sm transition">
                        <i class="fa-solid fa-check" aria-hidden="true"></i>
                        <span>Save Profile</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- 2. Update Password --}}
    @if (Route::has('password.update'))
        <div class="bg-white rounded-2xl border border-stone-200/80 shadow-sm p-5 sm:p-6">
            <div class="flex items-center gap-2.5 pb-4 mb-4 border-b border-stone-100">
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-key" aria-hidden="true"></i>
                </div>
                <div>
                    <h2 class="text-base font-extrabold text-stone-900">Change Password</h2>
                    <p class="text-xs text-stone-400">Use a strong password with at least 8 characters</p>
                </div>
            </div>

            @if (session('status') === 'password-updated')
                <div class="mb-4 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-600" aria-hidden="true"></i>
                    <span>Password updated successfully.</span>
                </div>
            @endif

            <form method="post" action="{{ route('password.update') }}" class="space-y-4">
                @csrf
                @method('put')

                <div>
                    <label for="update_password_current_password" class="block text-xs font-bold text-stone-600 uppercase tracking-wide mb-1.5">Current Password</label>
                    <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password"
                           class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm text-stone-900 focus:outline-none transition">
                    @foreach ($errors->updatePassword->get('current_password') as $message)
                        <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p>
                    @endforeach
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="update_password_password" class="block text-xs font-bold text-stone-600 uppercase tracking-wide mb-1.5">New Password</label>
                        <input id="update_password_password" name="password" type="password" autocomplete="new-password"
                               class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm text-stone-900 focus:outline-none transition">
                        @foreach ($errors->updatePassword->get('password') as $message)
                            <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p>
                        @endforeach
                    </div>

                    <div>
                        <label for="update_password_password_confirmation" class="block text-xs font-bold text-stone-600 uppercase tracking-wide mb-1.5">Confirm New Password</label>
                        <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                               class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm text-stone-900 focus:outline-none transition">
                        @foreach ($errors->updatePassword->get('password_confirmation') as $message)
                            <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end pt-1">
                    <button type="submit" class="inline-flex items-center gap-2 bg-stone-900 hover:bg-stone-800 text-white font-bold px-5 py-2.5 rounded-xl text-xs shadow-sm transition">
                        <i class="fa-solid fa-shield-halved" aria-hidden="true"></i>
                        <span>Update Password</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- 3. Danger Zone (Delete Own Account) --}}
    @if (Route::has('profile.destroy'))
        <div class="bg-white rounded-2xl border border-red-200/80 shadow-sm p-5 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-base font-extrabold text-red-700">Delete Account</h2>
                    <p class="text-xs text-stone-500 mt-0.5">Permanently remove your account and revoke access to the staff portal.</p>
                    @foreach ($errors->userDeletion->get('password') as $message)
                        <p class="text-xs font-bold text-red-600 mt-1.5">{{ $message }}</p>
                    @endforeach
                </div>

                <button type="button" onclick="openDeleteOwnAccountModal()" class="inline-flex items-center gap-1.5 bg-red-50 hover:bg-red-100 border border-red-200 text-red-700 font-bold px-4 py-2.5 rounded-xl text-xs transition self-start sm:self-auto flex-shrink-0">
                    <i class="fa-solid fa-trash" aria-hidden="true"></i>
                    <span>Delete Account</span>
                </button>
            </div>
        </div>

        {{-- Confirm Account Deletion Modal --}}
        <div id="deleteOwnAccountModal" class="fixed inset-0 bg-stone-950/60 backdrop-blur-[1px] z-50 {{ $errors->userDeletion->isNotEmpty() ? 'flex' : 'hidden' }} items-center justify-center p-4" onclick="if (event.target === this) closeDeleteOwnAccountModal()">
            <div class="bg-white rounded-2xl w-full max-w-md overflow-hidden shadow-2xl">
                <div class="p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-base flex-shrink-0">
                            <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-stone-900">Delete your account?</h3>
                            <p class="text-xs text-stone-500">Enter your password to permanently confirm.</p>
                        </div>
                    </div>

                    <form method="post" action="{{ route('profile.destroy') }}" class="space-y-4 mt-4">
                        @csrf
                        @method('delete')

                        <div>
                            <label for="delete_account_password" class="block text-xs font-bold text-stone-600 uppercase tracking-wide mb-1.5">Password</label>
                            <input id="delete_account_password" name="password" type="password" required placeholder="Enter your current password"
                                   class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-200 focus:bg-white focus:border-red-500 rounded-xl text-sm text-stone-900 focus:outline-none">
                            @foreach ($errors->userDeletion->get('password') as $message)
                                <p class="text-xs font-semibold text-red-600 mt-1">{{ $message }}</p>
                            @endforeach
                        </div>

                        <div class="flex gap-2">
                            <button type="button" onclick="closeDeleteOwnAccountModal()" class="flex-1 border border-stone-200 hover:bg-stone-50 text-stone-700 font-bold px-4 py-2.5 rounded-xl text-xs transition">Cancel</button>
                            <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs transition">Yes, Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
    function openDeleteOwnAccountModal() {
        var modal = document.getElementById('deleteOwnAccountModal');
        if (!modal) { return; }
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeDeleteOwnAccountModal() {
        var modal = document.getElementById('deleteOwnAccountModal');
        if (!modal) { return; }
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>
@endpush
