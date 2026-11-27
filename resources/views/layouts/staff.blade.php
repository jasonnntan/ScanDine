<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Staff') - DineFlow</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media (min-width: 768px) {
            body.sidebar-collapsed #staffMenu {
                width: 5rem;
            }
            body.sidebar-collapsed #staffMenu .sidebar-label {
                display: none;
            }
            body.sidebar-collapsed #staffMenu .sidebar-header {
                justify-content: center;
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }
            body.sidebar-collapsed #staffMenu .sidebar-nav-link {
                justify-content: center;
                padding-left: 0;
                padding-right: 0;
            }
            body.sidebar-collapsed #staffMenu .sidebar-user-row {
                justify-content: center;
                padding-left: 0;
                padding-right: 0;
            }
            body.sidebar-collapsed #staffMenu .sidebar-footer-actions {
                grid-template-columns: repeat(1, minmax(0, 1fr));
            }
            body.sidebar-collapsed #staffMenu .sidebar-footer-btn {
                padding-left: 0;
                padding-right: 0;
            }
            body.sidebar-collapsed #sidebarCollapseIcon {
                transform: rotate(180deg);
            }
        }
    </style>
</head>
<body class="bg-stone-50 text-stone-800 antialiased selection:bg-orange-100 selection:text-orange-900">
    <script>
        if (localStorage.getItem('staffSidebarCollapsed') === '1') {
            document.body.classList.add('sidebar-collapsed');
        }
    </script>

    @php
        $user = auth()->user();
        $role = $user->role;
        $initials = collect(explode(' ', trim($user->name)))
            ->filter()
            ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->take(2)
            ->implode('');

        $links = [
            ['label' => 'Sales Dashboard', 'icon' => 'fa-solid fa-chart-line', 'route' => 'staff.dashboard',       'match' => 'staff.dashboard',   'roles' => ['manager', 'owner']],
            ['label' => 'Orders',          'icon' => 'fa-solid fa-receipt',    'route' => 'staff.orders.index',    'match' => 'staff.orders.*',    'roles' => ['staff', 'manager', 'owner']],
            ['label' => 'Tables',          'icon' => 'fa-solid fa-chair',      'route' => 'staff.tables.index',    'match' => 'staff.tables.*',    'roles' => ['staff', 'manager', 'owner']],
            ['label' => 'Food Items',      'icon' => 'fa-solid fa-utensils',   'route' => 'staff.foodItems.index', 'match' => 'staff.foodItems.*', 'roles' => ['staff', 'manager', 'owner']],
            ['label' => 'Accounts',        'icon' => 'fa-solid fa-users',      'route' => 'staff.accounts.index',  'match' => 'staff.accounts.*',  'roles' => ['manager', 'owner']],
            ['label' => 'Settings',        'icon' => 'fa-solid fa-gear',       'route' => 'staff.settings.edit',   'match' => 'staff.settings.*',  'roles' => ['owner']],
        ];
    @endphp

    {{-- Floating Toast Notification --}}
    <div id="staffToast" class="fixed bottom-5 right-5 z-[70] pointer-events-none opacity-0 translate-y-3 transition-all duration-200">
        <div id="staffToastBox" class="bg-stone-900 text-white text-xs sm:text-sm font-bold px-4 py-3 rounded-2xl shadow-xl flex items-center gap-2.5">
            <i id="staffToastIcon" class="fa-solid fa-circle-check text-emerald-400" aria-hidden="true"></i>
            <span id="staffToastText">Updated</span>
        </div>
    </div>

    <div class="min-h-screen md:flex">

        {{-- Mobile Top Bar --}}
        <header class="md:hidden sticky top-0 z-30 flex items-center justify-between bg-white/95 backdrop-blur-md border-b border-stone-200/80 px-4 py-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-orange-600 text-white flex items-center justify-center text-sm font-extrabold shadow-sm">
                    <i class="fa-solid fa-utensils" aria-hidden="true"></i>
                </div>
                <div>
                    <span class="block text-sm font-extrabold text-stone-900 leading-none">DineFlow</span>
                    <span id="mobileHeaderTitle" class="block text-[11px] font-medium text-stone-400 mt-0.5">@yield('title', 'Staff Portal')</span>
                </div>
            </div>

            <button type="button" onclick="toggleStaffMenu(true)" class="w-9 h-9 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-700 flex items-center justify-center transition" aria-label="Open menu">
                <i class="fa-solid fa-bars" aria-hidden="true"></i>
            </button>
        </header>

        {{-- Mobile Drawer Backdrop --}}
        <div id="staffBackdrop" onclick="toggleStaffMenu(false)" class="fixed inset-0 bg-stone-950/50 backdrop-blur-[1px] z-40 hidden md:hidden transition-opacity"></div>

        {{-- Sidebar --}}
        <aside id="staffMenu" class="fixed inset-y-0 left-0 z-50 w-64 -translate-x-full md:translate-x-0 md:static md:flex md:flex-col flex-shrink-0 bg-white border-r border-stone-200/80 md:sticky md:top-0 md:h-screen transition-all duration-200 ease-out flex flex-col">

            {{-- Brand Header --}}
            <div class="sidebar-header flex items-center justify-between px-5 py-5 border-b border-stone-100">
                <a href="{{ route('staff.orders.index') }}" class="sidebar-label flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-orange-500 to-red-700 text-white flex items-center justify-center shadow-sm flex-shrink-0">
                        <i class="fa-solid fa-utensils text-sm" aria-hidden="true"></i>
                    </div>
                    <div class="truncate">
                        <p class="text-base font-extrabold text-stone-900 tracking-tight leading-none">DineFlow</p>
                        <p class="text-[11px] font-medium text-stone-400 mt-1">Staff & Admin</p>
                    </div>
                </a>

                {{-- Desktop Collapse / Expand Toggle --}}
                <button type="button"
                        id="sidebarCollapseBtn"
                        onclick="toggleSidebarCollapse()"
                        title="Collapse sidebar"
                        aria-label="Toggle sidebar width"
                        class="hidden md:flex w-8 h-8 rounded-lg text-stone-400 hover:bg-stone-100 hover:text-stone-700 items-center justify-center transition flex-shrink-0">
                    <i id="sidebarCollapseIcon" class="fa-solid fa-angles-left text-xs transition-transform duration-200" aria-hidden="true"></i>
                </button>

                {{-- Mobile Close Button --}}
                <button type="button" onclick="toggleStaffMenu(false)" class="md:hidden w-8 h-8 rounded-lg text-stone-400 hover:bg-stone-100 hover:text-stone-600 flex items-center justify-center" aria-label="Close menu">
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                </button>
            </div>

            {{-- Navigation Links --}}
            <div class="flex-1 overflow-y-auto px-3 py-4">
                <p class="sidebar-label px-3 mb-2 text-[10px] font-bold uppercase tracking-wider text-stone-400">Management</p>
                <nav id="staffSidebarNav" class="flex flex-col gap-1">
                    @foreach ($links as $link)
                        @if (in_array($role, $link['roles']))
                            @php
                                $isActive = request()->routeIs($link['match']);
                            @endphp
                            <a href="{{ route($link['route']) }}"
                               title="{{ $link['label'] }}"
                               class="sidebar-nav-link flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ $isActive ? 'bg-orange-600 text-white shadow-sm shadow-orange-600/25' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900' }}">
                                <i class="{{ $link['icon'] }} w-5 text-center flex-shrink-0 {{ $isActive ? 'text-white' : 'text-stone-400' }}" aria-hidden="true"></i>
                                <span class="sidebar-label truncate">{{ $link['label'] }}</span>
                            </a>
                        @endif
                    @endforeach
                </nav>
            </div>

            {{-- User Footer Card --}}
            <div class="p-3 border-t border-stone-100 bg-stone-50/70">
                <div class="sidebar-user-row flex items-center gap-3 px-2 py-2 mb-2" title="{{ $user->name }} ({{ ucfirst($role) }})">
                    <div class="w-9 h-9 rounded-full bg-stone-800 text-white text-xs font-extrabold flex items-center justify-center flex-shrink-0">
                        {{ $initials ?: 'ST' }}
                    </div>
                    <div class="sidebar-label min-w-0 flex-1">
                        <p class="text-sm font-bold text-stone-800 truncate">{{ $user->name }}</p>
                        <span class="inline-block text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full mt-0.5 {{ $role === 'owner' ? 'bg-orange-100 text-orange-700' : ($role === 'manager' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800') }}">
                            {{ $role }}
                        </span>
                    </div>
                </div>

                <div class="sidebar-footer-actions grid grid-cols-2 gap-1.5 pt-1">
                    <a href="{{ route('profile.edit') }}" data-no-ajax title="Profile" class="sidebar-footer-btn flex items-center justify-center gap-1.5 px-2.5 py-2 rounded-lg bg-white border border-stone-200 hover:bg-stone-100 text-xs font-semibold text-stone-700 transition">
                        <i class="fa-solid fa-circle-user text-stone-400" aria-hidden="true"></i>
                        <span class="sidebar-label">Profile</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}" data-no-ajax>
                        @csrf
                        <button type="submit" title="Log out" class="sidebar-footer-btn w-full flex items-center justify-center gap-1.5 px-2.5 py-2 rounded-lg bg-white border border-stone-200 hover:bg-red-50 hover:border-red-200 text-xs font-semibold text-red-600 transition">
                            <i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i>
                            <span class="sidebar-label">Log out</span>
                        </button>
                    </form>
                </div>
            </div>

        </aside>

        {{-- Swappable Content Container --}}
        <main id="ajax-container" class="flex-1 min-w-0">
            @if (session('success'))
                <div data-flash-toast="success" data-flash-message="{{ session('success') }}" class="hidden"></div>
            @endif

            @if (session('error'))
                <div data-flash-toast="error" data-flash-message="{{ session('error') }}" class="hidden"></div>
            @endif

            @if ($errors->any())
                <div class="max-w-7xl mx-auto px-4 md:px-6 pt-4">
                    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <p class="flex items-center gap-2">
                                <i class="fa-solid fa-triangle-exclamation text-red-600 text-xs" aria-hidden="true"></i>
                                <span>{{ $error }}</span>
                            </p>
                        @endforeach
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        var staffToastTimer = null;

        function toggleSidebarCollapse() {
            var isCollapsed = document.body.classList.toggle('sidebar-collapsed');
            localStorage.setItem('staffSidebarCollapsed', isCollapsed ? '1' : '0');

            var btn = document.getElementById('sidebarCollapseBtn');
            if (btn) {
                btn.title = isCollapsed ? 'Expand sidebar' : 'Collapse sidebar';
            }

            setTimeout(function () {
                window.dispatchEvent(new Event('resize'));
            }, 220);
        }

        function toggleStaffMenu(forceState) {
            var menu = document.getElementById('staffMenu');
            var backdrop = document.getElementById('staffBackdrop');
            var isOpen = typeof forceState === 'boolean' ? forceState : menu.classList.contains('-translate-x-full');

            if (isOpen) {
                menu.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            } else {
                menu.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }
        }

        function showStaffToast(message, isError) {
            var toast = document.getElementById('staffToast');
            var box = document.getElementById('staffToastBox');
            var icon = document.getElementById('staffToastIcon');
            var text = document.getElementById('staffToastText');

            text.textContent = message;
            if (isError) {
                box.className = 'bg-red-700 text-white text-xs sm:text-sm font-bold px-4 py-3 rounded-2xl shadow-xl flex items-center gap-2.5';
                icon.className = 'fa-solid fa-circle-exclamation text-white';
            } else {
                box.className = 'bg-stone-900 text-white text-xs sm:text-sm font-bold px-4 py-3 rounded-2xl shadow-xl flex items-center gap-2.5';
                icon.className = 'fa-solid fa-circle-check text-emerald-400';
            }

            toast.classList.remove('opacity-0', 'translate-y-3');
            toast.classList.add('opacity-100', 'translate-y-0');

            clearTimeout(staffToastTimer);
            staffToastTimer = setTimeout(function () {
                toast.classList.add('opacity-0', 'translate-y-3');
                toast.classList.remove('opacity-100', 'translate-y-0');
            }, 2400);
        }

        function triggerInitialFlashToasts() {
            var flash = document.querySelector('#ajax-container [data-flash-toast]');
            if (flash) {
                showStaffToast(flash.dataset.flashMessage, flash.dataset.flashToast === 'error');
            }
        }

        // Runs scripts from swapped views and triggers any DOMContentLoaded callbacks immediately
        function executeSwappedScripts(scriptWrapper) {
            var host = document.getElementById('ajax-scripts');
            if (!host || !scriptWrapper) { return; }

            var oldScripts = Array.from(scriptWrapper.querySelectorAll('script'));
            var externalScripts = oldScripts.filter(function (s) { return !!s.src; });
            var inlineScripts = oldScripts.filter(function (s) { return !s.src; });

            var loadExternal = function (s) {
                return new Promise(function (resolve) {
                    if (document.querySelector('script[src="' + s.getAttribute('src') + '"]')) {
                        resolve();
                        return;
                    }
                    var tag = document.createElement('script');
                    tag.src = s.src;
                    tag.onload = resolve;
                    tag.onerror = resolve;
                    document.head.appendChild(tag);
                });
            };

            Promise.all(externalScripts.map(loadExternal)).then(function () {
                var readyCallbacks = [];
                var origAddEventListener = document.addEventListener.bind(document);

                document.addEventListener = function (type, listener, options) {
                    if (type === 'DOMContentLoaded' && typeof listener === 'function') {
                        readyCallbacks.push(listener);
                        return;
                    }
                    if (type === 'keydown') {
                        return;
                    }
                    return origAddEventListener(type, listener, options);
                };

                try {
                    host.innerHTML = '';
                    inlineScripts.forEach(function (oldScript) {
                        var newScript = document.createElement('script');
                        newScript.textContent = oldScript.textContent;
                        host.appendChild(newScript);
                    });
                } finally {
                    document.addEventListener = origAddEventListener;
                }

                readyCallbacks.forEach(function (fn) {
                    try { fn(); } catch (err) { console.error(err); }
                });
            });
        }

        // Seamless SPA-style page & action swap
        function ajaxVisit(url, options, pushHistory) {
            var currentPath = window.location.pathname;
            var isMutation = options && options.method && options.method.toUpperCase() !== 'GET';
            var savedInputs = {};

            document.querySelectorAll('#ajax-container input[type="text"][id]').forEach(function (el) {
                if (!el.closest('[id$="Modal"]')) {
                    savedInputs[el.id] = el.value;
                }
            });

            var fetchOptions = Object.assign({
                credentials: 'same-origin',
                headers: { 'Accept': 'text/html' }
            }, options || {});

            return fetch(url, fetchOptions)
                .then(function (response) {
                    if (pushHistory && response.url) {
                        window.history.pushState({}, '', response.url);
                    }
                    return response.text().then(function (html) {
                        return { html: html, finalUrl: response.url, ok: response.ok };
                    });
                })
                .then(function (res) {
                    var doc = new DOMParser().parseFromString(res.html, 'text/html');
                    var nextContainer = doc.getElementById('ajax-container');

                    if (!nextContainer) {
                        // Never navigate via GET to a POST/PUT/DELETE action URL
                        if (isMutation || !res.ok) {
                            document.open();
                            document.write(res.html);
                            document.close();
                            return;
                        }
                        window.location.href = res.finalUrl || url;
                        return;
                    }

                    if (window.autoRefreshTimer) {
                        clearInterval(window.autoRefreshTimer);
                        window.autoRefreshTimer = null;
                    }

                    if (doc.title) {
                        document.title = doc.title;
                    }
                    var nextMobileTitle = doc.getElementById('mobileHeaderTitle');
                    var currMobileTitle = document.getElementById('mobileHeaderTitle');
                    if (nextMobileTitle && currMobileTitle) {
                        currMobileTitle.textContent = nextMobileTitle.textContent;
                    }

                    var nextNav = doc.getElementById('staffSidebarNav');
                    var currNav = document.getElementById('staffSidebarNav');
                    if (nextNav && currNav) {
                        currNav.innerHTML = nextNav.innerHTML;
                    }

                    toggleStaffMenu(false);

                    document.getElementById('ajax-container').innerHTML = nextContainer.innerHTML;

                    var samePage = new URL(res.finalUrl || url, window.location.origin).pathname === currentPath;
                    if (samePage) {
                        Object.keys(savedInputs).forEach(function (id) {
                            var el = document.getElementById(id);
                            if (el) {
                                el.value = savedInputs[id];
                                el.dispatchEvent(new Event('input'));
                            }
                        });
                    } else if (pushHistory) {
                        window.scrollTo({ top: 0, behavior: 'instant' });
                    }

                    executeSwappedScripts(doc.getElementById('ajax-scripts'));
                    triggerInitialFlashToasts();
                })
                .catch(function () {
                    showStaffToast('Network error. Please try again.', true);
                });
        }

        function submitAjaxForm(form) {
            if (form.dataset.submitting === '1') { return Promise.resolve(); }
            form.dataset.submitting = '1';

            var method = (form.getAttribute('method') || 'GET').toUpperCase();
            var request;

            if (method === 'GET') {
                var params = new URLSearchParams(new FormData(form));
                var url = form.action.split('?')[0] + '?' + params.toString();
                request = ajaxVisit(url, null, true);
            } else {
                request = ajaxVisit(form.action, {
                    method: 'POST',
                    body: new FormData(form)
                }, false);
            }

            return request.finally(function () {
                form.dataset.submitting = '0';
            });
        }

        var nativeFormSubmit = HTMLFormElement.prototype.submit;
        HTMLFormElement.prototype.submit = function () {
            if (this.closest('#ajax-container') && !this.hasAttribute('data-no-ajax')) {
                submitAjaxForm(this);
                return;
            }
            nativeFormSubmit.call(this);
        };

        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (form && form.closest('#ajax-container') && !form.hasAttribute('data-no-ajax') && !form.target) {
                e.preventDefault();
                submitAjaxForm(form);
            }
        });

        document.addEventListener('click', function (e) {
            var link = e.target.closest('a[href]');
            if (!link || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) { return; }
            if (link.hasAttribute('data-no-ajax') || link.hasAttribute('download') || link.target === '_blank') { return; }

            var inStaffZone = link.closest('#staffSidebarNav') || link.closest('#staffMenu') || link.closest('#ajax-container');
            if (!inStaffZone) { return; }

            var rawHref = link.getAttribute('href');
            if (!rawHref || rawHref.startsWith('#') || rawHref.startsWith('javascript:')) { return; }

            var targetUrl = new URL(link.href, window.location.origin);
            if (targetUrl.origin !== window.location.origin) { return; }

            e.preventDefault();
            ajaxVisit(targetUrl.href, null, true);
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('#ajax-container [id$="Modal"]').forEach(function (modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                });
            }
        });

        window.addEventListener('popstate', function () {
            ajaxVisit(window.location.href, null, false);
        });

        document.addEventListener('DOMContentLoaded', function () {
            var btn = document.getElementById('sidebarCollapseBtn');
            if (btn && document.body.classList.contains('sidebar-collapsed')) {
                btn.title = 'Expand sidebar';
            }
            triggerInitialFlashToasts();
        });
    </script>

    <div id="ajax-scripts">
        @stack('scripts')
    </div>
</body>
</html>
