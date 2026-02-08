@extends('layouts.admin')

@section('title', 'Add Product — SyriaZone Admin')
@section('page-title', 'Add Product')

@section('content')
<div class="mx-auto max-w-3xl">
    <nav class="mb-6 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.products.index') }}" class="transition-colors hover:text-brand-600">Products</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        <span class="font-medium text-gray-900">Create</span>
    </nav>

    <x-alert type="error" id="create-alert" />
    <x-alert type="success" id="create-success" />

    <form id="create-form" class="space-y-6" novalidate enctype="multipart/form-data">
        <x-products.form-fields :showVendorSelect="true" />

        <x-products.photo-upload color="brand" />

        {{-- Actions --}}
        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <a href="{{ route('admin.products.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" id="create-btn" class="btn-primary">
                <span id="create-btn-text">Create Product</span>
                <svg id="create-spinner" class="hidden h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<x-products.photo-upload-script color="brand" alertId="create-alert" />
<script>
document.addEventListener('DOMContentLoaded', async function () {
    const form = document.getElementById('create-form');
    const vendorSelect = document.getElementById('vendor_id');

    // Load vendors
    try {
        const res = await window.axios.get('/api/admin/vendors?per_page=100');
        const vendors = res.data.data;
        vendorSelect.innerHTML = '<option value="">Select a vendor...</option>' +
            vendors.filter(v => v.is_active).map(v =>
                `<option value="${v.id}">${esc(v.store_name)} — ${esc(v.user?.name || 'N/A')}</option>`
            ).join('');
    } catch (e) {
        vendorSelect.innerHTML = '<option value="">Failed to load vendors</option>';
        console.error('Failed to load vendors:', e);
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        clearErrors();
        toggleLoading(true);

        const formData = new FormData();
        formData.append('vendor_id', vendorSelect.value);
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
            vendor_id: vendorSelect.value,
            name: form.name.value.trim(),
            price: parseFloat(form.price.value),
            quantity: parseInt(form.quantity.value),
            is_active: document.getElementById('is_active').checked,
            description: desc || null,
            photos_count: selectedFiles.length
        });

        try {
            await window.axios.post('/api/admin/products', formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });
            showAlert('create-success', 'Product created successfully! Redirecting...');
            setTimeout(() => { window.location.href = '{{ route("admin.products.index") }}'; }, 800);
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
    function esc(t) { if (!t) return ''; const d = document.createElement('div'); d.textContent = t; return d.innerHTML; }
});
</script>
@endpush
