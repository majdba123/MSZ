@extends('layouts.vendor')

@section('title', 'Add Product — SyriaZone Vendor')
@section('page-title', 'Add Product')

@section('content')
<div class="mx-auto max-w-3xl">
    <nav class="mb-6 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('vendor.products.index') }}" class="transition-colors hover:text-emerald-600">Products</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        <span class="font-medium text-gray-900">Create</span>
    </nav>

    <x-alert type="error" id="create-alert" />
    <x-alert type="success" id="create-success" />

    <form id="create-form" class="space-y-6" novalidate enctype="multipart/form-data">

        {{-- Product Details Card --}}
        <div class="card">
            <div class="card-body border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Product Details</h2>
                <p class="mt-0.5 text-sm text-gray-500">Add your product information below.</p>
            </div>

            <div class="card-body space-y-5">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <x-form.input name="name" label="Product Name" placeholder="Enter product name" :required="true" />
                    </div>
                    <x-form.input name="price" label="Price ($)" type="number" placeholder="0.00" :required="true" />
                    <x-form.input name="quantity" label="Quantity" type="number" placeholder="0" :required="true" />
                </div>

                <div>
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" rows="4" placeholder="Describe your product in detail (optional)" class="form-textarea"></textarea>
                    <p class="form-error" id="description-error"></p>
                </div>

                <div class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Active Status</p>
                        <p class="text-xs text-gray-500">Product will be visible to customers when active.</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" id="is_active" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Photos Card --}}
        <div class="card">
            <div class="card-body border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Product Photos</h2>
                <p class="mt-0.5 text-sm text-gray-500">Upload up to 10 images (JPEG, PNG, GIF, WebP · max 5 MB each).</p>
            </div>

            <div class="card-body">
                <p class="form-error" id="photos-error"></p>
                <p class="form-error" id="photos.0-error"></p>

                {{-- Drop Zone --}}
                <div id="drop-zone" class="group flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-gray-50/50 px-6 py-10 text-center transition-colors hover:border-emerald-400 hover:bg-emerald-50/30">
                    <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 transition-transform group-hover:scale-110">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/></svg>
                    </div>
                    <p class="text-sm font-medium text-gray-700">Drag & drop images here, or <span class="text-emerald-600 underline">browse</span></p>
                    <p class="mt-1 text-xs text-gray-400">JPEG, PNG, GIF, WebP · Max 5 MB each</p>
                    <input type="file" id="photo-input" multiple accept="image/jpeg,image/png,image/gif,image/webp" class="hidden">
                </div>

                {{-- Preview Grid --}}
                <div id="photo-preview" class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4"></div>
                <p id="photo-count" class="mt-2 hidden text-xs text-gray-500"></p>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <a href="{{ route('vendor.products.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" id="create-btn" class="btn-primary">
                <span id="create-btn-text">Create Product</span>
                <svg id="create-spinner" class="hidden h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('create-form');
    const photoInput = document.getElementById('photo-input');
    const preview = document.getElementById('photo-preview');
    const dropZone = document.getElementById('drop-zone');
    const countEl = document.getElementById('photo-count');
    let selectedFiles = [];

    // Click to browse
    dropZone.addEventListener('click', () => photoInput.click());

    // Drag & drop
    ['dragenter', 'dragover'].forEach(e => dropZone.addEventListener(e, ev => { ev.preventDefault(); dropZone.classList.add('border-emerald-400', 'bg-emerald-50/40'); }));
    ['dragleave', 'drop'].forEach(e => dropZone.addEventListener(e, ev => { ev.preventDefault(); dropZone.classList.remove('border-emerald-400', 'bg-emerald-50/40'); }));
    dropZone.addEventListener('drop', ev => {
        const files = Array.from(ev.dataTransfer.files).filter(f => f.type.startsWith('image/'));
        addFiles(files);
    });

    photoInput.addEventListener('change', function () { addFiles(Array.from(this.files)); this.value = ''; });

    function addFiles(files) {
        if (selectedFiles.length + files.length > 10) {
            showAlert('create-alert', 'Maximum 10 photos allowed.');
            return;
        }
        selectedFiles = selectedFiles.concat(files);
        renderPreviews();
    }

    function renderPreviews() {
        if (selectedFiles.length === 0) {
            preview.innerHTML = '';
            countEl.classList.add('hidden');
            return;
        }
        countEl.textContent = selectedFiles.length + ' of 10 photos selected';
        countEl.classList.remove('hidden');

        preview.innerHTML = selectedFiles.map((f, i) => {
            const url = URL.createObjectURL(f);
            const sizeMB = (f.size / 1048576).toFixed(1);
            return `<div class="group relative overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition-shadow hover:shadow-md">
                <div class="aspect-square overflow-hidden">
                    <img src="${url}" class="h-full w-full object-cover transition-transform duration-200 group-hover:scale-105" alt="">
                </div>
                <div class="px-2.5 py-2">
                    <p class="truncate text-xs font-medium text-gray-700">${esc(f.name)}</p>
                    <p class="text-[10px] text-gray-400">${sizeMB} MB</p>
                </div>
                <button type="button" onclick="removePreview(${i})" class="absolute right-1.5 top-1.5 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white shadow-lg opacity-0 transition-all group-hover:opacity-100 hover:bg-red-600">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>`;
        }).join('');
    }

    window.removePreview = function (i) { selectedFiles.splice(i, 1); renderPreviews(); };

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        clearErrors();
        toggleLoading(true);

        const formData = new FormData();
        formData.append('name', form.name.value.trim());
        formData.append('price', form.price.value);
        formData.append('quantity', form.quantity.value);
        formData.append('is_active', document.getElementById('is_active').checked ? '1' : '0');
        const desc = form.description.value.trim();
        if (desc) formData.append('description', desc);
        selectedFiles.forEach(f => formData.append('photos[]', f));

        try {
            await window.axios.post('/api/vendor/products', formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });
            showAlert('create-success', 'Product created successfully! Redirecting...');
            setTimeout(() => { window.location.href = '{{ route("vendor.products.index") }}'; }, 800);
        } catch (error) {
            handleErrors(error);
        } finally {
            toggleLoading(false);
        }
    });

    function toggleLoading(l) {
        document.getElementById('create-btn').disabled = l;
        document.getElementById('create-spinner').classList.toggle('hidden', !l);
        document.getElementById('create-btn-text').textContent = l ? 'Creating...' : 'Create Product';
    }
    function clearErrors() {
        document.getElementById('create-alert').classList.add('hidden');
        document.getElementById('create-success').classList.add('hidden');
        document.querySelectorAll('.form-error').forEach(el => { el.classList.add('hidden'); el.textContent = ''; });
    }
    function showAlert(id, msg) {
        const el = document.getElementById(id);
        document.getElementById(id + '-message').textContent = msg;
        el.classList.remove('hidden');
        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    function handleErrors(error) {
        if (error.response?.status === 422) {
            for (const [f, m] of Object.entries(error.response.data.errors)) {
                const el = document.getElementById(f + '-error') || document.getElementById(f.replace('.', '\\.') + '-error');
                if (el) { el.textContent = m[0]; el.classList.remove('hidden'); }
            }
        } else {
            showAlert('create-alert', error.response?.data?.message || 'An unexpected error occurred.');
        }
    }
    function esc(t) { if (!t) return ''; const d = document.createElement('div'); d.textContent = t; return d.innerHTML; }
});
</script>
@endpush
