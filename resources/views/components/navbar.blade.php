{{-- Navigation Bar --}}
<nav class="sticky top-0 z-50 bg-navy-900 shadow-lg">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
        {{-- Logo --}}
        <a href="{{ url('/') }}" class="flex items-center gap-2">
            <span class="text-xl font-bold tracking-tight text-white">Syria<span class="text-brand-500">Zone</span></span>
        </a>

        {{-- Navigation Links --}}
        <div class="flex items-center gap-3" id="nav-links">
            <a href="{{ route('home') }}#products"
               class="hidden rounded-md px-4 py-2 text-sm font-medium text-gray-200 transition-colors hover:border-brand-500 hover:text-white sm:block">
                Products
            </a>
            <a href="{{ route('home') }}#vendors"
               class="hidden rounded-md px-4 py-2 text-sm font-medium text-gray-200 transition-colors hover:border-brand-500 hover:text-white sm:block">
                Vendors
            </a>
            <button id="nav-cart"
                    class="relative flex items-center gap-2 rounded-lg border border-gray-600 px-3 py-2.5 text-gray-200 transition-all hover:border-brand-500 hover:bg-brand-500/10 hover:text-white"
                    onclick="window.showCart && window.showCart()"
                    title="Shopping Cart">
                <svg class="h-6 w-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                </svg>
                <span id="cart-badge" class="flex h-6 min-w-[24px] items-center justify-center rounded-full bg-gradient-to-br from-red-500 to-red-600 px-2 text-xs font-extrabold leading-none text-white shadow-lg ring-2 ring-white transition-all duration-300 ease-out hidden"></span>
            </button>
            <a href="{{ route('login') }}"
               id="nav-login"
               class="rounded-md border border-gray-600 px-4 py-2 text-sm font-medium text-gray-200 transition-colors hover:border-brand-500 hover:text-white">
                Sign In
            </a>
            <a href="{{ route('register') }}"
               id="nav-register"
               class="btn-primary rounded-md px-4 py-2 text-sm">
                Create Account
            </a>
            <button id="nav-logout"
                    class="hidden rounded-md border border-gray-600 px-4 py-2 text-sm font-medium text-gray-200 transition-colors hover:border-red-500 hover:text-red-400"
                    onclick="handleLogout()">
                Sign Out
            </button>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        updateNavbar();
    });

    function updateNavbar() {
        const isAuth = window.Auth && window.Auth.isAuthenticated();
        const loginLink = document.getElementById('nav-login');
        const registerLink = document.getElementById('nav-register');
        const logoutBtn = document.getElementById('nav-logout');

        if (isAuth) {
            if (loginLink) loginLink.classList.add('hidden');
            if (registerLink) registerLink.classList.add('hidden');
            if (logoutBtn) logoutBtn.classList.remove('hidden');
        } else {
            if (loginLink) loginLink.classList.remove('hidden');
            if (registerLink) registerLink.classList.remove('hidden');
            if (logoutBtn) logoutBtn.classList.add('hidden');
        }
        updateCartBadge();
    }

    function updateCartBadge(animate = false) {
        try {
            const cart = JSON.parse(localStorage.getItem('cart') || '[]');
            const badge = document.getElementById('cart-badge');
            if (!badge) return;

            const totalItems = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);

            if (totalItems > 0) {
                badge.textContent = totalItems > 99 ? '99+' : totalItems;

                // Adjust width for 2-digit numbers
                if (totalItems >= 10) {
                    badge.classList.add('min-w-[28px]');
                } else {
                    badge.classList.remove('min-w-[28px]');
                }

                badge.classList.remove('hidden');

                // Add pulse animation when item is added
                if (animate) {
                    badge.classList.add('animate-pulse', 'scale-125');
                    setTimeout(() => {
                        badge.classList.remove('animate-pulse', 'scale-125');
                    }, 600);
                }
            } else {
                badge.classList.add('hidden');
            }
        } catch (e) {
            console.error('Error updating cart badge:', e);
        }
    }

    // Event-based cart updates (performance optimized)
    function setupCartListener() {
        // Listen for custom cart update events
        window.addEventListener('cartUpdated', () => {
            updateCartBadge(true);
        });

        // Listen for storage changes (for cross-tab updates)
        window.addEventListener('storage', (e) => {
            if (e.key === 'cart') {
                updateCartBadge();
            }
        });

        // Initial update
        updateCartBadge();
    }

    // Initialize cart listener
    setupCartListener();

    // Make updateCartBadge globally available
    window.updateCartBadge = updateCartBadge;

    async function handleLogout() {
        try {
            await window.axios.post('/api/auth/logout');
        } catch (e) {
            // Token may already be invalid
        }
        window.Auth.removeToken();
        window.location.href = '{{ route("login") }}';
    }
</script>

