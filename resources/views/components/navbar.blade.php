{{-- ═══ Navbar ═══ --}}
<header class="sticky top-0 z-50">
    <nav class="border-b border-gray-200/80 bg-white/80 backdrop-blur-xl dark:border-gray-800/80 dark:bg-gray-950/80">
        <div class="mx-auto flex h-16 max-w-screen-2xl items-center gap-4 px-4 sm:px-6 lg:px-8">
            {{-- Logo --}}
            <a href="{{ url('/') }}" class="flex shrink-0 items-center gap-2 text-xl font-extrabold tracking-tight text-gray-900 dark:text-white">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-brand-500 to-brand-600 shadow-md shadow-brand-500/20">
                    <svg class="h-4.5 w-4.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72"/></svg>
                </div>
                <span class="hidden sm:inline">Syria<span class="text-brand-500">Zone</span></span>
            </a>

            {{-- Desktop Category Button --}}
            <div class="relative hidden lg:block" id="mega-wrap">
                <button id="mega-btn" class="flex items-center gap-2 rounded-xl border border-gray-200 bg-gray-50 px-4 py-2 text-sm font-semibold text-gray-700 transition-all hover:border-brand-300 hover:bg-brand-50 hover:text-brand-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-brand-500 dark:hover:bg-brand-500/10 dark:hover:text-brand-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                    Categories
                    <svg class="h-3.5 w-3.5 text-gray-400 transition-transform duration-200" id="mega-chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                </button>
                <div id="mega-panel" class="absolute left-0 top-full z-50 mt-3 hidden w-[780px] overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-2xl shadow-gray-900/10 dark:border-gray-700 dark:bg-gray-900 dark:shadow-black/30" style="animation:fadeIn .15s ease-out;">
                    <div class="flex" style="min-height:340px;">
                        <div id="mega-cats" class="w-64 shrink-0 overflow-y-auto border-r border-gray-100 bg-gray-50/80 py-2 dark:border-gray-800 dark:bg-gray-900/50">
                            <div class="px-5 py-8 text-center text-xs text-gray-400">Loading categories...</div>
                        </div>
                        <div id="mega-subs" class="flex-1 p-5 overflow-y-auto">
                            <p class="py-8 text-center text-sm text-gray-400 dark:text-gray-500">Hover a category to browse subcategories</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Desktop Links --}}
            <div class="hidden items-center gap-1 md:flex">
                <a href="{{ route('products.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-gray-600 transition-colors hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white">Products</a>
                <a href="{{ route('categories.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-gray-600 transition-colors hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white">Categories</a>
                <a href="{{ route('vendors.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-gray-600 transition-colors hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white">Stores</a>
            </div>

            <div class="flex-1"></div>

            {{-- Right Actions --}}
            <div class="flex items-center gap-1.5">
                {{-- Dark Mode Toggle --}}
                <button id="theme-toggle" class="relative flex h-9 w-9 items-center justify-center rounded-xl text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200" title="Toggle theme">
                    <svg id="icon-sun" class="h-5 w-5 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/></svg>
                    <svg id="icon-moon" class="h-5 w-5 block dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/></svg>
                </button>

                {{-- Cart --}}
                <button id="nav-cart" class="relative flex h-9 w-9 items-center justify-center rounded-xl text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200" onclick="window.showCart && window.showCart()" title="Cart">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                    <span id="cart-badge" class="absolute -right-0.5 -top-0.5 flex h-4.5 min-w-[18px] items-center justify-center rounded-full bg-brand-500 px-1 text-[10px] font-bold leading-none text-white shadow hidden"></span>
                </button>

                {{-- Guest Buttons (hidden when authenticated) --}}
                <a href="{{ route('login') }}" id="nav-login" class="hidden rounded-xl px-3.5 py-2 text-sm font-semibold text-gray-600 transition-colors hover:bg-gray-100 hover:text-gray-900 sm:inline-flex dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white">Sign In</a>
                <a href="{{ route('register') }}" id="nav-register" class="hidden rounded-xl bg-brand-500 px-4 py-2 text-sm font-bold text-white shadow-sm shadow-brand-500/20 transition-all hover:bg-brand-600 hover:shadow-md hover:shadow-brand-500/30 sm:inline-flex">Register</a>

                {{-- Profile Dropdown (shown when authenticated) --}}
                <div id="profile-wrap" class="relative hidden">
                    <button id="profile-btn" class="flex items-center gap-2.5 rounded-xl px-2 py-1.5 transition-all hover:bg-gray-100 dark:hover:bg-gray-800">
                        <div id="profile-avatar" class="flex h-8 w-8 items-center justify-center overflow-hidden rounded-full bg-gradient-to-br from-brand-400 to-brand-600 ring-2 ring-white dark:ring-gray-800">
                            <span id="profile-initial" class="text-sm font-bold text-white">?</span>
                        </div>
                        <div class="hidden sm:block text-left">
                            <p id="profile-name" class="text-sm font-bold leading-tight text-gray-900 dark:text-white"></p>
                            <p id="profile-role" class="text-[10px] font-medium text-gray-400 dark:text-gray-500">Customer</p>
                        </div>
                        <svg class="h-3.5 w-3.5 text-gray-400 transition-transform duration-200" id="profile-chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                    </button>

                    {{-- Dropdown --}}
                    <div id="profile-dropdown" class="absolute right-0 top-full z-50 mt-2 hidden w-72 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-2xl shadow-gray-900/10 dark:border-gray-700 dark:bg-gray-900 dark:shadow-black/30" style="animation:fadeIn .12s ease-out;">
                        {{-- User Info Header --}}
                        <div class="border-b border-gray-100 bg-gray-50/80 px-5 py-4 dark:border-gray-800 dark:bg-gray-800/50">
                            <div class="flex items-center gap-3">
                                <div id="dd-avatar" class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-full bg-gradient-to-br from-brand-400 to-brand-600 ring-2 ring-white dark:ring-gray-700">
                                    <span id="dd-initial" class="text-base font-bold text-white">?</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p id="dd-name" class="truncate text-sm font-bold text-gray-900 dark:text-white"></p>
                                    <p id="dd-email" class="truncate text-xs text-gray-500 dark:text-gray-400"></p>
                                </div>
                            </div>
                        </div>
                        {{-- Menu Links --}}
                        <div class="py-2">
                            <a href="{{ route('profile') }}" class="flex items-center gap-3 px-5 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800">
                                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                My Profile
                            </a>
                            <a id="dd-dashboard-link" href="#" class="hidden items-center gap-3 px-5 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800">
                                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6z"/></svg>
                                Dashboard
                            </a>
                        </div>
                        <div class="border-t border-gray-100 py-2 dark:border-gray-800">
                            <button onclick="handleLogout()" class="flex w-full items-center gap-3 px-5 py-2.5 text-sm font-medium text-red-600 transition-colors hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                                Sign Out
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Mobile Hamburger --}}
                <button id="mobile-btn" class="flex h-9 w-9 items-center justify-center rounded-xl text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 lg:hidden">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                </button>
            </div>
        </div>
    </nav>

    {{-- Mobile Drawer --}}
    <div id="mobile-drawer" class="fixed inset-0 z-[60] hidden">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeMobileMenu()"></div>
        <div class="absolute right-0 top-0 flex h-full w-80 max-w-[85vw] flex-col bg-white shadow-2xl dark:bg-gray-900" style="animation:slideInRight .25s cubic-bezier(.22,1,.36,1);">
            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-gray-800">
                <span class="text-lg font-extrabold text-gray-900 dark:text-white">Menu</span>
                <button onclick="closeMobileMenu()" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>

            {{-- Mobile Profile Section --}}
            <div id="mobile-profile" class="hidden border-b border-gray-200 px-5 py-4 dark:border-gray-800">
                <div class="flex items-center gap-3">
                    <div id="mob-avatar" class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-gradient-to-br from-brand-400 to-brand-600">
                        <span id="mob-initial" class="text-sm font-bold text-white">?</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p id="mob-name" class="truncate text-sm font-bold text-gray-900 dark:text-white"></p>
                        <p id="mob-email" class="truncate text-xs text-gray-400 dark:text-gray-500"></p>
                    </div>
                </div>
                <div class="mt-3 flex gap-2">
                    <a href="{{ route('profile') }}" onclick="closeMobileMenu()" class="flex-1 rounded-xl border border-gray-200 py-2 text-center text-xs font-bold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Profile</a>
                    <a id="mob-dashboard-link" href="#" onclick="closeMobileMenu()" class="hidden flex-1 rounded-xl border border-gray-200 py-2 text-center text-xs font-bold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Dashboard</a>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto px-5 py-4">
                <div class="space-y-1">
                    <a href="{{ route('products.index') }}" onclick="closeMobileMenu()" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                        Products
                    </a>
                    <a href="{{ route('categories.index') }}" onclick="closeMobileMenu()" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6z"/></svg>
                        Categories
                    </a>
                    <a href="{{ route('vendors.index') }}" onclick="closeMobileMenu()" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35"/></svg>
                        Stores
                    </a>
                </div>
                <div class="mt-4 border-t border-gray-200 pt-4 dark:border-gray-800">
                    <p class="mb-2 text-[11px] font-bold uppercase tracking-widest text-gray-400">Categories</p>
                    <div id="mobile-cats" class="space-y-0.5"></div>
                </div>
            </div>

            {{-- Mobile Footer --}}
            <div id="mobile-guest-footer" class="border-t border-gray-200 px-5 py-4 dark:border-gray-800">
                <a href="{{ route('login') }}" class="mb-2 block w-full rounded-xl border border-gray-200 py-2.5 text-center text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Sign In</a>
                <a href="{{ route('register') }}" class="block w-full rounded-xl bg-brand-500 py-2.5 text-center text-sm font-bold text-white transition-colors hover:bg-brand-600">Create Account</a>
            </div>
            <div id="mobile-auth-footer" class="hidden border-t border-gray-200 px-5 py-4 dark:border-gray-800">
                <button onclick="handleLogout();closeMobileMenu();" class="flex w-full items-center justify-center gap-2 rounded-xl border border-red-200 py-2.5 text-sm font-bold text-red-600 transition-colors hover:bg-red-50 dark:border-red-500/20 dark:text-red-400 dark:hover:bg-red-500/10">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                    Sign Out
                </button>
            </div>
        </div>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function () {
    updateNavbar();
    loadNavCategories();
    initMegaMenu();
    initThemeToggle();
    initProfileDropdown();

    document.getElementById('mobile-btn')?.addEventListener('click', () => {
        document.getElementById('mobile-drawer').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    });
});

function closeMobileMenu() {
    document.getElementById('mobile-drawer').classList.add('hidden');
    document.body.style.overflow = '';
}

function initMegaMenu() {
    const btn = document.getElementById('mega-btn'), panel = document.getElementById('mega-panel'), chevron = document.getElementById('mega-chevron');
    if (!btn) return;
    let open = false;
    btn.addEventListener('click', () => { open = !open; panel.classList.toggle('hidden', !open); chevron.style.transform = open ? 'rotate(180deg)' : ''; });
    document.addEventListener('click', (e) => { if (open && !document.getElementById('mega-wrap').contains(e.target)) { open = false; panel.classList.add('hidden'); chevron.style.transform = ''; } });
}

function initThemeToggle() {
    const btn = document.getElementById('theme-toggle');
    if (!btn) return;
    btn.addEventListener('click', () => {
        const isDark = document.documentElement.classList.toggle('dark');
        localStorage.setItem('sz_theme', isDark ? 'dark' : 'light');
    });
}

function initProfileDropdown() {
    const btn = document.getElementById('profile-btn'), dd = document.getElementById('profile-dropdown'), chev = document.getElementById('profile-chevron');
    if (!btn || !dd) return;
    let open = false;
    btn.addEventListener('click', () => { open = !open; dd.classList.toggle('hidden', !open); chev.style.transform = open ? 'rotate(180deg)' : ''; });
    document.addEventListener('click', (e) => { if (open && !document.getElementById('profile-wrap').contains(e.target)) { open = false; dd.classList.add('hidden'); chev.style.transform = ''; } });
}

function updateNavbar() {
    const isAuth = window.Auth && window.Auth.isAuthenticated();
    const el = id => document.getElementById(id);
    const user = isAuth ? window.Auth.getUser() : null;

    if (isAuth && user) {
        el('nav-login')?.classList.add('hidden');
        el('nav-register')?.classList.add('hidden');
        el('profile-wrap')?.classList.remove('hidden');
        el('mobile-guest-footer')?.classList.add('hidden');
        el('mobile-auth-footer')?.classList.remove('hidden');
        el('mobile-profile')?.classList.remove('hidden');

        const name = user.name || 'User';
        const email = user.email || '';
        const initial = name.charAt(0).toUpperCase();
        const avatarUrl = user.avatar_url || null;
        const roleMap = { 0: 'Customer', 1: 'Admin', 2: 'Vendor' };
        const role = roleMap[user.type] || 'Customer';

        const setAvatar = (containerId, initialId) => {
            const c = el(containerId), i = el(initialId);
            if (!c) return;
            if (avatarUrl) {
                c.innerHTML = `<img src="${_esc(avatarUrl)}" class="h-full w-full object-cover" alt="">`;
            } else if (i) {
                i.textContent = initial;
            }
        };

        setAvatar('profile-avatar', 'profile-initial');
        setAvatar('dd-avatar', 'dd-initial');
        setAvatar('mob-avatar', 'mob-initial');

        if (el('profile-name')) el('profile-name').textContent = name;
        if (el('profile-role')) el('profile-role').textContent = role;
        if (el('dd-name')) el('dd-name').textContent = name;
        if (el('dd-email')) el('dd-email').textContent = email;
        if (el('mob-name')) el('mob-name').textContent = name;
        if (el('mob-email')) el('mob-email').textContent = email;

        const dashLink = el('dd-dashboard-link'), mobDash = el('mob-dashboard-link');
        if (user.type === 1) {
            if (dashLink) { dashLink.href = '{{ url("/admin/dashboard") }}'; dashLink.classList.remove('hidden'); dashLink.classList.add('flex'); }
            if (mobDash) { mobDash.href = '{{ url("/admin/dashboard") }}'; mobDash.classList.remove('hidden'); }
        } else if (user.type === 2) {
            if (dashLink) { dashLink.href = '{{ url("/vendor/dashboard") }}'; dashLink.classList.remove('hidden'); dashLink.classList.add('flex'); }
            if (mobDash) { mobDash.href = '{{ url("/vendor/dashboard") }}'; mobDash.classList.remove('hidden'); }
        }
    } else if (isAuth) {
        el('nav-login')?.classList.add('hidden');
        el('nav-register')?.classList.add('hidden');
        el('profile-wrap')?.classList.remove('hidden');
        el('mobile-guest-footer')?.classList.add('hidden');
        el('mobile-auth-footer')?.classList.remove('hidden');

        fetchAndSetUser();
    } else {
        el('nav-login')?.classList.remove('hidden');
        el('nav-register')?.classList.remove('hidden');
        el('profile-wrap')?.classList.add('hidden');
        el('mobile-guest-footer')?.classList.remove('hidden');
        el('mobile-auth-footer')?.classList.add('hidden');
        el('mobile-profile')?.classList.add('hidden');
    }
    updateCartBadge();
}

async function fetchAndSetUser() {
    try {
        const res = await window.axios.get('/api/user');
        const user = res.data.data || res.data;
        window.Auth.setUser(user);
        updateNavbar();
    } catch (e) {
        window.Auth.removeToken();
        updateNavbar();
    }
}

async function loadNavCategories() {
    try {
        const res = await window.axios.get('/api/categories');
        const cats = res.data.data || [];
        window._navCats = cats;

        const list = document.getElementById('mega-cats');
        const mobileCats = document.getElementById('mobile-cats');

        if (cats.length === 0) { list.innerHTML = '<p class="px-5 py-8 text-center text-xs text-gray-400">No categories</p>'; return; }

        list.innerHTML = cats.map(c => `
            <a href="/categories/${c.id}" data-cat-id="${c.id}" class="mega-cat-btn group flex w-full items-center gap-3 px-4 py-2.5 text-left text-sm transition-all hover:bg-white dark:hover:bg-gray-800"
                    onmouseenter="showNavSubs(${c.id}, this)">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-gray-100 ring-1 ring-gray-200/50 dark:bg-gray-800 dark:ring-gray-700">
                    ${c.logo ? `<img src="/storage/${_esc(c.logo)}" class="h-full w-full object-cover" alt="">` : `<svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/></svg>`}
                </div>
                <span class="flex-1 truncate font-medium text-gray-700 group-hover:text-brand-600 dark:text-gray-300 dark:group-hover:text-brand-400">${_esc(c.name)}</span>
                <span class="text-[10px] font-medium text-gray-400">${c.subcategories ? c.subcategories.length : 0}</span>
                <svg class="h-3.5 w-3.5 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            </a>
        `).join('');

        mobileCats.innerHTML = cats.map(c => {
            const subs = c.subcategories || [];
            return `
            <div>
                <button onclick="this.nextElementSibling.classList.toggle('hidden');this.querySelector('.mob-chev').classList.toggle('rotate-90')" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-sm font-medium text-gray-700 transition-colors hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                        ${c.logo ? `<img src="/storage/${_esc(c.logo)}" class="h-full w-full rounded-lg object-cover" alt="">` : `<svg class="h-3.5 w-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581"/></svg>`}
                    </div>
                    <span class="flex-1">${_esc(c.name)}</span>
                    <svg class="mob-chev h-3.5 w-3.5 text-gray-400 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                </button>
                <div class="hidden ml-12 space-y-0.5 pb-2 pt-1">
                    <a href="/categories/${c.id}" onclick="closeMobileMenu()" class="block rounded-lg px-3 py-2 text-xs font-semibold text-brand-600 hover:bg-gray-100 dark:text-brand-400">View All</a>
                    ${subs.map(s => `<a href="/subcategories/${s.id}" onclick="closeMobileMenu()" class="block rounded-lg px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200">${_esc(s.name)}</a>`).join('')}
                </div>
            </div>`;
        }).join('');
    } catch(e) {}
}

window.showNavSubs = function(catId, btn) {
    document.querySelectorAll('.mega-cat-btn').forEach(el => { el.classList.remove('bg-white', 'dark:bg-gray-800'); el.style.boxShadow = ''; });
    btn.classList.add('bg-white', 'dark:bg-gray-800');
    btn.style.boxShadow = 'inset 3px 0 0 #f97316';
    const cat = (window._navCats || []).find(c => c.id === catId);
    const panel = document.getElementById('mega-subs');
    if (!cat || !cat.subcategories || cat.subcategories.length === 0) {
        panel.innerHTML = '<div class="flex h-full items-center justify-center"><p class="text-sm text-gray-400">No subcategories</p></div>';
        return;
    }
    panel.innerHTML = `
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-base font-bold text-gray-900 dark:text-white">${_esc(cat.name)}</h3>
            <a href="/categories/${cat.id}" class="text-xs font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-400">View All &rarr;</a>
        </div>
        <div class="grid grid-cols-2 gap-2">
            ${cat.subcategories.map(s => `
                <a href="/subcategories/${s.id}" class="group flex items-center gap-3 rounded-xl border border-transparent p-3 transition-all hover:border-gray-200 hover:bg-gray-50 dark:hover:border-gray-700 dark:hover:bg-gray-800">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-gray-100 ring-1 ring-gray-200/50 dark:bg-gray-800 dark:ring-gray-700">
                        ${s.image ? `<img src="/storage/${_esc(s.image)}" class="h-full w-full object-cover" alt="">` : `<svg class="h-5 w-5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5a2.25 2.25 0 002.25-2.25V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/></svg>`}
                    </div>
                    <span class="text-sm font-medium text-gray-600 group-hover:text-brand-600 dark:text-gray-400 dark:group-hover:text-brand-400">${_esc(s.name)}</span>
                </a>
            `).join('')}
        </div>`;
};

function _esc(t) { if (!t) return ''; const d = document.createElement('div'); d.textContent = t; return d.innerHTML; }

function updateCartBadge(animate) {
    try {
        const cart = JSON.parse(localStorage.getItem('cart') || '[]');
        const badge = document.getElementById('cart-badge');
        if (!badge) return;
        const total = cart.reduce((s, i) => s + (i.quantity || 1), 0);
        if (total > 0) { badge.textContent = total > 99 ? '99+' : total; badge.classList.remove('hidden'); if (animate) { badge.classList.add('animate-bounce'); setTimeout(() => badge.classList.remove('animate-bounce'), 600); } }
        else { badge.classList.add('hidden'); }
    } catch (e) {}
}

window.addEventListener('cartUpdated', () => updateCartBadge(true));
window.addEventListener('storage', (e) => { if (e.key === 'cart') updateCartBadge(); });
updateCartBadge();
window.updateCartBadge = updateCartBadge;

async function handleLogout() {
    try { await window.axios.post('/api/auth/logout'); } catch (e) {}
    window.Auth.clearAll();
    window.location.href = '{{ route("login") }}';
}
</script>
