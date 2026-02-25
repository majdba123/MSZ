{{-- ═══ Top Bar ═══ --}}
<div class="bg-navy-900 text-gray-400">
    <div class="mx-auto flex h-8 max-w-7xl items-center justify-between px-4 text-[11px] sm:px-6 lg:px-8">
        <span>Welcome to SyriaZone — your trusted marketplace</span>
        <div class="hidden items-center gap-4 sm:flex">
            <span>support@syriazone.com</span>
        </div>
    </div>
</div>

{{-- ═══ Main Navbar ═══ --}}
<nav class="sticky top-0 z-50 border-b border-gray-800 bg-navy-800 shadow-xl">
    <div class="mx-auto flex h-14 max-w-7xl items-center gap-4 px-4 sm:px-6 lg:px-8">
        {{-- Logo --}}
        <a href="{{ url('/') }}" class="flex-shrink-0 text-xl font-extrabold tracking-tight text-white">
            Syria<span class="text-brand-500">Zone</span>
        </a>

        {{-- Categories Mega Menu --}}
        <div class="relative hidden lg:block" id="mega-menu-wrapper">
            <button id="mega-menu-btn" class="flex items-center gap-2 rounded-lg bg-white/10 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-white/15">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                All Categories
                <svg class="h-4 w-4 transition-transform" id="mega-chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
            </button>
            <div id="mega-menu" class="absolute left-0 top-full z-50 mt-2 hidden w-[720px] overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-2xl">
                <div class="flex">
                    <div id="mega-cats-list" class="w-60 border-r border-gray-100 bg-gray-50 py-2">
                        <div class="px-4 py-3 text-center text-xs text-gray-400">Loading...</div>
                    </div>
                    <div id="mega-subs-panel" class="flex-1 p-5">
                        <p class="text-sm text-gray-400">Hover over a category to see subcategories</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Search (optional placeholder) --}}
        <div class="flex-1"></div>

        {{-- Nav links --}}
        <div class="flex items-center gap-2" id="nav-links">
            <a href="{{ route('home') }}#vendors" class="hidden rounded-lg px-3 py-2 text-sm font-medium text-gray-300 transition-colors hover:bg-white/10 hover:text-white md:block">Stores</a>

            {{-- Cart --}}
            <button id="nav-cart" class="relative flex items-center gap-1.5 rounded-lg px-3 py-2 text-gray-300 transition-all hover:bg-white/10 hover:text-white" onclick="window.showCart && window.showCart()" title="Cart">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
                <span class="hidden text-sm font-medium sm:inline">Cart</span>
                <span id="cart-badge" class="absolute -right-1 -top-1 flex h-5 min-w-[20px] items-center justify-center rounded-full bg-brand-500 px-1.5 text-[10px] font-bold text-white shadow-md hidden"></span>
            </button>

            {{-- Auth --}}
            <a href="{{ route('login') }}" id="nav-login" class="rounded-lg px-3 py-2 text-sm font-medium text-gray-300 transition-colors hover:bg-white/10 hover:text-white">Sign In</a>
            <a href="{{ route('register') }}" id="nav-register" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-brand-600">Register</a>
            <button id="nav-logout" class="hidden rounded-lg border border-gray-600 px-3 py-2 text-sm font-medium text-gray-300 transition-colors hover:border-red-500 hover:text-red-400" onclick="handleLogout()">Sign Out</button>

            {{-- Mobile menu --}}
            <button id="mobile-menu-btn" class="rounded-lg p-2 text-gray-400 hover:bg-white/10 hover:text-white lg:hidden">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
            </button>
        </div>
    </div>

    {{-- Mobile Categories Dropdown --}}
    <div id="mobile-menu" class="hidden border-t border-gray-700 bg-navy-900 px-4 py-3 lg:hidden">
        <div id="mobile-cats" class="space-y-1 text-sm text-gray-300"></div>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function () {
    updateNavbar();
    loadNavCategories();

    const megaBtn = document.getElementById('mega-menu-btn');
    const megaMenu = document.getElementById('mega-menu');
    const chevron = document.getElementById('mega-chevron');
    let megaOpen = false;

    if (megaBtn) {
        megaBtn.addEventListener('click', () => {
            megaOpen = !megaOpen;
            megaMenu.classList.toggle('hidden', !megaOpen);
            chevron.style.transform = megaOpen ? 'rotate(180deg)' : '';
        });
        document.addEventListener('click', (e) => {
            if (megaOpen && !document.getElementById('mega-menu-wrapper').contains(e.target)) {
                megaOpen = false;
                megaMenu.classList.add('hidden');
                chevron.style.transform = '';
            }
        });
    }

    const mobileBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    if (mobileBtn) mobileBtn.addEventListener('click', () => mobileMenu.classList.toggle('hidden'));
});

async function loadNavCategories() {
    try {
        const res = await window.axios.get('/api/categories');
        const cats = res.data.data || [];
        const list = document.getElementById('mega-cats-list');
        const panel = document.getElementById('mega-subs-panel');
        const mobileCats = document.getElementById('mobile-cats');

        if (cats.length === 0) {
            list.innerHTML = '<p class="px-4 py-3 text-xs text-gray-400">No categories</p>';
            return;
        }

        list.innerHTML = cats.map(c => `
            <button data-cat-id="${c.id}" class="mega-cat-item flex w-full items-center gap-3 px-4 py-2.5 text-left text-sm text-gray-700 transition-colors hover:bg-white hover:text-brand-600"
                    onmouseenter="showSubcategories(${c.id}, this)"
                    onclick="window.location.href='/#products?category_id=${c.id}'">
                ${c.logo ? `<img src="/storage/${_esc(c.logo)}" class="h-7 w-7 rounded-lg object-cover" alt="">` : `<div class="flex h-7 w-7 items-center justify-center rounded-lg bg-brand-100 text-brand-500"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/></svg></div>`}
                <span class="flex-1 truncate font-medium">${_esc(c.name)}</span>
                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            </button>
        `).join('');

        window._navCats = cats;

        mobileCats.innerHTML = cats.map(c => `
            <a href="/#products?category_id=${c.id}" class="block rounded-lg px-3 py-2 hover:bg-white/10">${_esc(c.name)}</a>
        `).join('');

    } catch (e) { /* silent */ }
}

window.showSubcategories = function(catId, btn) {
    document.querySelectorAll('.mega-cat-item').forEach(el => el.classList.remove('bg-white', 'text-brand-600'));
    btn.classList.add('bg-white', 'text-brand-600');

    const cat = (window._navCats || []).find(c => c.id === catId);
    const panel = document.getElementById('mega-subs-panel');
    if (!cat || !cat.subcategories || cat.subcategories.length === 0) {
        panel.innerHTML = '<p class="text-sm text-gray-400">No subcategories</p>';
        return;
    }
    panel.innerHTML = `
        <h4 class="mb-3 text-xs font-bold uppercase tracking-wider text-gray-400">${_esc(cat.name)}</h4>
        <div class="grid grid-cols-2 gap-2">
            ${cat.subcategories.map(s => `
                <a href="/#products?category_id=${c.id}" class="flex items-center gap-3 rounded-xl p-2.5 transition-colors hover:bg-gray-50">
                    ${s.image ? `<img src="/storage/${_esc(s.image)}" class="h-10 w-10 rounded-lg object-cover" alt="">` : `<div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100"><svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909"/></svg></div>`}
                    <span class="text-sm font-medium text-gray-700">${_esc(s.name)}</span>
                </a>
            `).join('')}
        </div>
    `;
};

function _esc(t) { if (!t) return ''; const d = document.createElement('div'); d.textContent = t; return d.innerHTML; }

function updateNavbar() {
    const isAuth = window.Auth && window.Auth.isAuthenticated();
    const el = (id) => document.getElementById(id);
    if (isAuth) { el('nav-login')?.classList.add('hidden'); el('nav-register')?.classList.add('hidden'); el('nav-logout')?.classList.remove('hidden'); }
    else { el('nav-login')?.classList.remove('hidden'); el('nav-register')?.classList.remove('hidden'); el('nav-logout')?.classList.add('hidden'); }
    updateCartBadge();
}

function updateCartBadge(animate = false) {
    try {
        const cart = JSON.parse(localStorage.getItem('cart') || '[]');
        const badge = document.getElementById('cart-badge');
        if (!badge) return;
        const total = cart.reduce((s, i) => s + (i.quantity || 1), 0);
        if (total > 0) { badge.textContent = total > 99 ? '99+' : total; badge.classList.remove('hidden'); if (animate) { badge.classList.add('animate-pulse'); setTimeout(() => badge.classList.remove('animate-pulse'), 600); } }
        else { badge.classList.add('hidden'); }
    } catch (e) {}
}

function setupCartListener() {
    window.addEventListener('cartUpdated', () => updateCartBadge(true));
    window.addEventListener('storage', (e) => { if (e.key === 'cart') updateCartBadge(); });
    updateCartBadge();
}
setupCartListener();
window.updateCartBadge = updateCartBadge;

async function handleLogout() {
    try { await window.axios.post('/api/auth/logout'); } catch (e) {}
    window.Auth.removeToken();
    window.location.href = '{{ route("login") }}';
}
</script>
