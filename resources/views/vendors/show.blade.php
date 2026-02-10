@extends('layouts.app')

@section('title', 'Vendor Details — SyriaZone')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <nav class="mb-6 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-gray-700">Home</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        <span class="text-gray-900">Vendor</span>
    </nav>

    <div id="vendor-loading" class="flex justify-center py-12">
        <div class="h-10 w-10 animate-spin rounded-full border-4 border-gray-200 border-t-brand-500"></div>
    </div>

    <div id="vendor-content" class="hidden">
        {{-- Vendor Info Card --}}
        <div class="mb-8 overflow-hidden rounded-2xl bg-white shadow-lg ring-1 ring-gray-200">
            <div class="bg-gradient-to-r from-brand-600 to-brand-700 px-8 py-12">
                <div class="flex flex-col items-center gap-6 sm:flex-row sm:items-start">
                    <div class="flex-shrink-0">
                        <div id="vendor-logo" class="flex h-24 w-24 items-center justify-center overflow-hidden rounded-2xl bg-white ring-4 ring-white/20 shadow-xl"></div>
                    </div>
                    <div class="flex-1 text-center sm:text-left">
                        <h1 id="vendor-name" class="text-3xl font-bold text-white"></h1>
                        <p id="vendor-description" class="mt-2 text-brand-100"></p>
                        <p id="vendor-address" class="mt-3 flex items-center justify-center gap-2 text-sm text-white/90 sm:justify-start">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span id="vendor-address-text"></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Products Section --}}
        <div>
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Products</h2>
                    <p class="mt-1 text-sm text-gray-500" id="products-count">Loading...</p>
                </div>
            </div>

            <div id="vendor-products-loading" class="flex justify-center py-12">
                <div class="h-10 w-10 animate-spin rounded-full border-4 border-gray-200 border-t-brand-500"></div>
            </div>

            <div id="vendor-products-grid" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"></div>

            <div id="vendor-products-empty" class="hidden text-center py-12">
                <p class="text-lg font-medium text-gray-500">No products available from this vendor.</p>
            </div>

            <div id="vendor-products-pagination" class="mt-8 flex items-center justify-center gap-4"></div>
        </div>
    </div>

    <div id="vendor-error" class="hidden text-center py-12">
        <p class="text-lg font-medium text-red-500">Vendor not found.</p>
        <a href="{{ route('home') }}" class="mt-4 inline-block text-brand-600 hover:text-brand-700">Back to Home</a>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', async function () {
    const vendorId = {{ $vendorId ?? 'null' }};
    const vendorLoading = document.getElementById('vendor-loading');
    const vendorContent = document.getElementById('vendor-content');
    const vendorError = document.getElementById('vendor-error');

    if (!vendorId) {
        vendorLoading.classList.add('hidden');
        vendorError.classList.remove('hidden');
        return;
    }

    let currentPage = 1;

    try {
        const response = await window.axios.get(`/api/vendors/${vendorId}`);
        const vendor = response.data.data;

        // Set vendor info
        document.getElementById('vendor-name').textContent = vendor.store_name;
        document.getElementById('vendor-description').textContent = vendor.description || 'Quality products from trusted vendor';
        if (vendor.address) {
            document.getElementById('vendor-address-text').textContent = vendor.address;
        } else {
            document.getElementById('vendor-address').classList.add('hidden');
        }

        // Set logo
        const logoContainer = document.getElementById('vendor-logo');
        if (vendor.logo) {
            logoContainer.innerHTML = `<img src="${esc(vendor.logo)}" alt="${esc(vendor.store_name)}" class="h-full w-full object-cover">`;
        } else {
            logoContainer.innerHTML = `<span class="text-3xl font-bold text-brand-600">${esc(vendor.store_name).charAt(0).toUpperCase()}</span>`;
        }

        vendorLoading.classList.add('hidden');
        vendorContent.classList.remove('hidden');

        // Load products
        loadVendorProducts();

        async function loadVendorProducts() {
            const productsLoading = document.getElementById('vendor-products-loading');
            const productsGrid = document.getElementById('vendor-products-grid');
            const productsEmpty = document.getElementById('vendor-products-empty');
            const productsPagination = document.getElementById('vendor-products-pagination');
            const productsCount = document.getElementById('products-count');

            productsLoading.classList.remove('hidden');
            productsGrid.innerHTML = '';
            productsEmpty.classList.add('hidden');
            productsPagination.innerHTML = '';

            try {
                const params = new URLSearchParams({
                    page: currentPage,
                    vendor_id: vendorId,
                });

                const response = await window.axios.get(`/api/products?${params.toString()}`);
                const { data, meta } = response.data;

                productsCount.textContent = `${meta.total} product${meta.total !== 1 ? 's' : ''} available`;

                if (data.length === 0) {
                    productsEmpty.classList.remove('hidden');
                } else {
                    productsGrid.innerHTML = data.map(product => `
                        <div class="group relative overflow-hidden rounded-xl bg-white shadow-md ring-1 ring-gray-200 transition-all duration-300 hover:shadow-xl hover:ring-brand-500/50">
                            <div class="relative overflow-hidden">
                                <img src="${esc(product.first_photo_url || '/images/placeholder.png')}"
                                     alt="${esc(product.name)}"
                                     class="h-56 w-full object-cover transition-transform duration-300 group-hover:scale-110"
                                     loading="lazy">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                                ${product.quantity <= 0 ? `<div class="absolute top-3 right-3 rounded-full bg-red-500 px-3 py-1.5 text-xs font-semibold text-white shadow-lg">Out of Stock</div>` : ''}
                            </div>
                            <div class="p-5">
                                <h3 class="mb-2 text-lg font-bold text-gray-900 line-clamp-2 transition-colors group-hover:text-brand-600">${esc(product.name)}</h3>
                                <p class="mb-4 text-sm text-gray-600 line-clamp-2">${esc(product.description || 'No description available')}</p>
                                <div class="mb-4 flex items-center justify-between border-t border-gray-100 pt-4">
                                    <div>
                                        <span class="text-2xl font-bold text-brand-600">${parseFloat(product.price).toFixed(2)}</span>
                                        <span class="text-sm text-gray-500"> SYP</span>
                                    </div>
                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">${product.quantity} available</span>
                                </div>
                                <div class="flex gap-2">
                                    <a href="/products/${product.id}" class="flex-1 btn-secondary text-center text-sm font-medium transition-all hover:bg-gray-100">View</a>
                                    <button onclick="(function(){const photo='${esc(product.first_photo_url || '')}';window.addToCart(${product.id},${JSON.stringify(product.name)},${product.price},photo);})()"
                                            class="flex-1 btn-primary text-sm font-medium shadow-sm transition-all hover:shadow-md ${product.quantity <= 0 ? 'opacity-50 cursor-not-allowed' : ''}"
                                            ${product.quantity <= 0 ? 'disabled' : ''}>
                                        <span class="flex items-center justify-center gap-1.5">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            Add to Cart
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `).join('');
                }

                // Pagination
                if (meta.last_page > 1) {
                    productsPagination.innerHTML = `
                        <button ${meta.current_page === 1 ? 'disabled' : ''}
                                onclick="currentPage = ${meta.current_page - 1}; loadVendorProducts();"
                                class="rounded-lg border border-gray-300 px-4 py-2 text-sm transition-colors ${meta.current_page === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'}">
                            Previous
                        </button>
                        <span class="text-sm text-gray-600">Page ${meta.current_page} of ${meta.last_page}</span>
                        <button ${meta.current_page === meta.last_page ? 'disabled' : ''}
                                onclick="currentPage = ${meta.current_page + 1}; loadVendorProducts();"
                                class="rounded-lg border border-gray-300 px-4 py-2 text-sm transition-colors ${meta.current_page === meta.last_page ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'}">
                            Next
                        </button>
                    `;
                }
            } catch (error) {
                console.error('Failed to load products:', error);
                productsEmpty.classList.remove('hidden');
            } finally {
                productsLoading.classList.add('hidden');
            }
        }

        window.loadVendorProducts = loadVendorProducts;
        window.currentPage = currentPage;
    } catch (error) {
        console.error('Failed to load vendor:', error);
        vendorLoading.classList.add('hidden');
        vendorError.classList.remove('hidden');
    }

    function esc(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
});
</script>
@endpush

