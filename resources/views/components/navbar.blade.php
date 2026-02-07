{{-- Navigation Bar --}}
<nav class="sticky top-0 z-50 bg-navy-900 shadow-lg">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
        {{-- Logo --}}
        <a href="{{ url('/') }}" class="flex items-center gap-2">
            <span class="text-xl font-bold tracking-tight text-white">Syria<span class="text-brand-500">Zone</span></span>
        </a>

        {{-- Navigation Links --}}
        <div class="flex items-center gap-3" id="nav-links">
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
    }

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

