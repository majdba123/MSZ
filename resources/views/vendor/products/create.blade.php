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
        <x-products.form-fields />

        <x-products.photo-upload color="emerald" />

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
<x-products.photo-upload-script color="emerald" alertId="create-alert" />
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('create-form');
    const STORAGE_KEY = 'vendor_product_create_form';

    // Restore form data from localStorage
    try {
        const saved = localStorage.getItem(STORAGE_KEY);
        if (saved) {
            const data = JSON.parse(saved);
            if (form.name) form.name.value = data.name || '';
            if (form.price) form.price.value = data.price || '';
            if (form.quantity) form.quantity.value = data.quantity || '';
            if (form.description) form.description.value = data.description || '';
            if (document.getElementById('is_active')) {
                document.getElementById('is_active').checked = data.is_active || false;
            }
        }
    } catch (e) {
        console.error('Error restoring form:', e);
    }

    // Save form data to localStorage on change
    function saveFormData() {
        try {
            const data = {
                name: form.name?.value || '',
                price: form.price?.value || '',
                quantity: form.quantity?.value || '',
                description: form.description?.value || '',
                is_active: document.getElementById('is_active')?.checked || false,
            };
            localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
        } catch (e) {
            console.error('Error saving form:', e);
        }
    }

    // Add event listeners to save on change
    if (form.name) form.name.addEventListener('input', saveFormData);
    if (form.price) form.price.addEventListener('input', saveFormData);
    if (form.quantity) form.quantity.addEventListener('input', saveFormData);
    if (form.description) form.description.addEventListener('input', saveFormData);
    const isActiveCheckbox = document.getElementById('is_active');
    if (isActiveCheckbox) isActiveCheckbox.addEventListener('change', saveFormData);

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        clearErrors();
        toggleLoading(true);

        const formData = new FormData();
        formData.append('name', form.name.value.trim());
        formData.append('price', parseFloat(form.price.value) || 0);
        formData.append('quantity', parseInt(form.quantity.value) || 0);
        formData.append('is_active', document.getElementById('is_active').checked ? '1' : '0');
        const desc = form.description.value.trim();
        if (desc) formData.append('description', desc);

        const selectedFiles = window.getSelectedPhotos ? window.getSelectedPhotos() : [];
        selectedFiles.forEach(f => formData.append('photos[]', f));

        // Debug: Log what we're sending
        console.log('Form data:', {
            name: form.name.value.trim(),
            price: parseFloat(form.price.value),
            quantity: parseInt(form.quantity.value),
            is_active: document.getElementById('is_active').checked,
            description: desc || null,
            photos_count: selectedFiles.length
        });

        try {
            await window.axios.post('/api/vendor/products', formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });
            // Clear saved form data on success
            localStorage.removeItem(STORAGE_KEY);
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
        console.error('Product creation error:', error);
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
            showAlert('create-alert', 'Validation failed: ' + errorMessages);
        } else {
            const errorMsg = error.response?.data?.message || error.message || 'An unexpected error occurred.';
            showAlert('create-alert', errorMsg);
        }
    }
});
</script>
@endpush
