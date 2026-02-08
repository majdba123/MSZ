@extends('layouts.admin')

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
                        <p class="mt-0.5 text-sm text-gray-500">Manage product images.</p>
                    </div>
                    <button id="remove-selected-btn" class="btn-danger btn-xs hidden" onclick="removeSelectedPhotos()">
                        Remove Selected (<span id="selected-count">0</span>)
                    </button>
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
                        <button type="button" onclick="uploadNewPhotos()" id="upload-btn" class="btn-primary btn-sm shrink-0">
                            <span id="upload-btn-text">Upload</span>
                            <svg id="upload-spinner" class="hidden h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        </button>
                    </div>
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

    try {
        const res = await window.axios.get('/api/admin/products/' + productId);
        const p = res.data.data;

        form.name.value = p.name || '';
        form.price.value = p.price || '';
        form.quantity.value = p.quantity || 0;
        form.description.value = p.description || '';
        document.getElementById('is_active').checked = p.is_active;
        existingPhotos = p.photos || [];

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
        container.innerHTML = existingPhotos.map(photo => `
            <div class="group relative aspect-square overflow-hidden rounded-lg border-2 transition-colors ${selectedIds.has(photo.id) ? 'border-red-400 ring-2 ring-red-200' : 'border-gray-200'}">
                <img src="${photo.url}" class="h-full w-full object-cover" alt="">
                <div class="absolute inset-0 flex items-center justify-center bg-black/0 transition-colors group-hover:bg-black/30">
                    <button type="button" onclick="togglePhotoSelect(${photo.id})" class="flex h-7 w-7 items-center justify-center rounded-full transition-all ${selectedIds.has(photo.id) ? 'bg-red-500 text-white' : 'bg-white/90 text-gray-600 opacity-0 group-hover:opacity-100'}">
                        ${selectedIds.has(photo.id) ? '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>' : '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>'}
                    </button>
                </div>
            </div>
        `).join('');
        updateRemoveBtn();
    }

    window.togglePhotoSelect = function (id) {
        if (selectedIds.has(id)) { selectedIds.delete(id); } else { selectedIds.add(id); }
        renderExistingPhotos();
    };

    function updateRemoveBtn() {
        const btn = document.getElementById('remove-selected-btn');
        if (selectedIds.size > 0) {
            btn.classList.remove('hidden');
            document.getElementById('selected-count').textContent = selectedIds.size;
        } else {
            btn.classList.add('hidden');
        }
    }

    window.removeSelectedPhotos = async function () {
        if (selectedIds.size === 0) return;
        if (!confirm(`Remove ${selectedIds.size} photo(s)?`)) return;

        try {
            await window.axios.delete('/api/admin/products/' + productId + '/photos', {
                data: { photo_ids: Array.from(selectedIds) },
                headers: { 'Content-Type': 'application/json' },
            });
            existingPhotos = existingPhotos.filter(p => !selectedIds.has(p.id));
            selectedIds.clear();
            renderExistingPhotos();
            showPhotoAlert('photo-success', 'Photos removed successfully.');
        } catch (e) {
            showPhotoAlert('photo-alert', e.response?.data?.message || 'Failed to remove photos.');
        }
    };

    window.uploadNewPhotos = async function () {
        const input = document.getElementById('new-photos');
        if (!input.files || input.files.length === 0) return;

        document.getElementById('upload-btn').disabled = true;
        document.getElementById('upload-spinner').classList.remove('hidden');
        document.getElementById('upload-btn-text').textContent = 'Uploading...';

        const formData = new FormData();
        Array.from(input.files).forEach(f => formData.append('photos[]', f));

        try {
            const res = await window.axios.post('/api/admin/products/' + productId + '/photos', formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });
            existingPhotos = existingPhotos.concat(res.data.data);
            renderExistingPhotos();
            input.value = '';
            showPhotoAlert('photo-success', res.data.message);
        } catch (e) {
            showPhotoAlert('photo-alert', e.response?.data?.message || 'Failed to upload photos.');
        } finally {
            document.getElementById('upload-btn').disabled = false;
            document.getElementById('upload-spinner').classList.add('hidden');
            document.getElementById('upload-btn-text').textContent = 'Upload';
        }
    };

    function showPhotoAlert(id, msg) {
        document.getElementById(id + '-message').textContent = msg;
        document.getElementById(id).classList.remove('hidden');
        setTimeout(() => document.getElementById(id).classList.add('hidden'), 4000);
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        clearErrors();
        toggleLoading(true);

        try {
            await window.axios.put('/api/admin/products/' + productId, {
                name: form.name.value.trim(),
                price: parseFloat(form.price.value),
                quantity: parseInt(form.quantity.value),
                description: form.description.value.trim() || null,
                is_active: document.getElementById('is_active').checked,
            });
            showAlert('edit-success', 'Product updated!');
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
        if (error.response?.status === 422) {
            for (const [f, m] of Object.entries(error.response.data.errors)) {
                const el = document.getElementById(f + '-error');
                if (el) { el.textContent = m[0]; el.classList.remove('hidden'); }
            }
        } else {
            showAlert('edit-alert', error.response?.data?.message || 'An unexpected error occurred.');
        }
    }
});
</script>
@endpush
