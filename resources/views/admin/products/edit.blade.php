s@extends('layouts.admin')

@section('title', 'Edit Product — SyriaZone Admin')
@section('page-title', 'Edit Product')

@section('content')
<div class="mx-auto max-w-2xl">
    <nav class="mb-4 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.products.index') }}" class="hover:text-gray-700">Products</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        <span class="text-gray-900">Edit</span>
    </nav>

    <div id="edit-loading" class="py-16 text-center">
        <div class="mx-auto h-8 w-8 animate-spin rounded-full border-4 border-gray-200 border-t-brand-500"></div>
        <p class="mt-3 text-sm text-gray-500">Loading product...</p>
    </div>

    <div id="edit-content" class="hidden space-y-5">
        {{-- Product Details Card --}}
        <div class="card">
            <div class="card-body border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Edit Product</h2>
                <p class="mt-0.5 text-sm text-gray-500">Update product details. Vendor cannot be changed after creation.</p>
            </div>

            <div class="card-body">
                <x-alert type="error" id="edit-alert" />
                <x-alert type="success" id="edit-success" />

                {{-- Vendor info (read only) --}}
                <div class="mb-5 rounded-lg bg-gray-50 px-4 py-3">
                    <p class="text-xs font-medium uppercase text-gray-500">Vendor</p>
                    <p id="vendor-info" class="mt-0.5 text-sm font-semibold text-gray-900">—</p>
                </div>

                <form id="edit-form" class="space-y-6" novalidate>
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
                        <a href="{{ route('admin.products.index') }}" class="btn-secondary">Cancel</a>
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
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Product Photos</h2>
                        <p class="mt-0.5 text-sm text-gray-500">Select photos to remove. They will be deleted when you save the product.</p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <x-alert type="error" id="photo-alert" />
                <x-alert type="success" id="photo-success" />

                <div id="existing-photos" class="grid grid-cols-3 gap-3 sm:grid-cols-4 lg:grid-cols-5"></div>

                <div class="mt-4 border-t border-gray-100 pt-4">
                    <label class="form-label">Add More Photos</label>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                        <input type="file" id="new-photos" multiple accept="image/jpeg,image/png,image/gif,image/webp" class="form-input flex-1 text-sm file:mr-3 file:rounded-md file:border-0 file:bg-brand-50 file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-brand-700 hover:file:bg-brand-100">
                    </div>
                    <p class="mt-2 text-xs text-gray-500">Selected photos will be removed when you save the product.</p>
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

    // Define helper functions first
    function showAlert(id, msg) {
        const el = document.getElementById(id);
        if (el) {
            const msgEl = document.getElementById(id + '-message');
            if (msgEl) msgEl.textContent = msg;
            el.classList.remove('hidden');
            setTimeout(() => el.classList.add('hidden'), 5000);
        }
    }

    function renderExistingPhotos() {
        const container = document.getElementById('existing-photos');
        if (existingPhotos.length === 0) {
            container.innerHTML = '<p class="col-span-full text-sm text-gray-400">No photos yet.</p>';
            return;
        }
        container.innerHTML = existingPhotos.map(photo => {
            const isPrimary = photo.id === (window.primaryPhotoId || null);
            const isSelected = selectedIds.has(photo.id);
            const photoUrl = photo.url.replace(/"/g, '&quot;');
            return `<div class="group relative aspect-square overflow-hidden rounded-lg border-2 transition-colors ${isSelected ? 'border-red-500 ring-4 ring-red-200' : isPrimary ? 'border-blue-400 ring-2 ring-blue-200' : 'border-gray-200'}" id="photo-${photo.id}" data-photo-id="${photo.id}" data-photo-url="${photoUrl}">
                <img src="${photoUrl}" class="h-full w-full object-cover ${isSelected ? 'opacity-50' : ''}" alt="">
                ${isPrimary ? '<div class="absolute left-2 top-2 rounded bg-blue-500 px-1.5 py-0.5 text-[10px] font-semibold text-white z-10">Primary</div>' : ''}
                ${isSelected ? '<div class="absolute inset-0 flex items-center justify-center bg-red-500/20 z-10 pointer-events-none"><span class="rounded bg-red-500 px-3 py-1.5 text-xs font-semibold text-white shadow-lg">Marked for Removal</span></div>' : ''}
                <div class="absolute bottom-2 left-2 right-2 z-20 flex items-center justify-center gap-2 opacity-90 transition-opacity group-hover:opacity-100">
                    <button type="button" data-action="remove" data-photo-id="${photo.id}" title="${isSelected ? 'Cancel Removal' : 'Remove Photo'}" class="flex h-9 items-center justify-center gap-1.5 rounded-lg ${isSelected ? 'bg-white text-red-600' : 'bg-red-500 text-white'} px-3 py-1.5 text-xs font-semibold shadow-lg transition-all hover:scale-105">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                        ${isSelected ? 'Cancel' : 'Remove'}
                    </button>
                    <button type="button" data-action="view" data-photo-url="${photoUrl}" title="View Large" class="flex h-9 items-center justify-center gap-1.5 rounded-lg bg-blue-500 px-3 py-1.5 text-xs font-semibold text-white shadow-lg transition-all hover:bg-blue-600 hover:scale-105">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6"/></svg>
                        View
                    </button>
                    <button type="button" data-action="primary" data-photo-id="${photo.id}" title="Set as Primary" class="flex h-9 items-center justify-center gap-1.5 rounded-lg ${isPrimary ? 'bg-green-500' : 'bg-gray-600'} px-3 py-1.5 text-xs font-semibold text-white shadow-lg transition-all hover:bg-green-600 hover:scale-105">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        ${isPrimary ? 'Primary' : 'Set Primary'}
                    </button>
                </div>
            </div>`;
        }).join('');
    }

    // Define photo action functions
    window.togglePhotoSelect = function (id) {
        const photoId = parseInt(id);
        if (selectedIds.has(photoId)) {
            selectedIds.delete(photoId);
        } else {
            selectedIds.add(photoId);
        }
        renderExistingPhotos();
    };

    window.viewPhotoLarge = function (url) {
        // Remove existing modal if any
        const existingModal = document.getElementById('photo-modal');
        if (existingModal) existingModal.remove();

        const modal = document.createElement('div');
        modal.id = 'photo-modal';
        modal.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4';
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
        // Close on Escape key
        const escapeHandler = (e) => { if (e.key === 'Escape') { closeModal(); document.removeEventListener('keydown', escapeHandler); } };
        document.addEventListener('keydown', escapeHandler);
        document.body.appendChild(modal);
    };

    window.setPrimaryPhoto = async function (photoId) {
        try {
            const res = await window.axios.patch(`/api/admin/products/${productId}/photos/${photoId}/set-primary`);
            // Update primary photo ID from response
            window.primaryPhotoId = res.data.data.primary_photo_id || photoId;
            // Reload product to get updated data
            const productRes = await window.axios.get(`/api/admin/products/${productId}`);
            const product = productRes.data.data;
            existingPhotos = product.photos || [];
            window.primaryPhotoId = product.primary_photo_id || null;
            renderExistingPhotos();
            showAlert('edit-success', 'Primary photo updated successfully.');
        } catch (e) {
            console.error('Failed to set primary photo:', e);
            showAlert('edit-alert', e.response?.data?.message || 'Failed to set primary photo.');
        }
    };

    // Add event delegation for photo actions (only once, before loading)
    const container = document.getElementById('existing-photos');
    if (container && !container.hasAttribute('data-listener-added')) {
        container.setAttribute('data-listener-added', 'true');
        container.addEventListener('click', function(e) {
            const button = e.target.closest('button[data-action]');
            if (!button) return;

            const action = button.getAttribute('data-action');
            const photoId = button.getAttribute('data-photo-id');
            const photoUrl = button.getAttribute('data-photo-url');

            if (action === 'remove' && photoId) {
                window.togglePhotoSelect(parseInt(photoId));
            } else if (action === 'view' && photoUrl) {
                window.viewPhotoLarge(photoUrl);
            } else if (action === 'primary' && photoId) {
                window.setPrimaryPhoto(parseInt(photoId));
            }
        });
    }

    // Load product
    try {
        const res = await window.axios.get('/api/admin/products/' + productId);
        const p = res.data.data;

        form.name.value = p.name || '';
        form.price.value = p.price || '';
        form.quantity.value = p.quantity || 0;
        form.description.value = p.description || '';
        document.getElementById('is_active').checked = p.is_active;
        existingPhotos = p.photos || [];
        window.primaryPhotoId = p.primary_photo_id || null;

        // Show vendor info
        const vendorName = p.vendor?.store_name || '—';
        const ownerName = p.vendor?.user?.name || '';
        document.getElementById('vendor-info').textContent = vendorName + (ownerName ? ' — ' + ownerName : '');

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
            const isSelected = selectedIds.has(photo.id);
            const photoUrl = photo.url.replace(/"/g, '&quot;');
            return `<div class="group relative aspect-square overflow-hidden rounded-lg border-2 transition-colors ${isSelected ? 'border-red-500 ring-4 ring-red-200' : isPrimary ? 'border-blue-400 ring-2 ring-blue-200' : 'border-gray-200'}" id="photo-${photo.id}" data-photo-id="${photo.id}" data-photo-url="${photoUrl}">
                <img src="${photoUrl}" class="h-full w-full object-cover ${isSelected ? 'opacity-50' : ''}" alt="">
                ${isPrimary ? '<div class="absolute left-2 top-2 rounded bg-blue-500 px-1.5 py-0.5 text-[10px] font-semibold text-white z-10">Primary</div>' : ''}
                ${isSelected ? '<div class="absolute inset-0 flex items-center justify-center bg-red-500/20 z-10 pointer-events-none"><span class="rounded bg-red-500 px-3 py-1.5 text-xs font-semibold text-white shadow-lg">Marked for Removal</span></div>' : ''}
                <div class="absolute bottom-2 left-2 right-2 z-20 flex items-center justify-center gap-2 opacity-90 transition-opacity group-hover:opacity-100">
                    <button type="button" data-action="remove" data-photo-id="${photo.id}" title="${isSelected ? 'Cancel Removal' : 'Remove Photo'}" class="flex h-9 items-center justify-center gap-1.5 rounded-lg ${isSelected ? 'bg-white text-red-600' : 'bg-red-500 text-white'} px-3 py-1.5 text-xs font-semibold shadow-lg transition-all hover:scale-105">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                        ${isSelected ? 'Cancel' : 'Remove'}
                    </button>
                    <button type="button" data-action="view" data-photo-url="${photoUrl}" title="View Large" class="flex h-9 items-center justify-center gap-1.5 rounded-lg bg-blue-500 px-3 py-1.5 text-xs font-semibold text-white shadow-lg transition-all hover:bg-blue-600 hover:scale-105">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6"/></svg>
                        View
                    </button>
                    <button type="button" data-action="primary" data-photo-id="${photo.id}" title="Set as Primary" class="flex h-9 items-center justify-center gap-1.5 rounded-lg ${isPrimary ? 'bg-green-500' : 'bg-gray-600'} px-3 py-1.5 text-xs font-semibold text-white shadow-lg transition-all hover:bg-green-600 hover:scale-105">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        ${isPrimary ? 'Primary' : 'Set Primary'}
                    </button>
                </div>
            </div>`;
        }).join('');
    }



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
            await window.axios.post('/api/admin/products/' + productId, formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
                params: { _method: 'PUT' },
            });
            showAlert('edit-success', 'Product updated! Redirecting...');
            setTimeout(() => { window.location.href = '{{ route("admin.products.index") }}'; }, 800);
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
