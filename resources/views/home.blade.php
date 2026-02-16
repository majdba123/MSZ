@extends('layouts.app')

@section('title', 'SyriaZone')

@section('content')
{{-- Hero Banner - Amazon Style --}}
<div class="bg-gray-100">
    <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
        <div class="relative h-64 overflow-hidden rounded-lg bg-gradient-to-r from-brand-500 to-brand-600 sm:h-80 lg:h-96">
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="text-center text-white">
                    <h1 class="text-4xl font-bold sm:text-5xl lg:text-6xl">SyriaZone</h1>
                    <p class="mt-4 text-xl sm:text-2xl">Shop Everything You Need</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-gray-50">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

    {{-- Featured Vendors Section - Amazon Style --}}
    <div id="vendors" class="mb-12 scroll-mt-20">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">Shop by Vendor</h2>
            <div class="hidden gap-2 sm:flex">
                <button id="prev-vendors" class="flex h-8 w-8 items-center justify-center rounded border border-gray-300 bg-white text-gray-600 hover:bg-gray-50">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </button>
                <button id="next-vendors" class="flex h-8 w-8 items-center justify-center rounded border border-gray-300 bg-white text-gray-600 hover:bg-gray-50">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </button>
            </div>
        </div>
        <div id="vendors-loading" class="flex justify-center py-8">
            <div class="h-8 w-8 animate-spin rounded-full border-4 border-gray-200 border-t-brand-500"></div>
        </div>
        <div id="vendors-container" class="relative overflow-hidden">
            <div id="vendors-carousel" class="flex gap-4 transition-transform duration-300 ease-in-out"></div>
        </div>
        <div id="vendors-empty" class="hidden text-center py-8 text-gray-500">No vendors available.</div>
    </div>

    {{-- Products Section - Amazon Style --}}
    <div id="products" class="mb-12 scroll-mt-20">
        <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-2xl font-bold text-gray-900">Products</h2>
            <div class="flex flex-wrap items-center gap-2">
                <select id="filter-vendor" class="rounded border border-gray-300 bg-white px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                    <option value="">All Vendors</option>
                </select>
                <button id="apply-filters" class="rounded bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">Apply</button>
                <button id="clear-filters" class="rounded border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Clear</button>
            </div>
        </div>

        {{-- Loading State --}}
        <div id="products-loading" class="flex justify-center py-12">
            <div class="h-10 w-10 animate-spin rounded-full border-4 border-gray-200 border-t-brand-500"></div>
        </div>

        {{-- Products Grid - Amazon Style --}}
        <div id="products-grid" class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5"></div>

        {{-- Empty State --}}
        <div id="products-empty" class="hidden text-center py-12">
            <p class="text-lg font-medium text-gray-500">No products found.</p>
        </div>

        {{-- Pagination --}}
        <div id="products-pagination" class="mt-8 flex items-center justify-center gap-4"></div>
    </div>
</div>

{{-- Cart Modal --}}
<div id="cart-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4" style="animation: fadeIn 0.2s ease-out;">
    <div class="relative mx-auto w-full max-w-2xl rounded-2xl bg-white shadow-2xl" style="animation: slideUp 0.3s ease-out; max-height: 90vh; display: flex; flex-direction: column;">
        {{-- Header --}}
        <div class="sticky top-0 z-10 flex items-center justify-between rounded-t-2xl border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white px-6 py-5 backdrop-blur-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-500/10">
                    <svg class="h-5 w-5 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Shopping Cart</h3>
                    <p class="text-xs font-medium text-gray-500" id="cart-item-count">0 items</p>
                </div>
            </div>
            <button id="close-cart" class="group flex h-9 w-9 items-center justify-center rounded-lg text-gray-400 transition-all hover:bg-gray-100 hover:text-gray-600">
                <svg class="h-5 w-5 transition-transform group-hover:rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Cart Items --}}
        <div class="flex-1 overflow-y-auto px-6 py-4" style="scrollbar-width: thin; scrollbar-color: #e5e7eb transparent;">
            <div id="cart-items" class="space-y-3"></div>
            <div id="cart-empty" class="hidden py-16 text-center">
                <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-gray-100">
                    <svg class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                    </svg>
                </div>
                <p class="text-lg font-semibold text-gray-700">Your cart is empty</p>
                <p class="mt-1 text-sm text-gray-500">Start adding products to see them here</p>
            </div>
        </div>

        {{-- Footer --}}
        <div class="sticky bottom-0 rounded-b-2xl border-t border-gray-200 bg-gradient-to-b from-white to-gray-50 px-6 py-5 shadow-lg">
            <div class="mb-4 flex items-center justify-between rounded-lg bg-brand-50 px-4 py-3">
                <span class="text-sm font-semibold uppercase tracking-wide text-gray-600">Total Amount</span>
                <span id="cart-total" class="text-2xl font-bold text-brand-600">0.00 <span class="text-sm font-normal text-gray-500">SYP</span></span>
            </div>
            <button id="checkout-btn" class="hidden w-full rounded-xl bg-gradient-to-r from-brand-600 to-brand-700 py-3.5 text-base font-bold text-white shadow-lg transition-all hover:from-brand-700 hover:to-brand-800 hover:shadow-xl active:scale-[0.98]">
                <span class="flex items-center justify-center gap-2">
                    <span>Proceed to Checkout</span>
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </span>
            </button>
        </div>
    </div>
</div>

<style>
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(10px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.cart-item-enter {
    animation: slideInRight 0.3s ease-out;
}

#cart-modal .flex-1::-webkit-scrollbar {
    width: 6px;
}

#cart-modal .flex-1::-webkit-scrollbar-track {
    background: transparent;
}

#cart-modal .flex-1::-webkit-scrollbar-thumb {
    background: #e5e7eb;
    border-radius: 3px;
}

#cart-modal .flex-1::-webkit-scrollbar-thumb:hover {
    background: #d1d5db;
}
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', async function () {
    let currentPage = 1;
    let selectedVendorId = '';
    const productsGrid = document.getElementById('products-grid');
    const productsLoading = document.getElementById('products-loading');
    const productsEmpty = document.getElementById('products-empty');
    const productsPagination = document.getElementById('products-pagination');
    const filterVendor = document.getElementById('filter-vendor');
    const applyFilters = document.getElementById('apply-filters');
    const clearFilters = document.getElementById('clear-filters');
    const cartModal = document.getElementById('cart-modal');
    const closeCart = document.getElementById('close-cart');
    const cartItems = document.getElementById('cart-items');
    const cartEmpty = document.getElementById('cart-empty');
    const cartTotal = document.getElementById('cart-total');
    const checkoutBtn = document.getElementById('checkout-btn');

    // Initialize cart badge on page load
    if (typeof window.updateCartBadge === 'function') {
        window.updateCartBadge();
    }

    // Load vendors for filter and carousel
    async function loadVendors() {
        const vendorsLoading = document.getElementById('vendors-loading');
        const vendorsCarousel = document.getElementById('vendors-carousel');
        const vendorsEmpty = document.getElementById('vendors-empty');
        const vendorsContainer = document.getElementById('vendors-container');
        const prevBtn = document.getElementById('prev-vendors');
        const nextBtn = document.getElementById('next-vendors');

        try {
            vendorsLoading.classList.remove('hidden');
            vendorsCarousel.innerHTML = '';
            vendorsEmpty.classList.add('hidden');

            const vendorsRes = await window.axios.get('/api/vendors');
            const vendors = vendorsRes.data.data || [];

            // Populate filter dropdown
            filterVendor.innerHTML = '<option value="">All Vendors</option>' +
                vendors.map(v => `<option value="${v.id}">${esc(v.store_name)}</option>`).join('');

            // Populate carousel
            if (vendors.length === 0) {
                vendorsEmpty.classList.remove('hidden');
                vendorsContainer.classList.add('hidden');
            } else {
                vendorsContainer.classList.remove('hidden');
                vendorsCarousel.innerHTML = vendors.map(vendor => `
                    <div class="flex-shrink-0 w-56 rounded border border-gray-200 bg-white p-4 hover:shadow-md transition-shadow">
                        <div class="mb-3 h-32 bg-gray-100 rounded flex items-center justify-center overflow-hidden">
                            ${vendor.logo ? `
                                <img src="${esc(vendor.logo)}" alt="${esc(vendor.store_name)}" class="h-full w-full object-cover">
                            ` : `
                                <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            `}
                        </div>
                        <h3 class="mb-1 text-sm font-semibold text-gray-900 line-clamp-1">${esc(vendor.store_name)}</h3>
                        <p class="mb-3 text-xs text-gray-600 line-clamp-2">${esc(vendor.description || 'No description available')}</p>
                        <button onclick="selectedVendorId = '${vendor.id}'; currentPage = 1; loadProducts();"
                                class="w-full rounded bg-brand-500 px-3 py-2 text-xs font-medium text-white hover:bg-brand-600">
                            View Products
                        </button>
                    </div>
                `).join('');

                // Carousel navigation
                let currentScroll = 0;
                const scrollAmount = 280; // card width (256px) + gap (24px)

                // Remove any existing event listeners by replacing with new handlers
                prevBtn.replaceWith(prevBtn.cloneNode(true));
                nextBtn.replaceWith(nextBtn.cloneNode(true));

                // Get fresh references after replacement
                const freshPrevBtn = document.getElementById('prev-vendors');
                const freshNextBtn = document.getElementById('next-vendors');

                const updateCarouselButtons = () => {
                    const maxScroll = Math.max(0, vendorsCarousel.scrollWidth - vendorsContainer.offsetWidth);
                    if (currentScroll === 0) {
                        freshPrevBtn.classList.add('opacity-50');
                        freshPrevBtn.disabled = true;
                    } else {
                        freshPrevBtn.classList.remove('opacity-50');
                        freshPrevBtn.disabled = false;
                    }
                    if (currentScroll >= maxScroll) {
                        freshNextBtn.classList.add('opacity-50');
                        freshNextBtn.disabled = true;
                    } else {
                        freshNextBtn.classList.remove('opacity-50');
                        freshNextBtn.disabled = false;
                    }
                };

                freshPrevBtn.onclick = () => {
                    currentScroll = Math.max(0, currentScroll - scrollAmount);
                    vendorsCarousel.style.transform = `translateX(-${currentScroll}px)`;
                    updateCarouselButtons();
                };

                freshNextBtn.onclick = () => {
                    const maxScroll = Math.max(0, vendorsCarousel.scrollWidth - vendorsContainer.offsetWidth);
                    currentScroll = Math.min(maxScroll, currentScroll + scrollAmount);
                    vendorsCarousel.style.transform = `translateX(-${currentScroll}px)`;
                    updateCarouselButtons();
                };

                // Show/hide navigation buttons
                freshPrevBtn.classList.remove('hidden');
                freshNextBtn.classList.remove('hidden');
                updateCarouselButtons();
            }
        } catch (e) {
            console.error('Failed to load vendors:', e);
            vendorsEmpty.classList.remove('hidden');
            vendorsEmpty.innerHTML = '<p class="text-lg font-medium text-red-500">Failed to load vendors. Please try again later.</p>';
        } finally {
            vendorsLoading.classList.add('hidden');
        }
    }

    // Load vendors
    await loadVendors();

    // Load products
    loadProducts();

    // Event listeners
    applyFilters.addEventListener('click', () => {
        currentPage = 1;
        selectedVendorId = filterVendor.value;
        loadProducts();
    });

    clearFilters.addEventListener('click', () => {
        filterVendor.value = '';
        selectedVendorId = '';
        currentPage = 1;
        loadProducts();
    });

    closeCart.addEventListener('click', () => {
        const modalContent = cartModal.querySelector('.relative');
        modalContent.style.animation = 'slideUp 0.2s ease-out reverse';
        setTimeout(() => {
            cartModal.classList.add('hidden');
            cartModal.style.display = 'none';
        }, 200);
    });

    cartModal.addEventListener('click', (e) => {
        if (e.target === cartModal) {
            const modalContent = cartModal.querySelector('.relative');
            modalContent.style.animation = 'slideUp 0.2s ease-out reverse';
            setTimeout(() => {
                cartModal.classList.add('hidden');
                cartModal.style.display = 'none';
            }, 200);
        }
    });

    // Helper function to dispatch cart update event
    function dispatchCartUpdate() {
        window.dispatchEvent(new CustomEvent('cartUpdated'));
    }

    // Cart functions - Make globally accessible
    window.addToCart = function(productId, productName, productPrice, productPhoto) {
        try {
            let cart = JSON.parse(localStorage.getItem('cart') || '[]');
            const existingItem = cart.find(item => item.id === productId);

            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({
                    id: productId,
                    name: productName,
                    price: parseFloat(productPrice),
                    photo: productPhoto || '/images/placeholder.png',
                    quantity: 1
                });
            }

            localStorage.setItem('cart', JSON.stringify(cart));
            dispatchCartUpdate();
            if (typeof updateCartDisplay === 'function') updateCartDisplay();
            if (typeof window.updateCartBadge === 'function') window.updateCartBadge(true);
            showAlert('Product added to cart!', 'success');
        } catch (e) {
            console.error('Error adding to cart:', e);
            showAlert('Failed to add product to cart', 'error');
        }
    };

    window.removeFromCart = function(productId) {
        let cart = JSON.parse(localStorage.getItem('cart') || '[]');
        cart = cart.filter(item => item.id !== productId);
        localStorage.setItem('cart', JSON.stringify(cart));
        dispatchCartUpdate();
        if (typeof updateCartDisplay === 'function') updateCartDisplay();
        if (typeof window.updateCartBadge === 'function') window.updateCartBadge();
    };

    window.updateCartQuantity = function(productId, quantity) {
        let cart = JSON.parse(localStorage.getItem('cart') || '[]');
        const item = cart.find(item => item.id === productId);
        if (item) {
            if (quantity <= 0) {
                window.removeFromCart(productId);
            } else {
                item.quantity = quantity;
                localStorage.setItem('cart', JSON.stringify(cart));
                dispatchCartUpdate();
                if (typeof updateCartDisplay === 'function') updateCartDisplay();
                if (typeof window.updateCartBadge === 'function') window.updateCartBadge();
            }
        }
    }

    function updateCartDisplay() {
        const cart = JSON.parse(localStorage.getItem('cart') || '[]');

        if (cart.length === 0) {
            cartItems.innerHTML = '';
            cartEmpty.classList.remove('hidden');
            cartTotal.textContent = '0.00';
            checkoutBtn.classList.add('hidden');
            document.getElementById('cart-item-count').textContent = '0 items';
        } else {
            cartEmpty.classList.add('hidden');
            checkoutBtn.classList.remove('hidden');

            let total = 0;
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            document.getElementById('cart-item-count').textContent = totalItems + ' item' + (totalItems !== 1 ? 's' : '');

            cartItems.innerHTML = cart.map((item, index) => {
                const itemTotal = item.price * item.quantity;
                total += itemTotal;
                return `
                    <div class="cart-item-enter group relative flex items-center gap-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all hover:border-brand-300 hover:shadow-md" style="animation-delay: ${index * 0.05}s;">
                        <div class="relative flex-shrink-0">
                            <img src="${esc(item.photo || '/images/placeholder.png')}"
                                 alt="${esc(item.name)}"
                                 class="h-16 w-16 rounded-lg object-cover ring-2 ring-gray-100 transition-all group-hover:ring-brand-300">
                            <div class="absolute -bottom-1 -right-1 flex h-6 w-6 items-center justify-center rounded-full bg-brand-500 text-xs font-bold text-white shadow-lg">
                                ${item.quantity}
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="truncate font-semibold text-gray-900 transition-colors group-hover:text-brand-600">${esc(item.name)}</h4>
                            <p class="mt-0.5 text-xs font-medium text-gray-500">${item.price.toFixed(2)} SYP per unit</p>
                            <div class="mt-2 flex items-center gap-3">
                                <span class="text-sm font-bold text-brand-600">${itemTotal.toFixed(2)} SYP</span>
                                <span class="text-xs text-gray-400">× ${item.quantity}</span>
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-3">
                            <div class="flex items-center gap-1 rounded-lg border border-gray-200 bg-gray-50 p-1">
                                <button onclick="window.updateCartQuantity(${item.id}, ${item.quantity - 1})"
                                        class="flex h-8 w-8 items-center justify-center rounded-md text-gray-600 transition-all hover:bg-white hover:text-brand-600 hover:shadow-sm active:scale-95"
                                        title="Decrease quantity">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15" />
                                    </svg>
                                </button>
                                <span class="min-w-[2.5rem] text-center text-sm font-bold text-gray-900">${item.quantity}</span>
                                <button onclick="window.updateCartQuantity(${item.id}, ${item.quantity + 1})"
                                        class="flex h-8 w-8 items-center justify-center rounded-md text-gray-600 transition-all hover:bg-white hover:text-brand-600 hover:shadow-sm active:scale-95"
                                        title="Increase quantity">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                </button>
                            </div>
                            <button onclick="window.removeFromCart(${item.id})"
                                    class="flex items-center gap-1.5 rounded-md px-2.5 py-1.5 text-xs font-medium text-red-600 transition-all hover:bg-red-50 hover:text-red-700 active:scale-95">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                                Remove
                            </button>
                        </div>
                    </div>
                `;
            }).join('');

            cartTotal.textContent = total.toFixed(2);
        }
    }

    // Show cart modal with animation
    window.showCart = function() {
        updateCartDisplay();
        cartModal.classList.remove('hidden');
        cartModal.style.display = 'flex';
        // Trigger animation
        setTimeout(() => {
            cartModal.querySelector('.relative').style.animation = 'slideUp 0.3s ease-out';
        }, 10);
    };

    async function loadProducts() {
        productsLoading.classList.remove('hidden');
        productsGrid.innerHTML = '';
        productsEmpty.classList.add('hidden');
        productsPagination.innerHTML = '';

        try {
            const params = new URLSearchParams({
                page: currentPage,
            });
            if (selectedVendorId) {
                params.append('vendor_id', selectedVendorId);
            }

            const response = await window.axios.get(`/api/products?${params.toString()}`);
            const { data, meta } = response.data;

            if (data.length === 0) {
                productsEmpty.classList.remove('hidden');
            } else {
                productsGrid.innerHTML = data.map(product => `
                    <div class="group relative rounded border border-gray-200 bg-white p-4 hover:shadow-lg transition-shadow">
                        <div class="mb-3 aspect-square w-full overflow-hidden rounded bg-gray-100">
                            <a href="/products/${product.id}">
                                <img src="${esc(product.first_photo_url || '/images/placeholder.png')}"
                                     alt="${esc(product.name)}"
                                     class="h-full w-full object-cover">
                            </a>
                        </div>
                        <div class="space-y-2">
                            ${product.vendor ? `
                                <p class="text-xs text-gray-500">${esc(product.vendor.store_name)}</p>
                            ` : ''}
                            <a href="/products/${product.id}" class="block">
                                <h3 class="text-sm font-normal text-gray-900 line-clamp-2 h-10 hover:text-brand-600">${esc(product.name)}</h3>
                            </a>
                            <div class="flex items-center gap-1">
                                <div class="flex text-yellow-400">
                                    ${Array(5).fill(0).map((_, i) => `
                                        <svg class="h-4 w-4 ${i < 4 ? 'fill-current' : 'text-gray-300'}" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                        </svg>
                                    `).join('')}
                                </div>
                                <span class="text-xs text-gray-500">(123)</span>
                            </div>
                            <div class="flex items-baseline gap-1">
                                <span class="text-lg font-bold text-gray-900">${parseFloat(product.price).toFixed(2)}</span>
                                <span class="text-sm text-gray-500">SYP</span>
                            </div>
                            ${product.quantity <= 0 ? `
                                <p class="text-xs text-red-600 font-medium">Currently unavailable</p>
                            ` : `
                                <p class="text-xs text-green-600 font-medium">In Stock</p>
                            `}
                            <button data-product-id="${product.id}"
                                    data-product-name="${esc(product.name)}"
                                    data-product-price="${product.price}"
                                    data-product-photo="${esc(product.first_photo_url || '')}"
                                    onclick="handleAddToCartFromCard(this)"
                                    class="w-full rounded bg-yellow-400 px-4 py-2 text-sm font-medium text-gray-900 hover:bg-yellow-500 ${product.quantity <= 0 ? 'opacity-50 cursor-not-allowed' : ''}"
                                    ${product.quantity <= 0 ? 'disabled' : ''}>
                                Add to Cart
                            </button>
                        </div>
                    </div>
                `).join('');
            }

            // Pagination
            if (meta.last_page > 1) {
                productsPagination.innerHTML = `
                    <button ${meta.current_page === 1 ? 'disabled' : ''}
                            onclick="currentPage = ${meta.current_page - 1}; loadProducts();"
                            class="rounded border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 ${meta.current_page === 1 ? 'opacity-50 cursor-not-allowed' : ''}">
                        Previous
                    </button>
                    <span class="text-sm text-gray-700">Page ${meta.current_page} of ${meta.last_page}</span>
                    <button ${meta.current_page === meta.last_page ? 'disabled' : ''}
                            onclick="currentPage = ${meta.current_page + 1}; loadProducts();"
                            class="rounded border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 ${meta.current_page === meta.last_page ? 'opacity-50 cursor-not-allowed' : ''}">
                        Next
                    </button>
                `;
            }
        } catch (error) {
            console.error('Failed to load products:', error);
            productsEmpty.classList.remove('hidden');
            productsEmpty.innerHTML = '<p class="text-lg font-medium text-red-500">Failed to load products. Please try again later.</p>';
        } finally {
            productsLoading.classList.add('hidden');
        }
    }

    // Handle add to cart from product cards
    function handleAddToCart(button) {
        const productId = parseInt(button.dataset.productId);
        const productName = button.dataset.productName;
        const productPrice = parseFloat(button.dataset.productPrice);
        const productPhoto = button.dataset.productPhoto || '/images/placeholder.png';

        if (window.addToCart) {
            window.addToCart(productId, productName, productPrice, productPhoto);
        } else {
            console.error('addToCart function not available');
        }
    }

    // Handle add to cart from product cards (global function)
    window.handleAddToCartFromCard = function(button) {
        const productId = parseInt(button.dataset.productId);
        const productName = button.dataset.productName;
        const productPrice = parseFloat(button.dataset.productPrice);
        const productPhoto = button.dataset.productPhoto || '/images/placeholder.png';

        if (typeof window.addToCart === 'function') {
            window.addToCart(productId, productName, productPrice, productPhoto);
        } else {
            console.error('addToCart function not available');
        }
    };

    function esc(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function showAlert(message, type = 'success') {
        // Create toast notification
        const toast = document.createElement('div');
        toast.className = `fixed top-20 right-4 z-50 flex items-center gap-3 rounded-lg bg-white px-4 py-3 shadow-xl ring-1 ring-gray-200 animate-in slide-in-from-right`;
        toast.innerHTML = `
            <div class="flex h-8 w-8 items-center justify-center rounded-full ${type === 'success' ? 'bg-emerald-100' : 'bg-red-100'}">
                ${type === 'success' ? `
                    <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                ` : `
                    <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                `}
            </div>
            <p class="font-medium text-gray-900">${esc(message)}</p>
        `;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Make loadProducts available globally for pagination
    window.loadProducts = loadProducts;
    window.currentPage = currentPage;

    // Update cart badge on page load
    if (typeof window.updateCartBadge === 'function') {
        window.updateCartBadge();
    }
});
</script>
@endpush
