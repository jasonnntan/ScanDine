<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Accounts</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="max-w-5xl mx-auto p-4 md:p-6">

        <div class="flex justify-between items-center mb-4 gap-3">
            <h1 class="text-2xl font-bold text-gray-800">Manage Accounts</h1>
            <button type="button" onclick="openAddAccountModal()" class="bg-green-600 text-white font-semibold px-4 py-2 rounded-lg text-sm whitespace-nowrap">+ Add New Account</button>
        </div>

        @if (session('success'))
            <p class="bg-green-100 text-green-800 p-3 rounded-xl mb-4">{{ session('success') }}</p>
        @endif

        @if (session('error'))
            <p class="bg-red-100 text-red-800 p-3 rounded-xl mb-4">{{ session('error') }}</p>
        @endif

        @if ($users->isEmpty())
            <p class="bg-white rounded-xl shadow-md p-6 text-center text-gray-500">No staff or manager accounts yet.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($users as $user)
                    @php
                        $canManage = auth()->user()->role === 'owner' || $user->role === 'staff';
                    @endphp

                    <div class="bg-white rounded-xl shadow-md p-4 flex flex-col">

                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-12 h-12 flex-shrink-0 rounded-full bg-orange-100 text-orange-600 font-bold text-lg flex items-center justify-center">
                                {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="font-bold text-gray-800 break-words">{{ $user->name }}</p>
                                <p class="text-sm text-gray-500 break-all">{{ $user->email }}</p>
                            </div>
                        </div>

                        <span class="self-start text-xs font-semibold px-3 py-1 rounded-full mb-4 {{ $user->role === 'manager' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $user->role }}
                        </span>

                        <div class="mt-auto">
                            @if ($canManage)
                                <div class="flex gap-2">
                                    <button type="button"
                                            onclick="openEditAccountModal(this)"
                                            data-action="{{ route('staff.accounts.update', $user->id) }}"
                                            data-name="{{ $user->name }}"
                                            data-email="{{ $user->email }}"
                                            data-role="{{ $user->role }}"
                                            class="flex-1 border border-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg text-sm">Edit</button>

                                    <button type="button"
                                            onclick="openDeleteAccountModal(this)"
                                            data-action="{{ route('staff.accounts.destroy', $user->id) }}"
                                            data-name="{{ $user->name }}"
                                            data-role="{{ $user->role }}"
                                            class="flex-1 bg-red-600 text-white font-semibold px-4 py-2 rounded-lg text-sm">Delete</button>
                                </div>
                            @else
                                <div class="flex gap-2">
                                    <button type="button" disabled class="flex-1 bg-gray-100 text-gray-400 font-semibold px-4 py-2 rounded-lg text-sm cursor-not-allowed">Edit</button>
                                    <button type="button" disabled class="flex-1 bg-gray-100 text-gray-400 font-semibold px-4 py-2 rounded-lg text-sm cursor-not-allowed">Delete</button>
                                </div>
                                <p class="text-xs text-gray-400 mt-1 text-center">Only the owner can edit or delete a manager.</p>
                            @endif
                        </div>

                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- add account modal --}}
    <div id="addAccountModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4" onclick="if (event.target === this) closeAddAccountModal()">
        <div class="bg-white rounded-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">

            <div class="flex justify-between items-center p-4 border-b">
                <h2 class="text-lg font-bold text-gray-800">Add New Account</h2>
                <button type="button" onclick="closeAddAccountModal()" class="text-2xl text-gray-400">&times;</button>
            </div>

            <form action="{{ route('staff.accounts.store') }}" method="POST" class="p-4 flex flex-col gap-3">
                @csrf

                <label class="text-sm font-semibold text-gray-700">Name
                    <input type="text" name="name" required class="mt-1 w-full p-2 border border-gray-200 rounded-lg font-normal">
                </label>

                <label class="text-sm font-semibold text-gray-700">Email
                    <input type="email" name="email" required class="mt-1 w-full p-2 border border-gray-200 rounded-lg font-normal">
                </label>

                <label class="text-sm font-semibold text-gray-700">Password
                    <input type="password" name="password" required minlength="8" autocomplete="new-password" class="mt-1 w-full p-2 border border-gray-200 rounded-lg font-normal">
                    <span class="block text-xs text-gray-400 font-normal mt-1">At least 8 characters.</span>
                </label>

                <label class="text-sm font-semibold text-gray-700">Role
                    <select name="role" class="mt-1 w-full p-2 border border-gray-200 rounded-lg bg-white font-normal">
                        <option value="staff">Staff</option>
                        @if (auth()->user()->role === 'owner')
                            <option value="manager">Manager</option>
                        @endif
                    </select>
                </label>

                <div class="flex gap-2 pt-2">
                    <button type="button" onclick="closeAddAccountModal()" class="flex-1 border border-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg text-sm">Cancel</button>
                    <button type="submit" class="flex-1 bg-green-600 text-white font-semibold px-4 py-2 rounded-lg text-sm">Create Account</button>
                </div>
            </form>

        </div>
    </div>

    {{-- edit account modal --}}
    <div id="editAccountModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4" onclick="if (event.target === this) closeEditAccountModal()">
        <div class="bg-white rounded-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">

            <div class="flex justify-between items-center p-4 border-b">
                <h2 class="text-lg font-bold text-gray-800">Edit Account</h2>
                <button type="button" onclick="closeEditAccountModal()" class="text-2xl text-gray-400">&times;</button>
            </div>

            <form id="editAccountForm" action="" method="POST" class="p-4 flex flex-col gap-3">
                @csrf
                @method('PUT')

                <label class="text-sm font-semibold text-gray-700">Name
                    <input type="text" name="name" required class="mt-1 w-full p-2 border border-gray-200 rounded-lg font-normal">
                </label>

                <label class="text-sm font-semibold text-gray-700">Email
                    <input type="email" name="email" required class="mt-1 w-full p-2 border border-gray-200 rounded-lg font-normal">
                </label>

                <label class="text-sm font-semibold text-gray-700">New password
                    <input type="password" name="password" minlength="8" autocomplete="new-password" class="mt-1 w-full p-2 border border-gray-200 rounded-lg font-normal">
                    <span class="block text-xs text-gray-400 font-normal mt-1">Leave empty to keep the current password.</span>
                </label>

                @if (auth()->user()->role === 'owner')
                    <label class="text-sm font-semibold text-gray-700">Role
                        <select name="role" class="mt-1 w-full p-2 border border-gray-200 rounded-lg bg-white font-normal">
                            <option value="staff">Staff</option>
                            <option value="manager">Manager</option>
                        </select>
                    </label>
                @endif

                <div class="flex gap-2 pt-2">
                    <button type="button" onclick="closeEditAccountModal()" class="flex-1 border border-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg text-sm">Cancel</button>
                    <button type="submit" class="flex-1 bg-orange-500 text-white font-semibold px-4 py-2 rounded-lg text-sm">Save Changes</button>
                </div>
            </form>

        </div>
    </div>

    {{-- delete account modal --}}
    <div id="deleteAccountModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4" onclick="if (event.target === this) closeDeleteAccountModal()">
        <div class="bg-white rounded-2xl w-full max-w-sm overflow-hidden">

            <div class="p-5 text-center">
                <div class="mx-auto mb-3 w-12 h-12 rounded-full bg-red-100 flex items-center justify-center text-2xl">🗑️</div>
                <h2 class="text-lg font-bold text-gray-800">Delete account?</h2>
                <p class="text-sm text-gray-500 mt-2">
                    Are you sure you want to delete
                    <span id="deleteAccountName" class="font-semibold text-gray-800"></span>
                    (<span id="deleteAccountRole"></span>)?
                </p>
                <p class="text-sm text-red-600 mt-2">They will no longer be able to log in. This cannot be undone.</p>
            </div>

            <form id="deleteAccountForm" action="" method="POST" class="flex gap-2 p-4 pt-0">
                @csrf
                @method('DELETE')
                <button type="button" onclick="closeDeleteAccountModal()" class="flex-1 border border-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg text-sm">Cancel</button>
                <button type="submit" class="flex-1 bg-red-600 text-white font-semibold px-4 py-2 rounded-lg text-sm">Yes, delete</button>
            </form>

        </div>
    </div>

    <script>
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
</body>
</html>
