@extends('layouts.app')

@section('title', 'SyriaZone — Your Marketplace for Everything')

@push('styles')
<style>
@keyframes fadeIn { from{opacity:0} to{opacity:1} }
@keyframes fadeInUp { from{opacity:0;transform:translateY(24px)} to{opacity:1;transform:translateY(0)} }
@keyframes fadeInRight { from{opacity:0;transform:translateX(24px)} to{opacity:1;transform:translateX(0)} }
@keyframes slideInRight { from{transform:translateX(100%)} to{transform:translateX(0)} }
@keyframes pulse-dot { 0%,100%{opacity:1} 50%{opacity:.4} }
@keyframes shimmer { 0%{background-position:-200% 0} 100%{background-position:200% 0} }

.anim-up { opacity:0; animation: fadeInUp .6s ease-out forwards; }
.skeleton { background:linear-gradient(90deg,#f3f4f6 25%,#e5e7eb 50%,#f3f4f6 75%); background-size:200% 100%; animation:shimmer 1.5s infinite; }

.product-card { transition:transform .2s ease, box-shadow .2s ease; }
.product-card:hover { transform:translateY(-4px); box-shadow:0 12px 28px -6px rgba(0,0,0,.1); }

.hide-scrollbar { scrollbar-width:none; -ms-overflow-style:none; }
.hide-scrollbar::-webkit-scrollbar { display:none; }

.page-active { background:#f97316!important; color:#fff!important; border-color:#f97316!important; }
</style>
@endpush

@section('content')
    <x-home.hero />
    <x-home.categories />
    <x-home.promo-banner />
    <x-home.vendors />
    <x-home.products />
    <x-home.trust-badges />
    <x-home.cart-modal />
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', async function () {
    let currentPage = 1, selectedVendorId = '', selectedCategoryId = '';
    const $ = id => document.getElementById(id);

    if (typeof window.updateCartBadge === 'function') window.updateCartBadge();

    // ── Categories ──
    async function loadCategories() {
        try {
            const res = await window.axios.get('/api/categories');
            const cats = res.data.data || [];
            $('cats-loading').classList.add('hidden');

            $('filter-category').innerHTML = '<option value="">All Categories</option>' + cats.map(c => `<option value="${c.id}">${esc(c.name)}</option>`).join('');

            $('cats-track').innerHTML = cats.map(cat => {
                const logo = cat.logo ? `/storage/${cat.logo}` : '';
                const subCount = cat.subcategories ? cat.subcategories.length : 0;
                return `
                <div class="group flex-shrink-0 w-40 sm:w-44">
                    <button onclick="filterByCat(${cat.id})" class="flex w-full flex-col items-center gap-3 rounded-2xl border border-gray-100 bg-white p-5 text-center shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-lg hover:border-brand-200">
                        <div class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-2xl bg-gradient-to-br from-brand-50 to-brand-100 transition-transform duration-200 group-hover:scale-110">
                            ${logo ? `<img src="${esc(logo)}" alt="" class="h-full w-full rounded-2xl object-cover">` : `<svg class="h-8 w-8 text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/></svg>`}
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-900 line-clamp-1 group-hover:text-brand-600 transition-colors">${esc(cat.name)}</h4>
                            ${subCount > 0 ? `<p class="mt-0.5 text-[11px] text-gray-400">${subCount} sub</p>` : ''}
                        </div>
                    </button>
                </div>`;
            }).join('');
        } catch(e) { $('cats-loading').innerHTML = '<p class="text-sm text-gray-400">Could not load categories.</p>'; }
    }

    window.filterByCat = function(id) {
        selectedCategoryId = id;
        $('filter-category').value = id;
        currentPage = 1;
        loadProducts();
        $('products').scrollIntoView({ behavior:'smooth' });
    };

    // ── Vendors ──
    async function loadVendors() {
        try {
            const res = await window.axios.get('/api/vendors');
            const vendors = res.data.data || [];
            $('vendors-loading').classList.add('hidden');

            $('filter-vendor').innerHTML = '<option value="">All Stores</option>' + vendors.map(v => `<option value="${v.id}">${esc(v.store_name)}</option>`).join('');

            if (vendors.length === 0) { $('vendors-empty').classList.remove('hidden'); return; }

            const el = $('stat-vendors');
            if (el) el.textContent = vendors.length + '+';

            $('vendors-track').innerHTML = vendors.map(v => {
                const hasLogo = v.logo && v.logo !== 'null';
                return `
                <a href="/vendors/${v.id}" class="group flex-shrink-0 w-60 sm:w-64 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-lg hover:border-brand-200">
                    <div class="relative h-32 overflow-hidden bg-gradient-to-br from-gray-100 to-gray-50">
                        ${hasLogo
                            ? `<img src="${esc(v.logo)}" alt="${esc(v.store_name)}" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">`
                            : `<div class="flex h-full w-full items-center justify-center">
                                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-brand-100 text-brand-500">
                                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72"/></svg>
                                </div>
                            </div>`
                        }
                    </div>
                    <div class="p-4">
                        <h4 class="text-sm font-bold text-gray-900 line-clamp-1 group-hover:text-brand-600 transition-colors">${esc(v.store_name)}</h4>
                        <p class="mt-1 text-xs text-gray-500 line-clamp-2">${esc(v.description || 'Visit our store for amazing products')}</p>
                        <span class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-brand-600">
                            View Store <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                        </span>
                    </div>
                </a>`;
            }).join('');
        } catch(e) {
            $('vendors-loading').classList.add('hidden');
            $('vendors-empty').classList.remove('hidden');
        }
    }

    // ── Products ──
    async function loadProducts() {
        $('products-loading').classList.remove('hidden');
        $('products-grid').innerHTML = '';
        $('products-empty').classList.add('hidden');
        $('products-pagination').innerHTML = '';

        try {
            const params = new URLSearchParams({ page: currentPage });
            if (selectedVendorId) params.append('vendor_id', selectedVendorId);
            if (selectedCategoryId) params.append('category_id', selectedCategoryId);

            const res = await window.axios.get('/api/products?' + params.toString());
            const { data, meta } = res.data;

            const statEl = $('stat-products');
            if (statEl && meta.total) statEl.textContent = meta.total + '+';

            if (data.length === 0) { $('products-empty').classList.remove('hidden'); }
            else {
                $('products-grid').innerHTML = data.map((p, i) => {
                    const photo = p.first_photo_url || '';
                    const inStock = p.quantity > 0;
                    return `
                    <div class="product-card overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm" style="animation:fadeInUp .5s ease-out ${i*.03}s both;">
                        <a href="/products/${p.id}" class="block">
                            <div class="relative aspect-[4/5] overflow-hidden bg-gray-50">
                                ${photo
                                    ? `<img src="${esc(photo)}" alt="${esc(p.name)}" class="h-full w-full object-contain p-3 transition-transform duration-300 hover:scale-105" loading="lazy">`
                                    : `<div class="flex h-full items-center justify-center"><svg class="h-10 w-10 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909"/></svg></div>`
                                }
                                ${!inStock ? `<div class="absolute inset-0 flex items-center justify-center bg-white/60"><span class="rounded-full bg-red-50 px-3 py-1 text-[11px] font-bold text-red-600 ring-1 ring-red-200">Sold Out</span></div>` : ''}
                            </div>
                        </a>
                        <div class="p-3 sm:p-4">
                            ${p.vendor ? `<p class="mb-1 truncate text-[11px] font-medium text-gray-400">${esc(p.vendor.store_name)}</p>` : ''}
                            <a href="/products/${p.id}"><h3 class="line-clamp-2 text-sm font-semibold leading-snug text-gray-900 transition-colors hover:text-brand-600">${esc(p.name)}</h3></a>
                            <div class="mt-2 flex items-baseline gap-1">
                                <span class="text-lg font-extrabold text-gray-900">${parseFloat(p.price).toLocaleString()}</span>
                                <span class="text-[11px] text-gray-400">SYP</span>
                            </div>
                            <button data-pid="${p.id}" data-pname="${esc(p.name)}" data-pprice="${p.price}" data-pphoto="${esc(photo)}" onclick="handleAdd(this)"
                                class="mt-3 flex w-full items-center justify-center gap-1.5 rounded-xl py-2.5 text-xs font-bold transition-all ${inStock ? 'bg-brand-500 text-white hover:bg-brand-600 active:scale-[.97]' : 'bg-gray-100 text-gray-400 cursor-not-allowed'}" ${!inStock ? 'disabled' : ''}>
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                ${inStock ? 'Add to Cart' : 'Sold Out'}
                            </button>
                        </div>
                    </div>`;
                }).join('');
            }
            renderPagination(meta);
        } catch(e) {
            $('products-empty').classList.remove('hidden');
        } finally {
            $('products-loading').classList.add('hidden');
        }
    }

    function renderPagination(meta) {
        if (!meta || meta.last_page <= 1) return;
        const { current_page: cur, last_page: last } = meta;
        let h = `<button onclick="goPage(${cur-1})" class="flex h-9 items-center gap-1 rounded-lg border border-gray-200 bg-white px-3 text-xs font-semibold text-gray-600 shadow-sm hover:bg-gray-50 ${cur===1?'opacity-40 pointer-events-none':''}" ${cur===1?'disabled':''}><svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>Prev</button>`;
        getRange(cur,last).forEach(p => {
            h += p === '...' ? '<span class="px-1 text-gray-400">...</span>' : `<button onclick="goPage(${p})" class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-white text-xs font-bold shadow-sm hover:bg-gray-50 ${p===cur?'page-active':''}">${p}</button>`;
        });
        h += `<button onclick="goPage(${cur+1})" class="flex h-9 items-center gap-1 rounded-lg border border-gray-200 bg-white px-3 text-xs font-semibold text-gray-600 shadow-sm hover:bg-gray-50 ${cur===last?'opacity-40 pointer-events-none':''}" ${cur===last?'disabled':''}>Next<svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg></button>`;
        $('products-pagination').innerHTML = h;
    }

    function getRange(cur, last) {
        if (last <= 7) return Array.from({length:last},(_,i)=>i+1);
        const p=[1]; if(cur>3)p.push('...'); for(let i=Math.max(2,cur-1);i<=Math.min(last-1,cur+1);i++)p.push(i); if(cur<last-2)p.push('...'); p.push(last); return p;
    }

    window.goPage = function(p) { currentPage=p; loadProducts(); $('products').scrollIntoView({behavior:'smooth'}); };

    // ── Filters ──
    $('apply-filters').addEventListener('click', () => { currentPage=1; selectedVendorId=$('filter-vendor').value; selectedCategoryId=$('filter-category').value; loadProducts(); });
    $('clear-filters').addEventListener('click', () => { $('filter-vendor').value=''; $('filter-category').value=''; selectedVendorId=''; selectedCategoryId=''; currentPage=1; loadProducts(); });

    // ── Cart ──
    function dispatchCart() { window.dispatchEvent(new CustomEvent('cartUpdated')); }

    window.addToCart = function(id, name, price, photo) {
        let cart = JSON.parse(localStorage.getItem('cart')||'[]');
        const ex = cart.find(i=>i.id===id);
        if(ex) ex.quantity+=1; else cart.push({id,name,price:parseFloat(price),photo:photo||'',quantity:1});
        localStorage.setItem('cart',JSON.stringify(cart));
        dispatchCart(); updateCartDisplay();
        if(typeof window.updateCartBadge==='function') window.updateCartBadge(true);
        toast('Product added to cart!');
    };

    window.removeFromCart = function(id) {
        let cart = JSON.parse(localStorage.getItem('cart')||'[]').filter(i=>i.id!==id);
        localStorage.setItem('cart',JSON.stringify(cart));
        dispatchCart(); updateCartDisplay();
        if(typeof window.updateCartBadge==='function') window.updateCartBadge();
    };

    window.updateQty = function(id, qty) {
        if(qty<=0){window.removeFromCart(id);return;}
        let cart = JSON.parse(localStorage.getItem('cart')||'[]');
        const item = cart.find(i=>i.id===id);
        if(item){item.quantity=qty;localStorage.setItem('cart',JSON.stringify(cart));dispatchCart();updateCartDisplay();if(typeof window.updateCartBadge==='function')window.updateCartBadge();}
    };

    function updateCartDisplay() {
        const cart = JSON.parse(localStorage.getItem('cart')||'[]');
        const items=$('cart-items'),empty=$('cart-empty'),total=$('cart-total'),checkout=$('checkout-btn'),count=$('cart-item-count');
        if(cart.length===0){items.innerHTML='';empty.classList.remove('hidden');total.innerHTML='0.00 <span class="text-sm font-normal text-gray-400">SYP</span>';checkout.classList.add('hidden');count.textContent='0 items';return;}
        empty.classList.add('hidden');checkout.classList.remove('hidden');
        let t=0;const n=cart.reduce((s,i)=>s+i.quantity,0);count.textContent=n+' item'+(n!==1?'s':'');
        items.innerHTML=cart.map(item=>{const sub=item.price*item.quantity;t+=sub;return `
            <div class="flex items-center gap-3 rounded-xl border border-gray-100 bg-white p-3 shadow-sm">
                <img src="${esc(item.photo||'/images/placeholder.png')}" alt="" class="h-14 w-14 rounded-lg object-cover ring-1 ring-gray-100">
                <div class="flex-1 min-w-0">
                    <h4 class="truncate text-sm font-semibold text-gray-900">${esc(item.name)}</h4>
                    <p class="text-xs text-gray-500">${item.price.toFixed(2)} SYP</p>
                    <p class="text-xs font-bold text-brand-600">${sub.toFixed(2)} SYP</p>
                </div>
                <div class="flex flex-col items-end gap-2">
                    <div class="flex items-center gap-0.5 rounded-lg border border-gray-200 bg-gray-50">
                        <button onclick="updateQty(${item.id},${item.quantity-1})" class="flex h-7 w-7 items-center justify-center text-gray-500 hover:text-brand-600"><svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" d="M19.5 12h-15"/></svg></button>
                        <span class="w-7 text-center text-xs font-bold">${item.quantity}</span>
                        <button onclick="updateQty(${item.id},${item.quantity+1})" class="flex h-7 w-7 items-center justify-center text-gray-500 hover:text-brand-600"><svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" d="M12 4.5v15m7.5-7.5h-15"/></svg></button>
                    </div>
                    <button onclick="removeFromCart(${item.id})" class="text-[10px] font-medium text-red-500 hover:text-red-700">Remove</button>
                </div>
            </div>`;}).join('');
        total.innerHTML=t.toFixed(2)+' <span class="text-sm font-normal text-gray-400">SYP</span>';
    }

    window.showCart = function() { updateCartDisplay(); $('cart-modal').classList.remove('hidden'); $('cart-modal').style.display='flex'; document.body.style.overflow='hidden'; };
    window.closeCartModal = function() { $('cart-modal').classList.add('hidden'); $('cart-modal').style.display='none'; document.body.style.overflow=''; };

    window.handleAdd = function(btn) {
        const id=parseInt(btn.dataset.pid),name=btn.dataset.pname,price=parseFloat(btn.dataset.pprice),photo=btn.dataset.pphoto||'';
        if(typeof window.addToCart==='function') window.addToCart(id,name,price,photo);
    };

    function esc(s){if(!s)return '';const d=document.createElement('div');d.textContent=s;return d.innerHTML;}
    function toast(msg){const t=document.createElement('div');t.className='fixed bottom-6 right-6 z-[70] flex items-center gap-3 rounded-xl bg-gray-900 px-5 py-3.5 text-sm font-medium text-white shadow-2xl';t.style.animation='fadeInUp .3s ease-out';t.innerHTML=`<svg class="h-5 w-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>${esc(msg)}`;document.body.appendChild(t);setTimeout(()=>{t.style.opacity='0';t.style.transition='opacity .3s';setTimeout(()=>t.remove(),300);},2500);}

    // ── URL params ──
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('category_id')) { selectedCategoryId = urlParams.get('category_id'); }

    // ── Init ──
    await Promise.all([loadCategories(), loadVendors()]);
    loadProducts();
});
</script>
@endpush
