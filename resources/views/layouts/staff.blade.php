<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Staff') - DineFlow</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    @php
        $role = auth()->user()->role;

        $links = [
            ['label' => 'Sales Dashboard', 'icon' => '📊', 'route' => 'staff.dashboard',       'match' => 'staff.dashboard',   'roles' => ['manager', 'owner']],
            ['label' => 'Orders',          'icon' => '🧾', 'route' => 'staff.orders.index',    'match' => 'staff.orders.*',    'roles' => ['staff', 'manager', 'owner']],
            ['label' => 'Tables',          'icon' => '🪑', 'route' => 'staff.tables.index',    'match' => 'staff.tables.*',    'roles' => ['staff', 'manager', 'owner']],
            ['label' => 'Food Items',      'icon' => '🍽️', 'route' => 'staff.foodItems.index', 'match' => 'staff.foodItems.*', 'roles' => ['staff', 'manager', 'owner']],
            ['label' => 'Accounts',        'icon' => '👥', 'route' => 'staff.accounts.index',  'match' => 'staff.accounts.*',  'roles' => ['manager', 'owner']],
            ['label' => 'Settings',        'icon' => '⚙️', 'route' => 'staff.settings.edit',   'match' => 'staff.settings.*',  'roles' => ['owner']],
        ];
    @endphp

    <div class="min-h-screen md:flex">

        {{-- mobile top bar --}}
        <div class="md:hidden flex items-center justify-between bg-white shadow px-4 py-3">
            <span class="font-extrabold text-gray-800">DineFlow</span>
            <button type="button" onclick="toggleStaffMenu()" class="text-2xl text-gray-600">☰</button>
        </div>

        {{-- sidebar --}}
        <aside id="staffMenu" class="hidden md:flex md:flex-col md:w-60 flex-shrink-0 bg-white shadow-md md:sticky md:top-0 md:h-screen md:overflow-y-auto p-4">

            <div class="mb-6 hidden md:block">
                <p class="text-xl font-extrabold text-gray-800">DineFlow</p>
                <p class="text-xs text-gray-400">Restaurant management</p>
            </div>

            <nav class="flex flex-col gap-1">
                @foreach ($links as $link)
                    @if (in_array($role, $link['roles']))
                        <a href="{{ route($link['route']) }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs($link['match']) ? 'bg-orange-500 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                            <span>{{ $link['icon'] }}</span>
                            <span>{{ $link['label'] }}</span>
                        </a>
                    @endif
                @endforeach
            </nav>

            <div class="mt-auto pt-6 border-t border-gray-100">
                <p class="text-sm font-semibold text-gray-800 break-words">{{ auth()->user()->name }}</p>
                <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-full mb-3 {{ $role === 'owner' ? 'bg-orange-100 text-orange-700' : ($role === 'manager' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800') }}">{{ $role }}</span>

                <a href="{{ route('profile.edit') }}" class="block text-sm text-gray-600 mb-2">My profile</a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-red-600 font-semibold">Log out</button>
                </form>
            </div>

        </aside>

        {{-- page content --}}
        <main class="flex-1 min-w-0">
            @yield('content')
        </main>
    </div>

    <script>
    function toggleStaffMenu() {
        document.getElementById('staffMenu').classList.toggle('hidden');
    }
    </script>
    @stack('scripts')
</body>
</html>
