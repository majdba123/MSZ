@extends('layouts.app')

@section('title', 'Product Details — SyriaZone')

@section('content')
<div class="bg-white dark:bg-gray-950">
    <div class="border-b border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-900">
        <div class="mx-auto max-w-screen-2xl px-4 py-3 sm:px-6 lg:px-8">
            <nav class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                <a href="{{ route('home') }}" class="hover:text-brand-600 dark:hover:text-brand-400">Home</a>
                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                <a href="{{ route('products.index') }}" class="hover:text-brand-600 dark:hover:text-brand-400">Products</a>
                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                <span class="font-medium text-gray-900 dark:text-white" id="bc-name">Details</span>
            </nav>
        </div>
    </div>

    <div class="mx-auto max-w-screen-2xl px-4 py-8 sm:px-6 lg:px-8">
        <div id="show-loading" class="py-16 text-center">
            <div class="mx-auto h-12 w-12 animate-spin rounded-full border-4 border-gray-200 border-t-brand-500 dark:border-gray-700"></div>
            <p class="mt-4 text-sm font-medium text-gray-500 dark:text-gray-400">Loading product details...</p>
        </div>

        <div id="show-content" class="hidden">
            <div class="grid gap-8 lg:grid-cols-3">
                <div class="lg:col-span-2 space-y-6">
                    <div class="overflow-hidden rounded-2xl border border-gray-200/80 bg-white dark:border-gray-800 dark:bg-gray-900">
                        <div id="primary-photo-container" class="relative aspect-square bg-gray-50 dark:bg-gray-800">
                            <p class="absolute inset-0 flex items-center justify-center text-gray-400 dark:text-gray-500">No photo available.</p>
                        </div>
                    </div>
                    <div class="rounded-2xl border border-gray-200/80 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">Gallery</h3>
                            <span id="photo-count" class="text-xs text-gray-400 dark:text-gray-500"></span>
                        </div>
                        <div id="product-photos" class="flex gap-3 overflow-x-auto pb-2 hide-scrollbar"></div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="sticky top-20 space-y-5">
                        <div class="rounded-2xl border border-gray-200/80 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                            <h1 id="product-name" class="mb-3 text-2xl font-black leading-tight text-gray-900 dark:text-white"></h1>
                            <div class="mb-5 flex items-center gap-2 border-b border-gray-100 pb-5 dark:border-gray-800">
                                <span class="text-xs text-gray-400 dark:text-gray-500">Sold by</span>
                                <a id="vendor-link" href="#" class="text-sm font-bold text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300"></a>
                            </div>
                            <div class="mb-5 flex items-baseline gap-2 border-b border-gray-100 pb-5 dark:border-gray-800">
                                <span id="product-price" class="text-3xl font-black text-gray-900 dark:text-white"></span>
                                <span id="product-price-original" class="hidden text-sm text-gray-400 line-through"></span>
                                <span class="text-sm text-gray-400">SYP</span>
                            </div>
                            <div class="mb-5 rounded-xl border border-gray-100 bg-gray-50 p-3 dark:border-gray-800 dark:bg-gray-800/50">
                                <p class="mb-2 text-[10px] font-bold uppercase tracking-widest text-gray-400">Discount Details</p>
                                <div class="grid grid-cols-2 gap-2 text-center">
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Status</p>
                                        <p id="product-discount-status" class="mt-0.5 text-xs font-bold text-gray-900 dark:text-white">—</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Value</p>
                                        <p id="product-discount-value" class="mt-0.5 text-xs font-bold text-red-600 dark:text-red-400">—</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Starts</p>
                                        <p id="product-discount-start" class="mt-0.5 text-xs font-bold text-gray-900 dark:text-white">—</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Ends</p>
                                        <p id="product-discount-end" class="mt-0.5 text-xs font-bold text-gray-900 dark:text-white">—</p>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-5 space-y-3">
                                <p id="product-availability" class="mb-2"></p>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Available:</span>
                                    <span id="product-quantity" class="text-sm font-bold text-gray-900 dark:text-white"></span>
                                </div>
                                <div class="rounded-xl border border-gray-100 bg-gray-50 p-3 dark:border-gray-800 dark:bg-gray-800/50">
                                    <div class="grid grid-cols-3 gap-2 text-center">
                                        <div>
                                            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Category</p>
                                            <p id="product-category" class="mt-0.5 text-xs font-bold text-gray-900 dark:text-white">—</p>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Subcategory</p>
                                            <p id="product-subcategory" class="mt-0.5 text-xs font-bold text-gray-900 dark:text-white">—</p>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Commission</p>
                                            <p id="product-commission" class="mt-0.5 text-xs font-bold text-emerald-600 dark:text-emerald-400">—</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <button id="add-to-cart-btn" class="flex-1 rounded-xl bg-gray-900 py-3.5 text-sm font-bold text-white transition-all hover:bg-brand-600 active:scale-[.97] dark:bg-white dark:text-gray-900 dark:hover:bg-brand-500 dark:hover:text-white" disabled>
                                    <span class="flex items-center justify-center gap-2">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/></svg>
                                        Add to Cart
                                    </span>
                                </button>
                                <button id="fav-detail-btn" onclick="window.toggleFav({{ $productId ?? 0 }},this)" class="flex h-[52px] w-[52px] shrink-0 items-center justify-center rounded-xl border border-gray-200 transition-all hover:scale-105 dark:border-gray-700 text-gray-400 dark:text-gray-500" data-fav-btn="{{ $productId ?? 0 }}">
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                                </button>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-gray-200/80 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                            <h3 class="mb-3 text-base font-bold text-gray-900 dark:text-white">Description</h3>
                            <p id="product-description" class="whitespace-pre-wrap text-sm leading-relaxed text-gray-600 dark:text-gray-400"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="product-error" class="hidden py-16 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
            <p class="mt-4 text-base font-bold text-gray-900 dark:text-white">Product not found</p>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">The product you're looking for doesn't exist or has been removed.</p>
            <a href="{{ route('products.index') }}" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-gray-900 px-6 py-3 text-sm font-bold text-white hover:bg-brand-600 dark:bg-white dark:text-gray-900 dark:hover:bg-brand-500 dark:hover:text-white">Back to Products</a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', async function () {
    const productId = {{ $productId ?? 'null' }};
    const $ = id => document.getElementById(id);
    function esc(s){if(!s)return '';const d=document.createElement('div');d.textContent=s;return d.innerHTML;}

    if (!productId) { $('show-loading').classList.add('hidden'); $('product-error').classList.remove('hidden'); return; }

    try {
        const res = await window.axios.get(`/api/products/${productId}`);
        const p = res.data.data;
        const photos = p.photos || [];

        $('product-name').textContent = p.name || '—';
        $('bc-name').textContent = p.name || 'Details';
        const hasDiscount = !!p.has_active_discount;
        const effectivePrice = parseFloat(hasDiscount ? p.discounted_price : p.price || 0);
        $('product-price').textContent = effectivePrice.toLocaleString();
        $('product-price').className = hasDiscount
            ? 'text-3xl font-black text-red-600 dark:text-red-400'
            : 'text-3xl font-black text-gray-900 dark:text-white';
        if (hasDiscount) {
            $('product-price-original').classList.remove('hidden');
            $('product-price-original').textContent = parseFloat(p.price || 0).toLocaleString() + ' SYP';
        } else {
            $('product-price-original').classList.add('hidden');
            $('product-price-original').textContent = '';
        }
        $('product-quantity').textContent = (p.quantity || 0) + ' units';
        $('product-description').textContent = p.description || 'No description provided.';
        $('product-category').textContent = p.category?.name || '—';
        $('product-subcategory').textContent = p.subcategory?.name || '—';
        $('product-commission').textContent = p.category?.commission ? parseFloat(p.category.commission).toFixed(2) + '%' : '—';
        $('product-discount-status').textContent = formatDiscountStatus(p.discount_status);
        $('product-discount-value').textContent = p.discount_percentage ? `${parseFloat(p.discount_percentage).toFixed(2)}%` : 'No discount';
        $('product-discount-start').textContent = formatDateOnly(p.discount_starts_at);
        $('product-discount-end').textContent = formatDateOnly(p.discount_ends_at);

        if (p.vendor) { const vl = $('vendor-link'); vl.textContent = p.vendor.store_name || '—'; vl.href = `/vendors/${p.vendor.id}`; }

        const inStock = p.quantity > 0;
        $('product-availability').innerHTML = inStock
            ? '<span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>In Stock</span>'
            : '<span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-3 py-1 text-xs font-bold text-red-600 dark:bg-red-500/10 dark:text-red-400"><span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>Out of Stock</span>';

        const btn = $('add-to-cart-btn');
        if (inStock) {
            btn.disabled = false;
            btn.onclick = () => {
                const primary = photos.find(ph => ph.is_primary) || photos[0];
                const url = primary ? (primary.url || `/storage/${primary.path}`) : '';
                window.addToCart(p.id, p.name, hasDiscount ? p.discounted_price : p.price, url);
            };
        } else {
            btn.innerHTML = '<span class="flex items-center justify-center gap-2"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>Out of Stock</span>';
            btn.disabled = true;
            btn.classList.replace('bg-gray-900', 'bg-gray-200');
            btn.classList.add('cursor-not-allowed');
            btn.classList.replace('dark:bg-white', 'dark:bg-gray-800');
            btn.classList.replace('dark:text-gray-900', 'dark:text-gray-500');
        }

        const primary = photos.find(ph => ph.is_primary) || photos[0];
        if (primary) {
            const url = primary.url || `/storage/${primary.path}`;
            $('primary-photo-container').innerHTML = `<img src="${url}" alt="${esc(p.name)}" class="h-full w-full object-contain p-4 transition-transform duration-500 hover:scale-105 cursor-zoom-in" onclick="window._viewLarge(this.src)" loading="eager">`;
        }

        $('photo-count').textContent = photos.length + ' photo' + (photos.length !== 1 ? 's' : '');
        $('product-photos').innerHTML = photos.length ? photos.map(ph => {
            const url = ph.url || `/storage/${ph.path}`;
            return `<button onclick="window._setPrimary('${esc(url)}')" class="group relative h-20 w-20 shrink-0 overflow-hidden rounded-xl border-2 ${ph.is_primary ? 'border-brand-500 ring-2 ring-brand-500/20' : 'border-gray-200 hover:border-brand-300 dark:border-gray-700 dark:hover:border-brand-500'} transition-all"><img src="${url}" class="h-full w-full object-contain bg-white p-1 dark:bg-gray-800" loading="lazy" alt=""><div class="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition-colors"></div></button>`;
        }).join('') : '<p class="py-4 text-xs text-gray-400 dark:text-gray-500">No photos available.</p>';

        window._setPrimary = function(url) {
            $('primary-photo-container').innerHTML = `<img src="${url}" alt="${esc(p.name)}" class="h-full w-full object-contain p-4 transition-transform duration-500 hover:scale-105 cursor-zoom-in" onclick="window._viewLarge(this.src)" loading="eager">`;
        };
        window._viewLarge = function(url) {
            const m = document.createElement('div');
            m.className = 'fixed inset-0 z-[80] flex items-center justify-center bg-black/90 backdrop-blur-sm p-4';
            m.innerHTML = `<div class="relative max-h-[90vh] max-w-[90vw]"><img src="${url}" class="max-h-[90vh] max-w-[90vw] rounded-xl object-contain" alt=""><button onclick="this.closest('.fixed').remove()" class="absolute -right-2 -top-2 flex h-10 w-10 items-center justify-center rounded-full bg-white text-gray-900 shadow-xl hover:scale-110 transition-transform"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button></div>`;
            m.addEventListener('click', e => { if (e.target === m) m.remove(); });
            document.addEventListener('keydown', function h(e) { if (e.key === 'Escape') { m.remove(); document.removeEventListener('keydown', h); } });
            document.body.appendChild(m);
        };

        $('show-loading').classList.add('hidden');
        $('show-content').classList.remove('hidden');
    } catch (e) {
        console.error('Failed to load product:', e);
        $('show-loading').classList.add('hidden');
        $('product-error').classList.remove('hidden');
    }

    function formatDateOnly(value) {
        if (!value) {
            return '—';
        }

        const normalized = typeof value === 'string' ? value.replace(' ', 'T') : value;
        const date = new Date(normalized);
        if (Number.isNaN(date.getTime())) {
            return String(value).slice(0, 10);
        }

        return date.toLocaleDateString();
    }

    function formatDiscountStatus(status) {
        if (status === 'active') return 'Active';
        if (status === 'pending') return 'Pending';
        if (status === 'expired') return 'Expired';

        return '—';
    }
});
</script>
@endpush
