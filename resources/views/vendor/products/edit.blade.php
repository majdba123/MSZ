@extends('layouts.vendor')

@section('title', 'Edit Product — SyriaZone Vendor')
@section('page-title', 'Edit Product')

@section('content')
<div class="mx-auto max-w-2xl">
    <nav class="mb-4 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('vendor.products.index') }}" class="hover:text-gray-700">Products</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        <span class="text-gray-900">Edit</span>
    </nav>

    <div id="edit-loading" class="py-16 text-center">
        <div class="mx-auto h-8 w-8 animate-spin rounded-full border-4 border-gray-200 border-t-emerald-500"></div>
        <p class="mt-3 text-sm text-gray-500">Loading product...</p>
    </div>

    <div id="edit-content" class="hidden space-y-5">
        {{-- Product Details Card --}}
        <div class="card">
            <div class="card-body border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Edit Product</h2>
                <p class="mt-0.5 text-sm text-gray-500">Update your product details.</p>
            </div>

            <div class="card-body">
                <x-alert type="error" id="edit-alert" />
                <x-alert type="success" id="edit-success" />

                <form id="edit-form" class="mt-1 space-y-6" novalidate>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <x-form.input name="name" label="Product Name" placeholder="Enter product name" :required="true" />
                        </div>
                        <x-form.input name="price" label="Price ($)" type="number" placeholder="0.00" :required="true" />
                        <x-form.input name="quantity" label="Quantity" type="number" placeholder="0" :required="true" />
                    </div>

                    <div>
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" rows="3" placeholder="Product description (optional)" class="form-textarea"></textarea>
                        <p class="form-error" id="description-error"></p>
                    </div>

                    <div class="flex items-center gap-3">
                        <label class="form-label mb-0">Active</label>
                        <label class="toggle-switch">
                            <input type="checkbox" id="is_active">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div class="flex flex-col-reverse gap-2 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end">
                        <a href="{{ route('vendor.products.index') }}" class="btn-secondary">Cancel</a>
                        <button type="submit" id="edit-btn" class="btn-primary">
                            <span id="edit-btn-text">Save Changes</span>
                            <svg id="edit-spinner" class="hidden h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Photos Card --}}
        <div class="card">
            <div class="card-body border-b border-gray-100">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Product Photos</h2>
                    <p class="mt-0.5 text-sm text-gray-500">Select photos to remove. They will be deleted when you save the product.</p>
                </div>
            </div>
            <div class="card-body">
                <x-alert type="error" id="photo-alert" />
                <x-alert type="success" id="photo-success" />

                {{-- Existing Photos --}}
                <div id="existing-photos" class="grid grid-cols-3 gap-3 sm:grid-cols-4 lg:grid-cols-5"></div>

                {{-- Upload New --}}
                <div class="mt-4 border-t border-gray-100 pt-4">
                    <label class="form-label">Add More Photos</label>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                        <input type="file" id="new-photos" multiple accept="image/jpeg,image/png,image/gif,image/webp" class="form-input flex-1 text-sm file:mr-3 file:rounded-md file:border-0 file:bg-emerald-50 file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-emerald-700 hover:file:bg-emerald-100">
                    </div>
                    <p class="mt-2 text-xs text-gray-500">New photos will be added when you save the product.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', async function () {
    const productId = '{{ $productId }}';
    const form = document.getElementById('edit-form');
    let existingPhotos = [];
    let selectedIds = new Set();

    // Load product
    try {
        const res = await window.axios.get('/api/vendor/products/' + productId);
        const p = res.data.data;

        form.name.value = p.name || '';
        form.price.value = p.price || '';
        form.quantity.value = p.quantity || 0;
        form.description.value = p.description || '';
        document.getElementById('is_active').checked = p.is_active;
        existingPhotos = p.photos || [];
        window.primaryPhotoId = p.primary_photo_id || null;

        renderExistingPhotos();
        document.getElementById('edit-loading').classList.add('hidden');
        document.getElementById('edit-content').classList.remove('hidden');
    } catch (e) {
        document.getElementById('edit-loading').innerHTML = '<p class="text-red-500">Failed to load product.</p>';
    }

    function renderExistingPhotos() {
        const container = document.getElementById('existing-photos');
        if (existingPhotos.length === 0) {
            container.innerHTML = '<p class="col-span-full text-sm text-gray-400">No photos yet.</p>';
            return;
        }
        container.innerHTML = existingPhotos.map(photo => {
            const isPrimary = photo.id === (window.primaryPhotoId || null);
            return `<div class="group relative aspect-square overflow-hidden rounded-lg border-2 transition-colors ${selectedIds.has(photo.id) ? 'border-red-400 ring-2 ring-red-200' : isPrimary ? 'border-blue-400 ring-2 ring-blue-200' : 'border-gray-200'}" id="photo-${photo.id}">
                <img src="${photo.url}" class="h-full w-full object-cover" alt="">
                ${isPrimary ? '<div class="absolute left-2 top-2 rounded bg-blue-500 px-1.5 py-0.5 text-[10px] font-semibold text-white">Primary</div>' : ''}
                <div class="absolute inset-0 flex items-center justify-center gap-2 bg-black/0 transition-all group-hover:bg-black/50 group-hover:opacity-100">
                    <button type="button" onclick="togglePhotoSelect(${photo.id})" title="Remove" class="flex h-8 w-8 items-center justify-center rounded-full bg-red-500 text-white opacity-0 shadow-lg transition-all hover:bg-red-600 group-hover:opacity-100">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                    </button>
                    <button type="button" onclick="viewPhotoLarge('${photo.url}')" title="View Large" class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-500 text-white opacity-0 shadow-lg transition-all hover:bg-blue-600 group-hover:opacity-100">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6"/></svg>
                    </button>
                    <button type="button" onclick="setPrimaryPhoto(${photo.id})" title="Set as Primary" class="flex h-8 w-8 items-center justify-center rounded-full ${isPrimary ? 'bg-green-500' : 'bg-gray-600'} text-white opacity-0 shadow-lg transition-all hover:bg-green-600 group-hover:opacity-100">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </button>
                </div>
            </div>`;
        }).join('');
    }

    window.togglePhotoSelect = function (id) {
        if (selectedIds.has(id)) { selectedIds.delete(id); } else { selectedIds.add(id); }
        renderExistingPhotos();
    };

    window.viewPhotoLarge = function (url) {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4';
        modal.innerHTML = `
            <div class="relative max-h-[90vh] max-w-[90vw]">
                <img src="${url}" class="max-h-[90vh] max-w-[90vw] rounded-lg object-contain" alt="Product photo">
                <button onclick="this.closest('.fixed').remove()" class="absolute right-2 top-2 flex h-8 w-8 items-center justify-center rounded-full bg-white/90 text-gray-900 hover:bg-white">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        `;
        modal.addEventListener('click', (e) => { if (e.target === modal) modal.remove(); });
        document.body.appendChild(modal);
    };

    window.setPrimaryPhoto = async function (photoId) {
        try {
            await window.axios.patch(`/api/vendor/products/${productId}/photos/${photoId}/set-primary`);
            window.primaryPhotoId = photoId;
            renderExistingPhotos();
            showAlert('edit-success', 'Primary photo updated successfully.');
        } catch (e) {
            showAlert('edit-alert', e.response?.data?.message || 'Failed to set primary photo.');
        }
    };


    // Update product form
    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        clearErrors();
        toggleLoading(true);

        const formData = new FormData();
        formData.append('name', form.name.value.trim());
        formData.append('price', parseFloat(form.price.value));
        formData.append('quantity', parseInt(form.quantity.value));
        const desc = form.description.value.trim();
        if (desc) formData.append('description', desc);
        formData.append('is_active', document.getElementById('is_active').checked ? '1' : '0');

        // Add photos to remove
        if (selectedIds.size > 0) {
            Array.from(selectedIds).forEach(id => {
                formData.append('photo_ids_to_remove[]', id);
            });
        }

        // Add new photos
        const newPhotosInput = document.getElementById('new-photos');
        if (newPhotosInput.files && newPhotosInput.files.length > 0) {
            Array.from(newPhotosInput.files).forEach(f => {
                formData.append('photos[]', f);
            });
        }

        try {
            await window.axios.post('/api/vendor/products/' + productId, formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
                params: { _method: 'PUT' },
            });
            showAlert('edit-success', 'Product updated! Redirecting...');
            setTimeout(() => { window.location.href = '{{ route("vendor.products.index") }}'; }, 800);
        } catch (error) {
            handleErrors(error);
        } finally {
            toggleLoading(false);
        }
    });

    function toggleLoading(l) {
        document.getElementById('edit-btn').disabled = l;
        document.getElementById('edit-spinner').classList.toggle('hidden', !l);
        document.getElementById('edit-btn-text').textContent = l ? 'Saving...' : 'Save Changes';
    }
    function clearErrors() {
        document.getElementById('edit-alert').classList.add('hidden');
        document.getElementById('edit-success').classList.add('hidden');
        document.querySelectorAll('[id$="-error"]').forEach(el => { el.classList.add('hidden'); el.textContent = ''; });
    }
    function showAlert(id, msg) {
        document.getElementById(id + '-message').textContent = msg;
        document.getElementById(id).classList.remove('hidden');
    }
    function handleErrors(error) {
        console.error('Product update error:', error);
        console.error('Error response:', error.response?.data);

        if (error.response?.status === 422) {
            const errors = error.response.data.errors || {};
            console.error('Validation errors:', errors);

            // Show all validation errors
            for (const [f, m] of Object.entries(errors)) {
                const fieldName = f.replace(/\./g, '\\.');
                const el = document.getElementById(f + '-error') || document.getElementById(fieldName + '-error');
                if (el) {
                    el.textContent = Array.isArray(m) ? m[0] : m;
                    el.classList.remove('hidden');
                } else {
                    console.warn('Error element not found for field:', f);
                }
            }

            // Also show a general alert with all errors
            const errorMessages = Object.values(errors).flat().join(', ');
            showAlert('edit-alert', 'Validation failed: ' + errorMessages);
        } else {
            const errorMsg = error.response?.data?.message || error.message || 'An unexpected error occurred.';
            showAlert('edit-alert', errorMsg);
        }
    }
});
</script>
@endpush
