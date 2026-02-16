@extends('layouts.admin')

@section('title', 'Subcategory Details — SyriaZone Admin')
@section('page-title', 'Subcategory Details')

@section('content')
<div class="space-y-4">
    {{-- Back Button --}}
    <div>
        <a href="{{ route('admin.subcategories.index') }}" class="btn-secondary btn-sm inline-flex items-center gap-2">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
            </svg>
            Back to Subcategories
        </a>
    </div>

    {{-- Loading --}}
    <div id="subcategory-loading" class="py-16 text-center">
        <div class="mx-auto h-8 w-8 animate-spin rounded-full border-4 border-gray-200 border-t-brand-500"></div>
        <p class="mt-3 text-sm text-gray-500">Loading subcategory details...</p>
    </div>

    {{-- Subcategory Details --}}
    <div id="subcategory-details" class="hidden space-y-4">
        {{-- Subcategory Info Card --}}
        <div class="card">
            <div class="card-body">
                <div class="flex flex-col items-center text-center">
                    <div id="subcategory-image" class="mb-6">
                        <div class="flex h-32 w-32 items-center justify-center rounded-lg bg-gray-100">
                            <svg class="h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="w-full">
                        <h2 id="subcategory-name" class="text-2xl font-bold text-gray-900"></h2>
                        <p id="subcategory-category" class="mt-2 text-sm text-gray-600"></p>
                        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <p class="text-xs font-medium text-gray-500">Created At</p>
                                <p id="subcategory-created" class="mt-1 text-sm font-semibold text-gray-900"></p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500">Updated At</p>
                                <p id="subcategory-updated" class="mt-1 text-sm font-semibold text-gray-900"></p>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-center gap-2">
                            <a id="edit-link" href="#" class="btn-primary btn-sm">Edit Subcategory</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Products Section --}}
        <div class="card">
            <div class="card-body">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Products</h3>
                        <p id="products-count" class="mt-1 text-sm text-gray-500">0 products</p>
                    </div>
                </div>

                {{-- Products Loading --}}
                <div id="products-loading" class="py-8 text-center">
                    <div class="mx-auto h-6 w-6 animate-spin rounded-full border-4 border-gray-200 border-t-brand-500"></div>
                </div>

                {{-- Products Empty --}}
                <div id="products-empty" class="hidden py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
                    </svg>
                    <h3 class="mt-3 text-sm font-semibold text-gray-900">No products</h3>
                    <p class="mt-1 text-sm text-gray-500">This subcategory doesn't have any products yet.</p>
                </div>

                {{-- Products Grid --}}
                <div id="products-grid" class="hidden grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3"></div>

                {{-- Pagination --}}
                <div id="products-pagination" class="mt-6 hidden flex items-center justify-center gap-4"></div>
            </div>
        </div>
    </div>
</div>

<x-alert type="error" id="subcategory-alert" />
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const subcategoryId = {{ $subcategoryId }};
    const loading = document.getElementById('subcategory-loading');
    const details = document.getElementById('subcategory-details');
    const alert = document.getElementById('subcategory-alert');
    let currentPage = 1;

    loadSubcategory();

    async function loadSubcategory() {
        try {
            const res = await window.axios.get(`/api/admin/subcategories/${subcategoryId}`);
            const subcategory = res.data.data;

            // Display subcategory info
            document.getElementById('subcategory-name').textContent = subcategory.name;
            document.getElementById('subcategory-category').textContent = `Category: ${subcategory.category?.name || 'N/A'}`;
            document.getElementById('subcategory-created').textContent = new Date(subcategory.created_at).toLocaleString();
            document.getElementById('subcategory-updated').textContent = new Date(subcategory.updated_at).toLocaleString();
            document.getElementById('edit-link').href = `/admin/subcategories/${subcategory.id}/edit`;

            // Display image
            const imageContainer = document.getElementById('subcategory-image');
            if (subcategory.image) {
                imageContainer.innerHTML = `<img src="/storage/${subcategory.image}" alt="${esc(subcategory.name)}" class="h-32 w-32 rounded-lg object-cover">`;
            }

            // Load products
            loadProducts();

            loading.classList.add('hidden');
            details.classList.remove('hidden');
        } catch (e) {
            console.error('Failed to load subcategory:', e);
            alert.textContent = 'Failed to load subcategory details.';
            alert.classList.remove('hidden');
            loading.classList.add('hidden');
        }
    }

    async function loadProducts() {
        const productsLoading = document.getElementById('products-loading');
        const productsEmpty = document.getElementById('products-empty');
        const productsGrid = document.getElementById('products-grid');
        const productsPagination = document.getElementById('products-pagination');
        const productsCount = document.getElementById('products-count');

        productsLoading.classList.remove('hidden');
        productsGrid.innerHTML = '';
        productsEmpty.classList.add('hidden');
        productsPagination.innerHTML = '';

        try {
            const params = new URLSearchParams({
                page: currentPage,
                subcategory_id: subcategoryId,
            });

            const response = await window.axios.get(`/api/admin/products?${params.toString()}`);
            const { data, meta } = response.data;

            productsCount.textContent = `${meta.total} product${meta.total !== 1 ? 's' : ''}`;

            if (data.length === 0) {
                productsLoading.classList.add('hidden');
                productsEmpty.classList.remove('hidden');
                productsGrid.classList.add('hidden');
            } else {
                productsLoading.classList.add('hidden');
                productsEmpty.classList.add('hidden');
                productsGrid.classList.remove('hidden');
                renderProducts(data);
                renderPagination(meta);
            }
        } catch (e) {
            console.error('Failed to load products:', e);
            productsLoading.classList.add('hidden');
            alert.textContent = 'Failed to load products.';
            alert.classList.remove('hidden');
        }
    }

    function renderProducts(products) {
        const grid = document.getElementById('products-grid');
        grid.innerHTML = products.map(product => {
            const photo = product.first_photo_url || '/images/placeholder.png';
            return `
                <div class="card">
                    <div class="card-body">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                <img src="${esc(photo)}" alt="${esc(product.name)}" class="h-20 w-20 rounded-lg object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-base font-semibold text-gray-900 line-clamp-2">${esc(product.name)}</h4>
                                <p class="mt-1 text-sm font-bold text-brand-600">${parseFloat(product.price || 0).toFixed(2)} SYP</p>
                                <p class="mt-1 text-xs text-gray-500">Qty: ${product.quantity || 0}</p>
                                <p class="mt-1 text-xs text-gray-500">Status: <span class="font-semibold ${product.status === 'approved' ? 'text-green-600' : product.status === 'rejected' ? 'text-red-600' : 'text-yellow-600'}">${product.status || 'pending'}</span></p>
                            </div>
                        </div>
                        <div class="mt-4 flex gap-2 border-t border-gray-100 pt-4">
                            <a href="/admin/products/${product.id}" class="btn-secondary btn-sm flex-1">View Product</a>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    function renderPagination(meta) {
        const pagination = document.getElementById('products-pagination');
        if (meta.last_page > 1) {
            pagination.classList.remove('hidden');
            pagination.innerHTML = `
                <button ${meta.current_page === 1 ? 'disabled' : ''}
                        onclick="currentPage = ${meta.current_page - 1}; loadProducts();"
                        class="btn-secondary btn-sm ${meta.current_page === 1 ? 'opacity-50 cursor-not-allowed' : ''}">
                    Previous
                </button>
                <span class="text-sm text-gray-600">Page ${meta.current_page} of ${meta.last_page}</span>
                <button ${meta.current_page === meta.last_page ? 'disabled' : ''}
                        onclick="currentPage = ${meta.current_page + 1}; loadProducts();"
                        class="btn-secondary btn-sm ${meta.current_page === meta.last_page ? 'opacity-50 cursor-not-allowed' : ''}">
                    Next
                </button>
            `;
        } else {
            pagination.classList.add('hidden');
        }
    }

    // Make loadProducts accessible globally for pagination
    window.loadProducts = loadProducts;

    function esc(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
});
</script>
@endpush

