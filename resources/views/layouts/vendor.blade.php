<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Vendor — SyriaZone')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-screen bg-gray-50 font-sans text-gray-900 antialiased">

    {{-- Authenticated wrapper (hidden until verified) --}}
    <div id="vendor-app" class="hidden">
        {{-- Mobile sidebar backdrop --}}
        <div id="sidebar-backdrop" class="fixed inset-0 z-40 hidden bg-gray-900/60 backdrop-blur-sm transition-opacity lg:hidden" onclick="closeSidebar()"></div>

        {{-- Sidebar --}}
        <x-vendor.sidebar />

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
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-700" id="vendor-avatar">V</div>
                            <span id="vendor-name" class="text-sm font-medium text-gray-700"></span>
                        </div>
                        <button onclick="vendorLogout()" class="btn-ghost btn-sm text-gray-500 hover:text-red-600" title="Sign Out">
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
    <div id="vendor-loading" class="flex min-h-screen items-center justify-center bg-gray-50">
        <div class="text-center">
            <div class="mx-auto h-10 w-10 animate-spin rounded-full border-4 border-gray-200 border-t-emerald-500"></div>
            <p class="mt-4 text-sm font-medium text-gray-500">Loading your store...</p>
        </div>
    </div>

    <script>
        // Helper function to delete a cookie
        function deleteCookie(name, path = '/', domain = '') {
            let cookieString = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=' + path;
            if (domain) {
                cookieString += '; domain=' + domain;
            }
            document.cookie = cookieString;
            // Also try without domain
            document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=' + path;
        }

        // Define logout function globally before DOM loads
        window.vendorLogout = async function() {
            try {
                // Get token before clearing (for API call)
                const token = window.Auth?.getToken() || localStorage.getItem('auth_token');

                // Call logout API FIRST to invalidate server-side session
                if (token && window.axios) {
                    try {
                        await window.axios.post('/api/auth/logout', {}, {
                            headers: {
                                'Authorization': 'Bearer ' + token
                            }
                        });
                    } catch (e) {
                        // Continue even if API call fails
                        console.log('Logout API call failed (continuing):', e);
                    }
                }

                // Clear all auth data from client
                if (window.Auth && window.Auth.clearAll) {
                    window.Auth.clearAll();
                } else {
                    localStorage.removeItem('auth_token');
                    localStorage.removeItem('auth_user');
                    sessionStorage.clear();
                    delete window.axios.defaults.headers.common['Authorization'];
                }

                // Delete cookies
                deleteCookie('XSRF-TOKEN');
                deleteCookie('laravel_session');

                // Force immediate redirect with logout parameter to prevent redirect loop
                window.location.replace('{{ route("login") }}?logout=1');
            } catch (e) {
                console.error('Error during logout:', e);
                // Even on error, redirect to login
                window.location.replace('{{ route("login") }}?logout=1');
            }
        };

        // Vendor Auth Guard
        document.addEventListener('DOMContentLoaded', async function () {
            // Check if this is a logout redirect
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('logout') === '1') {
                // Don't check auth, just show login page
                return;
            }

            if (!window.Auth || !window.Auth.isAuthenticated()) {
                window.location.href = '{{ route("login") }}';
                return;
            }

            try {
                const response = await window.axios.get('/api/user');
                const user = response.data;

                if (user.type !== 2) {
                    window.Auth.removeToken();
                    window.location.href = '{{ route("login") }}';
                    return;
                }

                document.getElementById('vendor-name').textContent = user.name;
                document.getElementById('vendor-avatar').textContent = (user.name || 'V').charAt(0).toUpperCase();
                document.getElementById('vendor-loading').classList.add('hidden');
                document.getElementById('vendor-app').classList.remove('hidden');
            } catch (e) {
                window.Auth.removeToken();
                window.location.href = '{{ route("login") }}';
            }
        });

        // Sidebar toggle (mobile)
        document.addEventListener('DOMContentLoaded', function () {
            const toggle = document.getElementById('sidebar-toggle');
            if (toggle) {
                toggle.addEventListener('click', function () {
                    document.getElementById('vendor-sidebar').classList.remove('-translate-x-full');
                    document.getElementById('sidebar-backdrop').classList.remove('hidden');
                    document.body.classList.add('overflow-hidden', 'lg:overflow-auto');
                });
            }
        });

        function closeSidebar() {
            document.getElementById('vendor-sidebar').classList.add('-translate-x-full');
            document.getElementById('sidebar-backdrop').classList.add('hidden');
            document.body.classList.remove('overflow-hidden', 'lg:overflow-auto');
        }
    </script>

    @stack('scripts')
</body>
</html>

