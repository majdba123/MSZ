<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin — SyriaZone')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-screen bg-gray-50 font-sans text-gray-900 antialiased">

    {{-- Authenticated wrapper (hidden until verified) --}}
    <div id="admin-app" class="hidden">
        {{-- Mobile sidebar backdrop --}}
        <div id="sidebar-backdrop" class="fixed inset-0 z-40 hidden bg-gray-900/60 backdrop-blur-sm transition-opacity lg:hidden" onclick="closeSidebar()"></div>

        {{-- Sidebar --}}
        <x-admin.sidebar />

        {{-- Main Column --}}
        <div class="lg:pl-72">
            {{-- Top Bar --}}
            <header class="sticky top-0 z-30 border-b border-gray-200 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/80">
                <div class="flex h-14 items-center gap-x-4 px-4 sm:px-6 lg:px-8">
                    {{-- Mobile menu button --}}
                    <button type="button" id="sidebar-toggle" class="-m-2.5 p-2.5 text-gray-500 hover:text-gray-700 lg:hidden" aria-label="Open sidebar">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                    </button>

                    {{-- Separator --}}
                    <div class="h-5 w-px bg-gray-200 lg:hidden" aria-hidden="true"></div>

                    {{-- Page title --}}
                    <h1 class="flex-1 text-base font-semibold text-gray-900 sm:text-lg">@yield('page-title', 'Dashboard')</h1>

                    {{-- Right side --}}
                    <div class="flex items-center gap-x-3">
                        <div class="hidden items-center gap-2 sm:flex">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-100 text-xs font-bold text-brand-700" id="admin-avatar">A</div>
                            <span id="admin-name" class="text-sm font-medium text-gray-700"></span>
                        </div>
                        <button onclick="adminLogout()" class="btn-ghost btn-sm text-gray-500 hover:text-red-600" title="Sign Out">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/></svg>
                            <span class="hidden sm:inline">Sign Out</span>
                        </button>
                    </div>
                </div>
            </header>

            {{-- Page Content --}}
            <main class="px-4 py-6 sm:px-6 lg:px-8">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Loading Screen --}}
    <div id="admin-loading" class="flex min-h-screen items-center justify-center bg-gray-50">
        <div class="text-center">
            <div class="mx-auto h-10 w-10 animate-spin rounded-full border-4 border-gray-200 border-t-brand-500"></div>
            <p class="mt-4 text-sm font-medium text-gray-500">Verifying access...</p>
        </div>
    </div>

    <script>
        // Admin Auth Guard — redirect to main login if not authenticated or not admin
        document.addEventListener('DOMContentLoaded', async function () {
            if (!window.Auth || !window.Auth.isAuthenticated()) {
                window.location.href = '{{ route("login") }}';
                return;
            }

            try {
                const response = await window.axios.get('/api/user');
                const user = response.data;

                if (user.type !== 1) {
                    window.location.href = '{{ route("login") }}';
                    return;
                }

                document.getElementById('admin-name').textContent = user.name;
                document.getElementById('admin-avatar').textContent = (user.name || 'A').charAt(0).toUpperCase();
                document.getElementById('admin-loading').classList.add('hidden');
                document.getElementById('admin-app').classList.remove('hidden');
            } catch (e) {
                window.Auth.removeToken();
                window.location.href = '{{ route("login") }}';
            }
        });

        function adminLogout() {
            // Get token before clearing (for API call)
            const token = localStorage.getItem('auth_token');

            // Clear all auth data immediately
            try {
                localStorage.removeItem('auth_token');
                localStorage.removeItem('auth_user');
                sessionStorage.clear();
                if (window.Auth) {
                    if (window.Auth.clearAll) {
                        window.Auth.clearAll();
                    } else if (window.Auth.removeToken) {
                        window.Auth.removeToken();
                    }
                }
                // Clear axios default headers
                delete window.axios.defaults.headers.common['Authorization'];
            } catch (e) {
                console.error('Error clearing storage:', e);
            }

            // Try to call logout API with token (fire and forget)
            if (token) {
                window.axios.post('/api/auth/logout', {}, {
                    headers: {
                        'Authorization': 'Bearer ' + token
                    }
                }).catch(() => {});
            }

            // Force immediate redirect - don't wait for API
            window.location.href = '{{ route("login") }}';
        }

        // Sidebar toggle (mobile)
        document.addEventListener('DOMContentLoaded', function () {
            const toggle = document.getElementById('sidebar-toggle');
            if (toggle) {
                toggle.addEventListener('click', function () {
                    document.getElementById('admin-sidebar').classList.remove('-translate-x-full');
                    document.getElementById('sidebar-backdrop').classList.remove('hidden');
                    document.body.classList.add('overflow-hidden', 'lg:overflow-auto');
                });
            }
        });

        function closeSidebar() {
            document.getElementById('admin-sidebar').classList.add('-translate-x-full');
            document.getElementById('sidebar-backdrop').classList.add('hidden');
            document.body.classList.remove('overflow-hidden', 'lg:overflow-auto');
        }
    </script>

    @stack('scripts')
</body>
</html>
