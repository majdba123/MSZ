<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'SyriaZone')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700" rel="stylesheet" />

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="min-h-screen bg-gray-50 font-sans text-gray-900 antialiased">

    {{-- Navbar --}}
    <x-navbar />

    {{-- Page Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="mt-auto border-t border-gray-800 bg-navy-900">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <a href="{{ url('/') }}" class="text-xl font-bold tracking-tight text-white">Syria<span class="text-brand-500">Zone</span></a>
                    <p class="mt-3 text-sm leading-relaxed text-gray-400">Your trusted marketplace for quality products from verified Syrian vendors.</p>
                </div>
                <div>
                    <h4 class="text-sm font-semibold uppercase tracking-wider text-gray-300">Quick Links</h4>
                    <ul class="mt-3 space-y-2 text-sm text-gray-400">
                        <li><a href="{{ route('home') }}#products" class="transition-colors hover:text-white">All Products</a></li>
                        <li><a href="{{ route('home') }}#categories" class="transition-colors hover:text-white">Categories</a></li>
                        <li><a href="{{ route('home') }}#vendors" class="transition-colors hover:text-white">Stores</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-semibold uppercase tracking-wider text-gray-300">Account</h4>
                    <ul class="mt-3 space-y-2 text-sm text-gray-400">
                        <li><a href="{{ route('login') }}" class="transition-colors hover:text-white">Sign In</a></li>
                        <li><a href="{{ route('register') }}" class="transition-colors hover:text-white">Create Account</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-semibold uppercase tracking-wider text-gray-300">Contact</h4>
                    <ul class="mt-3 space-y-2 text-sm text-gray-400">
                        <li class="flex items-center gap-2"><svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg> support@syriazone.com</li>
                        <li class="flex items-center gap-2"><svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg> Syria</li>
                    </ul>
                </div>
            </div>
            <div class="mt-10 border-t border-gray-800 pt-6 text-center text-xs text-gray-500">
                <p>&copy; {{ date('Y') }} SyriaZone. All rights reserved.</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>

