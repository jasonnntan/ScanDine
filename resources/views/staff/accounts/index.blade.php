@extends('layouts.staff')

@section('title', 'Accounts')

@section('content')
<div class="max-w-7xl mx-auto p-4 md:p-6 space-y-5">

    @php
        $managerCount = $users->where('role', 'manager')->count();
        $staffCount = $users->where('role', 'staff')->count();
    @endphp

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Team Accounts</h1>
            <div class="flex flex-wrap items-center gap-2 mt-1 text-xs">
                <span class="font-semibold text-stone-500">{{ $users->count() }} account(s)</span>
                <span class="text-stone-300">•</span>
                <span class="font-bold text-purple-800 bg-purple-50 px-2.5 py-0.5 rounded-full">{{ $managerCount }} Manager(s)</span>
                <span class="font-bold text-blue-800 bg-blue-50 px-2.5 py-0.5 rounded-full">{{ $staffCount }} Staff</span>
            </div>
        </div>

        <button type="button" onclick="openAddAccountModal()" class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs shadow-sm transition self-start sm:self-auto">
            <i class="fa-solid fa-user-plus" aria-hidden="true"></i>
            <span>Add New Account</span>
        </button>
    </div>

    {{-- Search & Role Filter Toolbar --}}
    <div class="bg-white rounded-2xl border border-stone-200/80 shadow-sm p-3.5 flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <i class="fa-solid fa-magnifying-glass text-stone-400 text-xs absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" aria-hidden="true"></i>
            <input type="text" id="accountSearchInput" oninput="filterAccounts()" placeholder="Search by name or email..."
                   class="w-full pl-9 pr-3 py-2 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm text-stone-800 focus:outline-none">
        </div>

        <div class="flex items-center gap-1.5 bg-stone-100 p-1 rounded-xl self-start sm:self-auto">
            <button type="button" onclick="setRoleFilter('all', this)" class="role-filter-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-white text-stone-900 shadow-xs transition">
                All
            </button>
            <button type="button" onclick="setRoleFilter('manager', this)" class="role-filter-btn px-3 py-1.5 rounded-lg text-xs font-semibold text-stone-600 hover:text-stone-900 transition">
                Managers
            </button>
            <button type="button" onclick="setRoleFilter('staff', this)" class="role-filter-btn px-3 py-1.5 rounded-lg text-xs font-semibold text-stone-600 hover:text-stone-900 transition">
                Staff
            </button>
        </div>
    </div>

    @if ($users->isEmpty())
        <div class="bg-white rounded-2xl border border-stone-200/80 shadow-sm p-12 text-center">
            <div class="w-12 h-12 rounded-full bg-stone-100 text-stone-400 flex items-center justify-center mx-auto mb-3 text-lg">
                <i class="fa-solid fa-users" aria-hidden="true"></i>
            </div>
            <p class="font-bold text-stone-800">No staff or manager accounts yet</p>
            <p class="text-xs text-stone-400 mt-1">Create an account so team members can log in and manage orders.</p>
        </div>
    @else
        <div id="emptyAccountFilter" class="hidden bg-white rounded-2xl border border-stone-200/80 shadow-sm p-10 text-center">
            <p class="font-bold text-stone-700">No matching accounts found</p>
            <p class="text-xs text-stone-400 mt-1">Try a different search keyword or role filter.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach ($users as $user)
                @php
                    $canManage = auth()->user()->role === 'owner' || $user->role === 'staff';
                    $initials = collect(explode(' ', trim($user->name)))
                        ->filter()
                        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
                        ->take(2)
                        ->implode('');
                @endphp

                <div class="account-card bg-white rounded-2xl border border-stone-200/80 shadow-sm p-5 flex flex-col justify-between transition hover:shadow-md"
                     data-name="{{ strtolower($user->name) }}"
                     data-email="{{ strtolower($user->email) }}"
                     data-role="{{ $user->role }}">

                    <div>
                        <div class="flex items-start justify-between gap-3 mb-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-11 h-11 flex-shrink-0 rounded-full font-extrabold text-sm flex items-center justify-center {{ $user->role === 'manager' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $initials ?: 'U' }}
                                </div>

                                <div class="min-w-0">
                                    <p class="font-extrabold text-stone-900 truncate">{{ $user->name }}</p>
                                    <p class="text-xs text-stone-500 truncate mt-0.5">{{ $user->email }}</p>
                                </div>
                            </div>

                            <span class="flex-shrink-0 text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full {{ $user->role === 'manager' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $user->role }}
                            </span>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-stone-100">
                        @if ($canManage)
                            <div class="flex gap-2">
                                <button type="button"
                                        onclick="openEditAccountModal(this)"
                                        data-action="{{ route('staff.accounts.update', $user->id) }}"
                                        data-name="{{ $user->name }}"
                                        data-email="{{ $user->email }}"
                                        data-role="{{ $user->role }}"
                                        class="flex-1 flex items-center justify-center gap-1.5 border border-stone-200 hover:bg-stone-50 text-stone-700 font-bold px-3 py-2 rounded-xl text-xs transition">
                                    <i class="fa-solid fa-pen text-[11px] text-stone-400" aria-hidden="true"></i>
                                    <span>Edit</span>
                                </button>

                                <button type="button"
                                        onclick="openDeleteAccountModal(this)"
                                        data-action="{{ route('staff.accounts.destroy', $user->id) }}"
                                        data-name="{{ $user->name }}"
                                        data-role="{{ $user->role }}"
                                        class="flex-1 flex items-center justify-center gap-1.5 border border-red-200 bg-red-50/60 hover:bg-red-100 text-red-700 font-bold px-3 py-2 rounded-xl text-xs transition">
                                    <i class="fa-solid fa-trash text-[11px]" aria-hidden="true"></i>
                                    <span>Delete</span>
                                </button>
                            </div>
                        @else
                            <div class="flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl bg-stone-50 border border-stone-100 text-xs font-semibold text-stone-400">
                                <i class="fa-solid fa-shield-halved text-stone-400" aria-hidden="true"></i>
                                <span>Protected • Owner access only</span>
                            </div>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- Add Account Modal --}}
<div id="addAccountModal" class="fixed inset-0 bg-stone-950/60 backdrop-blur-[1px] z-50 hidden items-center justify-center p-4" onclick="if (event.target === this) closeAddAccountModal()">
    <div class="bg-white rounded-2xl w-full max-w-md max-h-[90vh] overflow-y-auto shadow-2xl">
        <div class="flex justify-between items-center px-5 py-4 border-b border-stone-100">
            <h2 class="text-lg font-extrabold text-stone-900">Add New Account</h2>
            <button type="button" onclick="closeAddAccountModal()" class="w-8 h-8 rounded-full bg-stone-100 hover:bg-stone-200 text-stone-500 flex items-center justify-center text-lg font-bold">&times;</button>
        </div>

        <form action="{{ route('staff.accounts.store') }}" method="POST" class="p-5 flex flex-col gap-3.5">
            @csrf

            <label class="text-xs font-bold text-stone-600 uppercase tracking-wide">Full Name
                <input type="text" name="name" required placeholder="e.g. Aiman Hakim" class="mt-1 w-full px-3 py-2.5 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm font-normal focus:outline-none">
            </label>

            <label class="text-xs font-bold text-stone-600 uppercase tracking-wide">Email Address
                <input type="email" name="email" required placeholder="staff@restaurant.com" class="mt-1 w-full px-3 py-2.5 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm font-normal focus:outline-none">
            </label>

            <label class="text-xs font-bold text-stone-600 uppercase tracking-wide">Password
                <input type="password" name="password" required minlength="8" autocomplete="new-password" placeholder="Minimum 8 characters" class="mt-1 w-full px-3 py-2.5 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm font-normal focus:outline-none">
            </label>

            <label class="text-xs font-bold text-stone-600 uppercase tracking-wide">Role
                <select name="role" class="mt-1 w-full px-3 py-2.5 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm font-medium focus:outline-none">
                    <option value="staff">Staff (Orders, Tables & Menu)</option>
                    @if (auth()->user()->role === 'owner')
                        <option value="manager">Manager (Includes Sales & Staff)</option>
                    @endif
                </select>
            </label>

            <div class="flex gap-2 pt-2">
                <button type="button" onclick="closeAddAccountModal()" class="flex-1 border border-stone-200 hover:bg-stone-50 text-stone-700 font-bold px-4 py-2.5 rounded-xl text-xs transition">Cancel</button>
                <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs transition">Create Account</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Account Modal --}}
<div id="editAccountModal" class="fixed inset-0 bg-stone-950/60 backdrop-blur-[1px] z-50 hidden items-center justify-center p-4" onclick="if (event.target === this) closeEditAccountModal()">
    <div class="bg-white rounded-2xl w-full max-w-md max-h-[90vh] overflow-y-auto shadow-2xl">
        <div class="flex justify-between items-center px-5 py-4 border-b border-stone-100">
            <h2 class="text-lg font-extrabold text-stone-900">Edit Account</h2>
            <button type="button" onclick="closeEditAccountModal()" class="w-8 h-8 rounded-full bg-stone-100 hover:bg-stone-200 text-stone-500 flex items-center justify-center text-lg font-bold">&times;</button>
        </div>

        <form id="editAccountForm" action="" method="POST" class="p-5 flex flex-col gap-3.5">
            @csrf
            @method('PUT')

            <label class="text-xs font-bold text-stone-600 uppercase tracking-wide">Full Name
                <input type="text" name="name" required class="mt-1 w-full px-3 py-2.5 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm font-normal focus:outline-none">
            </label>

            <label class="text-xs font-bold text-stone-600 uppercase tracking-wide">Email Address
                <input type="email" name="email" required class="mt-1 w-full px-3 py-2.5 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm font-normal focus:outline-none">
            </label>

            <label class="text-xs font-bold text-stone-600 uppercase tracking-wide">New Password
                <input type="password" name="password" minlength="8" autocomplete="new-password" placeholder="Leave blank to keep current" class="mt-1 w-full px-3 py-2.5 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm font-normal focus:outline-none">
            </label>

            @if (auth()->user()->role === 'owner')
                <label class="text-xs font-bold text-stone-600 uppercase tracking-wide">Role
                    <select name="role" class="mt-1 w-full px-3 py-2.5 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm font-medium focus:outline-none">
                        <option value="staff">Staff</option>
                        <option value="manager">Manager</option>
                    </select>
                </label>
            @endif

            <div class="flex gap-2 pt-2">
                <button type="button" onclick="closeEditAccountModal()" class="flex-1 border border-stone-200 hover:bg-stone-50 text-stone-700 font-bold px-4 py-2.5 rounded-xl text-xs transition">Cancel</button>
                <button type="submit" class="flex-1 bg-orange-600 hover:bg-orange-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs transition">Save Changes</button>
            </div>
        </form>
    </div>
</div>

{{-- Delete Account Modal --}}
<div id="deleteAccountModal" class="fixed inset-0 bg-stone-950/60 backdrop-blur-[1px] z-50 hidden items-center justify-center p-4" onclick="if (event.target === this) closeDeleteAccountModal()">
    <div class="bg-white rounded-2xl w-full max-w-sm overflow-hidden shadow-2xl">
        <div class="p-5 text-center">
            <div class="mx-auto mb-3 w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-user-xmark" aria-hidden="true"></i>
            </div>
            <h2 class="text-lg font-extrabold text-stone-900">Delete account?</h2>
            <p class="text-xs sm:text-sm text-stone-500 mt-1.5">
                Are you sure you want to delete
                <strong class="text-stone-800"><span id="deleteAccountName"></span></strong>
                (<span id="deleteAccountRole" class="capitalize"></span>)?
            </p>
            <p class="text-xs font-semibold text-red-600 mt-2">They will no longer be able to log in. This cannot be undone.</p>
        </div>

        <form id="deleteAccountForm" action="" method="POST" class="flex gap-2 px-5 pb-5">
            @csrf
            @method('DELETE')
            <button type="button" onclick="closeDeleteAccountModal()" class="flex-1 border border-stone-200 hover:bg-stone-50 text-stone-700 font-bold px-4 py-2.5 rounded-xl text-xs transition">Cancel</button>
            <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs transition">Yes, delete</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    var activeRoleFilter = 'all';

    function setRoleFilter(role, btn) {
        activeRoleFilter = role;
        document.querySelectorAll('.role-filter-btn').forEach(function (el) {
            el.className = 'role-filter-btn px-3 py-1.5 rounded-lg text-xs font-semibold text-stone-600 hover:text-stone-900 transition';
        });
        btn.className = 'role-filter-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-white text-stone-900 shadow-xs transition';
        filterAccounts();
    }

    function filterAccounts() {
        var input = document.getElementById('accountSearchInput');
        if (!input) { return; }

        var query = input.value.trim().toLowerCase();
        var visibleCount = 0;

        document.querySelectorAll('.account-card').forEach(function (card) {
            var matchesQuery = query === '' ||
                card.dataset.name.includes(query) ||
                card.dataset.email.includes(query);
            var matchesRole = activeRoleFilter === 'all' || card.dataset.role === activeRoleFilter;

            if (matchesQuery && matchesRole) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        var empty = document.getElementById('emptyAccountFilter');
        if (empty) {
            empty.classList.toggle('hidden', visibleCount > 0);
        }
    }

    function openAddAccountModal() {
        document.getElementById('addAccountModal').classList.remove('hidden');
        document.getElementById('addAccountModal').classList.add('flex');
    }

    function closeAddAccountModal() {
        document.getElementById('addAccountModal').classList.add('hidden');
        document.getElementById('addAccountModal').classList.remove('flex');
    }

    function openEditAccountModal(button) {
        var form = document.getElementById('editAccountForm');

        form.action = button.dataset.action;
        form.querySelector('[name="name"]').value = button.dataset.name;
        form.querySelector('[name="email"]').value = button.dataset.email;
        form.querySelector('[name="password"]').value = '';

        var roleField = form.querySelector('[name="role"]');
        if (roleField) {
            roleField.value = button.dataset.role;
        }

        document.getElementById('editAccountModal').classList.remove('hidden');
        document.getElementById('editAccountModal').classList.add('flex');
    }

    function closeEditAccountModal() {
        document.getElementById('editAccountModal').classList.add('hidden');
        document.getElementById('editAccountModal').classList.remove('flex');
    }

    function openDeleteAccountModal(button) {
        document.getElementById('deleteAccountForm').action = button.dataset.action;
        document.getElementById('deleteAccountName').innerText = button.dataset.name;
        document.getElementById('deleteAccountRole').innerText = button.dataset.role;

        document.getElementById('deleteAccountModal').classList.remove('hidden');
        document.getElementById('deleteAccountModal').classList.add('flex');
    }

    function closeDeleteAccountModal() {
        document.getElementById('deleteAccountModal').classList.add('hidden');
        document.getElementById('deleteAccountModal').classList.remove('flex');
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeAddAccountModal();
            closeEditAccountModal();
            closeDeleteAccountModal();
        }
    });
</script>
@endpush
