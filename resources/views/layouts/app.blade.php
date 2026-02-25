<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SyriaZone')</title>
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="dns-prefetch" href="https://fonts.bunny.net">
    <link rel="preload" href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet"></noscript>
    <meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#030712" media="(prefers-color-scheme: dark)">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <script>
        (function(){
            const t = localStorage.getItem('sz_theme');
            if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
</head>
<body class="min-h-screen bg-white text-gray-900 antialiased transition-colors duration-300 dark:bg-gray-950 dark:text-gray-100">

    <x-navbar />

    <main>@yield('content')</main>

    {{-- ═══ Global Cart Modal ═══ --}}
    <x-home.cart-modal />

    {{-- ═══ Footer ═══ --}}
    <footer class="border-t border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-900">
        <div class="mx-auto max-w-screen-2xl px-4 py-14 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 gap-8 sm:grid-cols-2 lg:grid-cols-5">
                {{-- Brand --}}
                <div class="col-span-2 sm:col-span-2 lg:col-span-2">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-2xl font-extrabold tracking-tight text-gray-900 dark:text-white">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-500">
                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72"/></svg>
                        </div>
                        Syria<span class="text-brand-500">Zone</span>
                    </a>
                    <p class="mt-4 max-w-sm text-sm leading-relaxed text-gray-500 dark:text-gray-400">
                        Your trusted online marketplace. Discover quality products from verified vendors, delivered to your doorstep.
                    </p>
                    <div class="mt-5 flex gap-3">
                        <a href="#" class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-200 text-gray-600 transition-all hover:bg-brand-500 hover:text-white dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-brand-500 dark:hover:text-white">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="#" class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-200 text-gray-600 transition-all hover:bg-brand-500 hover:text-white dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-brand-500 dark:hover:text-white">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                        <a href="#" class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-200 text-gray-600 transition-all hover:bg-brand-500 hover:text-white dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-brand-500 dark:hover:text-white">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                        </a>
                    </div>
                </div>
                {{-- Quick Links --}}
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-widest text-gray-900 dark:text-gray-200">Shop</h4>
                    <ul class="mt-4 space-y-2.5 text-sm">
                        <li><a href="{{ route('categories.index') }}" class="text-gray-500 transition-colors hover:text-brand-600 dark:text-gray-400 dark:hover:text-brand-400">Categories</a></li>
                        <li><a href="{{ route('products.index') }}" class="text-gray-500 transition-colors hover:text-brand-600 dark:text-gray-400 dark:hover:text-brand-400">All Products</a></li>
                        <li><a href="{{ route('vendors.index') }}" class="text-gray-500 transition-colors hover:text-brand-600 dark:text-gray-400 dark:hover:text-brand-400">All Stores</a></li>
                    </ul>
                </div>
                {{-- Account --}}
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-widest text-gray-900 dark:text-gray-200">Account</h4>
                    <ul class="mt-4 space-y-2.5 text-sm">
                        <li><a href="{{ route('login') }}" class="text-gray-500 transition-colors hover:text-brand-600 dark:text-gray-400 dark:hover:text-brand-400">Sign In</a></li>
                        <li><a href="{{ route('register') }}" class="text-gray-500 transition-colors hover:text-brand-600 dark:text-gray-400 dark:hover:text-brand-400">Create Account</a></li>
                    </ul>
                </div>
                {{-- Contact --}}
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-widest text-gray-900 dark:text-gray-200">Contact</h4>
                    <ul class="mt-4 space-y-2.5 text-sm">
                        <li class="flex items-center gap-2 text-gray-500 dark:text-gray-400"><svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>support@syriazone.com</li>
                        <li class="flex items-center gap-2 text-gray-500 dark:text-gray-400"><svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>Damascus, Syria</li>
                    </ul>
                </div>
            </div>
            <div class="mt-10 flex flex-col items-center justify-between gap-4 border-t border-gray-200 pt-8 sm:flex-row dark:border-gray-800">
                <p class="text-xs text-gray-400 dark:text-gray-500">&copy; {{ date('Y') }} SyriaZone. All rights reserved.</p>
                <div class="flex gap-6 text-xs text-gray-400 dark:text-gray-500">
                    <a href="#" class="hover:text-gray-600 dark:hover:text-gray-300">Privacy Policy</a>
                    <a href="#" class="hover:text-gray-600 dark:hover:text-gray-300">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    {{-- ═══ Global Favourites Logic ═══ --}}
    <script>
    (function(){
        window._favIds = new Set();
        window._favLoaded = false;

        window.loadFavIds = async function() {
            if (!window.Auth || !window.Auth.isAuthenticated()) return;
            try {
                const res = await window.axios.get('/api/favourites/ids');
                window._favIds = new Set(res.data.data || []);
                window._favLoaded = true;
                document.querySelectorAll('[data-fav-btn]').forEach(btn => {
                    const id = parseInt(btn.dataset.favBtn);
                    updateFavBtn(btn, window._favIds.has(id));
                });
            } catch(e) {}
        };

        window.toggleFav = async function(productId, btn) {
            if (!window.Auth || !window.Auth.isAuthenticated()) {
                window.location.href = '/login';
                return;
            }
            try {
                const res = await window.axios.post('/api/favourites/' + productId);
                const isFav = res.data.favourited;
                if (isFav) window._favIds.add(productId); else window._favIds.delete(productId);
                document.querySelectorAll('[data-fav-btn="' + productId + '"]').forEach(b => updateFavBtn(b, isFav));
                favToast(isFav ? 'Added to favourites' : 'Removed from favourites');
            } catch(e) {}
        };

        function updateFavBtn(btn, isFav) {
            const svg = btn.querySelector('svg');
            if (!svg) return;
            if (isFav) {
                svg.setAttribute('fill', 'currentColor');
                btn.classList.add('text-red-500');
                btn.classList.remove('text-gray-400', 'dark:text-gray-500');
            } else {
                svg.setAttribute('fill', 'none');
                btn.classList.remove('text-red-500');
                btn.classList.add('text-gray-400', 'dark:text-gray-500');
            }
        }

        function favToast(msg) {
            const t = document.createElement('div');
            t.className = 'fixed bottom-6 left-6 z-[80] flex items-center gap-3 rounded-2xl bg-gray-900 px-6 py-4 text-sm font-medium text-white shadow-2xl dark:bg-white dark:text-gray-900';
            t.style.animation = 'fadeInUp .3s cubic-bezier(.22,1,.36,1)';
            t.innerHTML = '<svg class="h-5 w-5 text-red-400 dark:text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z"/></svg>' + msg;
            document.body.appendChild(t);
            setTimeout(() => { t.style.opacity = '0'; t.style.transition = 'opacity .3s'; setTimeout(() => t.remove(), 300); }, 2000);
        }

        document.addEventListener('DOMContentLoaded', () => window.loadFavIds());
    })();
    </script>

    {{-- ═══ Global Cart Logic ═══ --}}
    <script>
    (function(){
        function _esc(s){if(!s)return '';const d=document.createElement('div');d.textContent=s;return d.innerHTML;}
        function toast(msg){const t=document.createElement('div');t.className='fixed bottom-6 right-6 z-[80] flex items-center gap-3 rounded-2xl bg-gray-900 px-6 py-4 text-sm font-medium text-white shadow-2xl dark:bg-white dark:text-gray-900';t.style.animation='fadeInUp .3s cubic-bezier(.22,1,.36,1)';t.innerHTML=`<svg class="h-5 w-5 text-emerald-400 dark:text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>${_esc(msg)}`;document.body.appendChild(t);setTimeout(()=>{t.style.opacity='0';t.style.transition='opacity .3s';setTimeout(()=>t.remove(),300);},2500);}

        window.addToCart = function(id,name,price,photo){
            let cart=JSON.parse(localStorage.getItem('cart')||'[]');
            const ex=cart.find(i=>i.id===id);
            if(ex)ex.quantity+=1;else cart.push({id,name,price:parseFloat(price),photo:photo||'',quantity:1});
            localStorage.setItem('cart',JSON.stringify(cart));
            window.dispatchEvent(new CustomEvent('cartUpdated'));
            window._refreshCartDisplay&&window._refreshCartDisplay();
            if(typeof window.updateCartBadge==='function')window.updateCartBadge(true);
            toast('Added to cart!');
        };
        window.removeFromCart = function(id){
            let cart=JSON.parse(localStorage.getItem('cart')||'[]').filter(i=>i.id!==id);
            localStorage.setItem('cart',JSON.stringify(cart));
            window.dispatchEvent(new CustomEvent('cartUpdated'));
            window._refreshCartDisplay&&window._refreshCartDisplay();
            if(typeof window.updateCartBadge==='function')window.updateCartBadge();
        };
        window.updateQty = function(id,qty){
            if(qty<=0){window.removeFromCart(id);return;}
            let cart=JSON.parse(localStorage.getItem('cart')||'[]');
            const item=cart.find(i=>i.id===id);
            if(item){item.quantity=qty;localStorage.setItem('cart',JSON.stringify(cart));window.dispatchEvent(new CustomEvent('cartUpdated'));window._refreshCartDisplay&&window._refreshCartDisplay();if(typeof window.updateCartBadge==='function')window.updateCartBadge();}
        };

        window._refreshCartDisplay = function(){
            const cart=JSON.parse(localStorage.getItem('cart')||'[]');
            const items=document.getElementById('cart-items'),empty=document.getElementById('cart-empty'),total=document.getElementById('cart-total'),checkout=document.getElementById('checkout-btn'),count=document.getElementById('cart-item-count');
            if(!items)return;
            if(!cart.length){items.innerHTML='';empty&&empty.classList.remove('hidden');if(total)total.innerHTML='0.00 <span class="text-sm font-normal text-gray-400">SYP</span>';checkout&&checkout.classList.add('hidden');if(count)count.textContent='0 items';return;}
            empty&&empty.classList.add('hidden');checkout&&checkout.classList.remove('hidden');
            const n=cart.reduce((s,i)=>s+i.quantity,0);if(count)count.textContent=n+' item'+(n!==1?'s':'');
            let t=0;
            items.innerHTML=cart.map(item=>{const sub=item.price*item.quantity;t+=sub;return `<div class="flex items-center gap-3 rounded-xl border border-gray-100 bg-white p-3 dark:border-gray-800 dark:bg-gray-800/50"><div class="h-14 w-14 shrink-0 overflow-hidden rounded-xl bg-gray-50 dark:bg-gray-800">${item.photo?`<img src="${_esc(item.photo)}" class="h-full w-full object-contain p-1" alt="">`:`<div class="flex h-full items-center justify-center"><svg class="h-5 w-5 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159"/></svg></div>`}</div><div class="min-w-0 flex-1"><h4 class="truncate text-sm font-bold text-gray-900 dark:text-white">${_esc(item.name)}</h4><p class="text-xs text-gray-500">${item.price.toLocaleString()} SYP</p><p class="text-xs font-bold text-brand-600 dark:text-brand-400">${sub.toLocaleString()} SYP</p></div><div class="flex flex-col items-end gap-2"><div class="flex items-center rounded-lg border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800"><button onclick="updateQty(${item.id},${item.quantity-1})" class="flex h-7 w-7 items-center justify-center text-gray-500 hover:text-brand-600"><svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" d="M19.5 12h-15"/></svg></button><span class="w-6 text-center text-xs font-bold dark:text-white">${item.quantity}</span><button onclick="updateQty(${item.id},${item.quantity+1})" class="flex h-7 w-7 items-center justify-center text-gray-500 hover:text-brand-600"><svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" d="M12 4.5v15m7.5-7.5h-15"/></svg></button></div><button onclick="removeFromCart(${item.id})" class="text-[10px] font-semibold text-red-500 hover:text-red-700">Remove</button></div></div>`;}).join('');
            if(total)total.innerHTML=t.toLocaleString()+' <span class="text-sm font-normal text-gray-400">SYP</span>';
        };

        window.checkoutCart = async function(){
            const cart = JSON.parse(localStorage.getItem('cart') || '[]');
            if (!cart.length) {
                toast('Your cart is empty.');
                return;
            }

            if (!window.Auth || !window.Auth.isAuthenticated()) {
                window.location.href = '/login';
                return;
            }

            const checkoutBtn = document.getElementById('checkout-btn');
            const originalLabel = checkoutBtn ? checkoutBtn.innerHTML : '';

            if (checkoutBtn) {
                checkoutBtn.disabled = true;
                checkoutBtn.innerHTML = 'Processing...';
            }

            try {
                const couponInput = document.getElementById('cart-coupon-code');
                const couponCode = couponInput ? String(couponInput.value || '').trim().toUpperCase() : '';
                const payload = {
                    items: cart.map(item => ({
                        product_id: item.id,
                        quantity: item.quantity || 1,
                    })),
                    coupon_code: couponCode || null,
                    payment_way: 'cash',
                };

                const response = await window.axios.post('/api/orders/checkout', payload);
                localStorage.removeItem('cart');
                if (couponInput) couponInput.value = '';
                window.dispatchEvent(new CustomEvent('cartUpdated'));
                window._refreshCartDisplay && window._refreshCartDisplay();
                window.closeCartModal && window.closeCartModal();

                toast(response.data?.message || 'Checkout completed successfully.');
            } catch (error) {
                const message = error.response?.data?.message || 'Checkout failed. Please try again.';
                toast(message);
            } finally {
                if (checkoutBtn) {
                    checkoutBtn.disabled = false;
                    checkoutBtn.innerHTML = originalLabel;
                }
            }
        };

        window.showCart = function(){window._refreshCartDisplay();const m=document.getElementById('cart-modal');if(m){m.classList.remove('hidden');document.body.style.overflow='hidden';}};
        window.closeCartModal = function(){const m=document.getElementById('cart-modal');if(m){m.classList.add('hidden');document.body.style.overflow='';}};
        document.addEventListener('DOMContentLoaded', function () {
            const checkoutBtn = document.getElementById('checkout-btn');
            if (checkoutBtn) {
                checkoutBtn.addEventListener('click', function () {
                    window.checkoutCart && window.checkoutCart();
                });
            }
        });
    })();
    </script>

    @stack('scripts')
</body>
</html>
