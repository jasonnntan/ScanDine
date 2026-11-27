<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login - DineFlow</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-50 text-stone-800 antialiased selection:bg-orange-100 selection:text-orange-900 flex flex-col justify-center p-4 sm:p-6">

    <div class="w-full max-w-md mx-auto">

        {{-- Brand Header --}}
        <div class="text-center mb-6">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-orange-500 to-red-700 text-white flex items-center justify-center shadow-lg shadow-orange-600/20 mx-auto mb-3.5">
                <i class="fa-solid fa-utensils text-xl" aria-hidden="true"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">DineFlow</h1>
            <p class="text-xs sm:text-sm font-medium text-stone-500 mt-1">Staff & Management Portal</p>
        </div>

        {{-- Login Card --}}
        <div class="bg-white rounded-3xl border border-stone-200/80 shadow-xl shadow-stone-200/50 p-6 sm:p-8">

            <div class="mb-6">
                <h2 class="text-lg font-extrabold text-stone-900">Welcome back</h2>
                <p class="text-xs text-stone-400 mt-0.5">Sign in with your staff, manager, or owner account.</p>
            </div>

            {{-- Session Status (e.g. password reset confirmation) --}}
            @if (session('status'))
                <div class="mb-4 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-600" aria-hidden="true"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            {{-- Authentication Errors --}}
            @if ($errors->any())
                <div class="mb-4 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs font-semibold space-y-1">
                    @foreach ($errors->all() as $error)
                        <p class="flex items-center gap-2">
                            <i class="fa-solid fa-circle-exclamation text-red-600" aria-hidden="true"></i>
                            <span>{{ $error }}</span>
                        </p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                {{-- Email Address --}}
                <div>
                    <label for="email" class="block text-xs font-bold text-stone-600 uppercase tracking-wide mb-1.5">
                        Email Address
                    </label>
                    <div class="relative">
                        <i class="fa-regular fa-envelope text-stone-400 text-sm absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" aria-hidden="true"></i>
                        <input id="email"
                               type="email"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               autofocus
                               autocomplete="username"
                               placeholder="you@restaurant.com"
                               class="w-full pl-10 pr-3.5 py-2.5 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm text-stone-900 placeholder:text-stone-400 focus:outline-none transition">
                    </div>
                </div>

                {{-- Password with Show/Hide Toggle --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-bold text-stone-600 uppercase tracking-wide">
                            Password
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs font-semibold text-orange-600 hover:text-orange-700 transition">
                                Forgot password?
                            </a>
                        @endif
                    </div>
                    <div class="relative">
                        <i class="fa-solid fa-lock text-stone-400 text-xs absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" aria-hidden="true"></i>
                        <input id="password"
                               type="password"
                               name="password"
                               required
                               autocomplete="current-password"
                               placeholder="Enter your password"
                               class="w-full pl-10 pr-10 py-2.5 bg-stone-50 border border-stone-200 focus:bg-white focus:border-orange-500 rounded-xl text-sm text-stone-900 placeholder:text-stone-400 focus:outline-none transition">
                        <button type="button"
                                onclick="toggleLoginPassword()"
                                aria-label="Toggle password visibility"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-600 p-1 transition">
                            <i id="loginPasswordEye" class="fa-regular fa-eye text-sm" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center justify-between pt-0.5">
                    <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer select-none">
                        <input id="remember_me"
                               type="checkbox"
                               name="remember"
                               class="w-4 h-4 rounded border-stone-300 text-orange-600 focus:ring-orange-500">
                        <span class="text-xs font-semibold text-stone-600">Keep me signed in</span>
                    </label>
                </div>

                {{-- Submit Button --}}
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 bg-orange-600 hover:bg-orange-700 active:scale-[0.99] text-white font-extrabold py-3 px-4 rounded-xl text-sm shadow-sm shadow-orange-600/25 transition mt-2">
                    <span>Sign In</span>
                    <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
                </button>
            </form>
        </div>

        {{-- Footer Note --}}
        <p class="text-center text-[11px] font-medium text-stone-400 mt-5">
            Need an account? Contact your restaurant manager or owner.
        </p>

    </div>

    <script>
        function toggleLoginPassword() {
            var input = document.getElementById('password');
            var icon = document.getElementById('loginPasswordEye');
            if (!input || !icon) { return; }

            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fa-regular fa-eye-slash text-sm';
            } else {
                input.type = 'password';
                icon.className = 'fa-regular fa-eye text-sm';
            }
        }
    </script>
</body>
</html>
