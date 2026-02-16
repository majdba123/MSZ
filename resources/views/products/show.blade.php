@extends('layouts.app')

@section('title', 'Product Details — SyriaZone')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <nav class="mb-6 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-gray-700 transition-colors">Home</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        <span class="text-gray-900 font-medium">Product Details</span>
    </nav>

    <div id="show-loading" class="py-16 text-center">
        <div class="mx-auto h-12 w-12 animate-spin rounded-full border-4 border-gray-200 border-t-brand-500"></div>
        <p class="mt-4 text-sm font-medium text-gray-500">Loading product details...</p>
    </div>

    <div id="show-content" class="hidden">
        <div class="grid gap-8 lg:grid-cols-3">
            {{-- Left Column: Images --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Primary Photo --}}
                <div class="overflow-hidden rounded-2xl bg-white shadow-xl ring-1 ring-gray-200/50">
                    <div id="primary-photo-container" class="relative aspect-square bg-gradient-to-br from-gray-50 to-gray-100">
                        <p class="absolute inset-0 flex items-center justify-center text-gray-400">No primary photo available.</p>
                    </div>
                </div>

                {{-- Scrollable Photo Gallery --}}
                <div class="rounded-2xl bg-white p-6 shadow-xl ring-1 ring-gray-200/50">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900">Product Gallery</h3>
                        <span id="photo-count" class="text-sm text-gray-500"></span>
                    </div>
                    <div id="product-photos" class="flex gap-3 overflow-x-auto pb-2 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100" style="scrollbar-width: thin;">
                        <!-- Photos will be inserted here -->
                    </div>
                </div>
            </div>

            {{-- Right Column: Product Info --}}
            <div class="lg:col-span-1">
                <div class="sticky top-6 space-y-6">
                    {{-- Product Title & Vendor --}}
                    <div class="rounded-2xl bg-white p-6 shadow-xl ring-1 ring-gray-200/50">
                        <h1 id="product-name" class="mb-3 text-3xl font-bold text-gray-900 leading-tight"></h1>
                        <div class="mb-6 flex items-center gap-2 border-b border-gray-100 pb-6">
                            <span class="text-sm text-gray-500">Sold by</span>
                            <a id="vendor-link" href="#" class="text-sm font-semibold text-brand-600 hover:text-brand-700 transition-colors"></a>
                        </div>
                        <div class="mb-6 flex items-baseline gap-2 border-b border-gray-100 pb-6">
                            <span id="product-price" class="text-4xl font-bold text-brand-600"></span>
                            <span class="text-xl text-gray-500">SYP</span>
                        </div>
                        <div class="mb-6 space-y-4">
                            <div>
                                <p id="product-availability" class="mb-2"></p>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium text-gray-700">Available Quantity:</span>
                                    <span id="product-quantity" class="text-lg font-bold text-gray-900"></span>
                                </div>
                            </div>
                        </div>
                        <button id="add-to-cart-btn" class="w-full rounded-xl bg-gradient-to-r from-brand-600 to-brand-700 py-4 text-base font-bold text-white shadow-lg transition-all hover:from-brand-700 hover:to-brand-800 hover:shadow-xl active:scale-[0.98]" disabled>
                            <span class="flex items-center justify-center gap-2">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Add to Cart
                            </span>
                        </button>
                    </div>

                    {{-- Description --}}
                    <div class="rounded-2xl bg-white p-6 shadow-xl ring-1 ring-gray-200/50">
                        <h3 class="mb-4 text-lg font-bold text-gray-900">Description</h3>
                        <p id="product-description" class="text-sm leading-relaxed text-gray-700 whitespace-pre-wrap"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="product-error" class="hidden text-center py-12">
        <div class="mx-auto max-w-md">
            <svg class="mx-auto h-16 w-16 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <p class="mt-4 text-lg font-medium text-gray-900">Product not found</p>
            <p class="mt-2 text-sm text-gray-500">The product you're looking for doesn't exist or has been removed.</p>
            <a href="{{ route('home') }}" class="mt-6 inline-block rounded-lg bg-brand-600 px-6 py-3 text-sm font-semibold text-white transition-all hover:bg-brand-700">Back to Products</a>
        </div>
    </div>
</div>

<style>
.scrollbar-thin::-webkit-scrollbar {
    height: 8px;
}
.scrollbar-thin::-webkit-scrollbar-track {
    background: #f3f4f6;
    border-radius: 4px;
}
.scrollbar-thin::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 4px;
}
.scrollbar-thin::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', async function () {
    const productId = {{ $productId ?? 'null' }};
    const showLoading = document.getElementById('show-loading');
    const showContent = document.getElementById('show-content');
    const productError = document.getElementById('product-error');

    if (!productId) {
        showLoading.classList.add('hidden');
        productError.classList.remove('hidden');
        return;
    }

    let currentPhotoPage = 1;
    const photosPerPage = 6;

    try {
        const response = await window.axios.get(`/api/products/${productId}`);
        const p = response.data.data;

        const photos = p.photos || [];

        document.getElementById('product-name').textContent = p.name || '—';
        document.getElementById('product-price').textContent = parseFloat(p.price || 0).toFixed(2);
        document.getElementById('product-quantity').textContent = (p.quantity || 0) + ' units';
        document.getElementById('product-description').textContent = p.description || 'No description provided.';

        // Vendor info
        if (p.vendor) {
            const vendorLink = document.getElementById('vendor-link');
            vendorLink.textContent = p.vendor.store_name || '—';
            vendorLink.href = `/vendors/${p.vendor.id}`;
        }

        // Availability
        const availabilityBadge = p.quantity > 0
            ? '<span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1.5 text-sm font-semibold text-emerald-700"><span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>In Stock</span>'
            : '<span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-3 py-1.5 text-sm font-semibold text-red-700"><span class="h-2 w-2 rounded-full bg-red-500"></span>Out of Stock</span>';
        document.getElementById('product-availability').innerHTML = availabilityBadge;

        // Add to cart button
        const addToCartBtn = document.getElementById('add-to-cart-btn');
        if (p.quantity > 0) {
            addToCartBtn.disabled = false;
            addToCartBtn.onclick = () => {
                const primaryPhoto = photos.find(photo => photo.is_primary === true) || photos[0];
                const photoUrl = primaryPhoto ? (primaryPhoto.url || `/storage/${primaryPhoto.path}`) : '';

                if (typeof window.addToCart === 'function') {
                    window.addToCart(p.id, p.name, p.price, photoUrl);
                } else {
                    console.error('addToCart function not available');
                    alert('Product added to cart!');
                }
            };
        } else {
            addToCartBtn.innerHTML = '<span>Out of Stock</span>';
            addToCartBtn.disabled = true;
            addToCartBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }

        // Display primary photo
        const primaryPhoto = photos.find(photo => photo.is_primary === true) || photos[0];
        const primaryPhotoContainer = document.getElementById('primary-photo-container');
        if (primaryPhoto) {
            const photoUrl = primaryPhoto.url || `/storage/${primaryPhoto.path}`;
            primaryPhotoContainer.innerHTML = `
                <img src="${photoUrl}"
                     alt="${esc(p.name)}"
                     class="h-full w-full object-cover transition-transform duration-500 hover:scale-110 cursor-zoom-in"
                     onclick="viewPhotoLarge('${photoUrl}')"
                     loading="eager">
            `;
        }

        // Display photos with pagination
        function renderPhotoGallery() {
            const photosContainer = document.getElementById('product-photos');
            const photoCount = document.getElementById('photo-count');
            const totalPages = Math.ceil(photos.length / photosPerPage);
            const startIndex = (currentPhotoPage - 1) * photosPerPage;
            const endIndex = startIndex + photosPerPage;
            const currentPhotos = photos.slice(startIndex, endIndex);

            photoCount.textContent = `${photos.length} photo${photos.length !== 1 ? 's' : ''}`;

            if (photos.length === 0) {
                photosContainer.innerHTML = '<p class="text-center text-sm text-gray-400 py-4">No photos available.</p>';
            } else {
                photosContainer.innerHTML = currentPhotos.map((photo, index) => {
                    const photoUrl = photo.url || `/storage/${photo.path}`;
                    const isPrimary = photo.is_primary === true;
                    return `
                        <button type="button"
                                onclick="setPrimaryImage('${photoUrl}', '${photo.url || `/storage/${photo.path}`}')"
                                class="group relative flex-shrink-0 aspect-square h-24 w-24 overflow-hidden rounded-xl border-2 transition-all ${isPrimary ? 'border-brand-500 ring-2 ring-brand-200 shadow-md' : 'border-gray-200 hover:border-brand-300'}">
                            <img src="${photoUrl}"
                                 alt="${esc(p.name)}"
                                 class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-110"
                                 loading="lazy">
                            ${isPrimary ? '<div class="absolute top-1 right-1 h-3 w-3 rounded-full bg-brand-500 ring-2 ring-white"></div>' : ''}
                        </button>
                    `;
                }).join('');

                // Add pagination if needed
                if (totalPages > 1) {
                    const pagination = document.createElement('div');
                    pagination.className = 'mt-4 flex items-center justify-center gap-2';
                    pagination.innerHTML = `
                        <button ${currentPhotoPage === 1 ? 'disabled' : ''}
                                onclick="currentPhotoPage = ${currentPhotoPage - 1}; renderPhotoGallery();"
                                class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm font-medium transition-colors ${currentPhotoPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'}"
                                ${currentPhotoPage === 1 ? 'disabled' : ''}>
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 19.5L8.25 12l7.5-7.5" />
                            </svg>
                        </button>
                        <span class="text-sm text-gray-600">Page ${currentPhotoPage} of ${totalPages}</span>
                        <button ${currentPhotoPage === totalPages ? 'disabled' : ''}
                                onclick="currentPhotoPage = ${currentPhotoPage + 1}; renderPhotoGallery();"
                                class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm font-medium transition-colors ${currentPhotoPage === totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'}"
                                ${currentPhotoPage === totalPages ? 'disabled' : ''}>
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                        </button>
                    `;
                    photosContainer.parentElement.appendChild(pagination);
                }
            }
        }

        window.setPrimaryImage = function (url, fullUrl) {
            const primaryPhotoContainer = document.getElementById('primary-photo-container');
            primaryPhotoContainer.innerHTML = `
                <img src="${fullUrl || url}"
                     alt="${esc(p.name)}"
                     class="h-full w-full object-cover transition-transform duration-500 hover:scale-110 cursor-zoom-in"
                     onclick="viewPhotoLarge('${fullUrl || url}')"
                     loading="eager">
            `;
            renderPhotoGallery();
        };

        renderPhotoGallery();

        window.viewPhotoLarge = function (url) {
            const existingModal = document.getElementById('photo-modal');
            if (existingModal) existingModal.remove();

            const modal = document.createElement('div');
            modal.id = 'photo-modal';
            modal.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black/90 backdrop-blur-sm p-4';
            modal.innerHTML = `
                <div class="relative max-h-[90vh] max-w-[90vw]">
                    <img src="${url}" class="max-h-[90vh] max-w-[90vw] rounded-lg object-contain shadow-2xl" alt="Product photo">
                    <button type="button" onclick="document.getElementById('photo-modal')?.remove()" class="absolute right-2 top-2 z-10 flex h-10 w-10 items-center justify-center rounded-full bg-white/95 text-gray-900 shadow-lg backdrop-blur-sm transition-all hover:bg-white hover:scale-110" title="Close">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            `;
            const closeModal = () => modal.remove();
            modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
            const escapeHandler = (e) => { if (e.key === 'Escape') { closeModal(); document.removeEventListener('keydown', escapeHandler); } };
            document.addEventListener('keydown', escapeHandler);
            document.body.appendChild(modal);
        };

        window.currentPhotoPage = currentPhotoPage;
        window.renderPhotoGallery = renderPhotoGallery;

        document.getElementById('show-loading').classList.add('hidden');
        document.getElementById('show-content').classList.remove('hidden');
    } catch (error) {
        console.error('Failed to load product:', error);
        showLoading.classList.add('hidden');
        productError.classList.remove('hidden');
    }

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
            window.dispatchEvent(new CustomEvent('cartUpdated'));

            if (typeof window.updateCartBadge === 'function') window.updateCartBadge(true);

            showAlert('Product added to cart!', 'success');
        } catch (e) {
            console.error('Error adding to cart:', e);
            showAlert('Failed to add product to cart', 'error');
        }
    };

    function showAlert(message, type = 'success') {
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

    function esc(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
});
</script>
@endpush
