@extends('layouts.admin')

@section('title', 'Edit Vendor — SyriaZone Admin')
@section('page-title', 'Edit Vendor')

@section('content')
<div class="mx-auto max-w-2xl">
    {{-- Breadcrumb --}}
    <nav class="mb-4 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.vendors.index') }}" class="hover:text-gray-700">Vendors</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        <span class="text-gray-900">Edit</span>
    </nav>

    {{-- Loading --}}
    <div id="edit-loading" class="py-20 text-center">
        <div class="mx-auto h-8 w-8 animate-spin rounded-full border-4 border-gray-200 border-t-brand-500"></div>
        <p class="mt-3 text-sm text-gray-500">Loading vendor details...</p>
    </div>

    {{-- Card --}}
    <div id="edit-card" class="card hidden">
        <div class="card-body border-b border-gray-100">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Edit Vendor</h2>
                    <p class="mt-0.5 text-sm text-gray-500">Update vendor account and store information.</p>
                </div>
                {{-- Toggle status --}}
                <div class="flex items-center gap-3">
                    <span id="status-label" class="text-sm font-medium text-gray-500">Active</span>
                    <button type="button" id="toggle-active-btn"
                            class="toggle-switch bg-emerald-500"
                            role="switch" aria-checked="true">
                        <span class="toggle-switch-dot translate-x-5"></span>
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body">
            <x-alert type="error" id="edit-alert" />
            <x-alert type="success" id="edit-success" />

            <form id="edit-vendor-form" class="mt-1 space-y-6" novalidate>
                {{-- User Account --}}
                <fieldset>
                    <legend class="text-sm font-semibold text-gray-900">User Account</legend>
                    <p class="mb-4 text-xs text-gray-500">The vendor's login credentials.</p>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <x-form.input name="name" label="Full Name" :required="true" />
                        <x-form.input name="phone_number" label="Phone Number" type="tel" :required="true" />
                        <x-form.input name="national_id" label="National ID" :required="true" />
                        <x-form.input name="email" label="Email" type="email" placeholder="(optional)" />
                        <div class="sm:col-span-2">
                            <x-form.input name="password" label="New Password" type="password" placeholder="Leave blank to keep current" />
                        </div>
                    </div>
                </fieldset>

                <div class="border-t border-gray-100"></div>

                {{-- Store Details --}}
                <fieldset>
                    <legend class="text-sm font-semibold text-gray-900">Store Details</legend>
                    <p class="mb-4 text-xs text-gray-500">Information about the vendor's store.</p>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <x-form.input name="store_name" label="Store Name" :required="true" />
                        <x-form.input name="address" label="Address" placeholder="(optional)" />
                    </div>
                    <div class="mt-4">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" rows="3" class="form-textarea"></textarea>
                        <p class="form-error" id="description-error"></p>
                    </div>
                </fieldset>

                {{-- Actions --}}
                <div class="flex flex-col-reverse gap-2 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end">
                    <a href="{{ route('admin.vendors.index') }}" class="btn-secondary">Cancel</a>
                    <button type="submit" id="edit-btn" class="btn-primary">
                        <span id="edit-btn-text">Save Changes</span>
                        <svg id="edit-spinner" class="hidden h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const vendorId = {{ $vendorId }};
    const form = document.getElementById('edit-vendor-form');
    const toggleBtn = document.getElementById('toggle-active-btn');
    const statusLabel = document.getElementById('status-label');
    let isActive = true;

    // Toggle switch
    toggleBtn.addEventListener('click', async function () {
        try {
            const response = await window.axios.patch('/api/admin/vendors/' + vendorId + '/toggle-active');
            isActive = response.data.data.is_active;
            updateToggleUI();
            showAlert('edit-success', response.data.message);
        } catch (error) {
            showAlert('edit-alert', error.response?.data?.message || 'Failed to toggle status.');
        }
    });

    function updateToggleUI() {
        if (isActive) {
            toggleBtn.classList.remove('bg-gray-300');
            toggleBtn.classList.add('bg-emerald-500');
            toggleBtn.querySelector('span').classList.remove('translate-x-0');
            toggleBtn.querySelector('span').classList.add('translate-x-5');
            statusLabel.textContent = 'Active';
            statusLabel.classList.remove('text-red-500');
            statusLabel.classList.add('text-gray-500');
        } else {
            toggleBtn.classList.remove('bg-emerald-500');
            toggleBtn.classList.add('bg-gray-300');
            toggleBtn.querySelector('span').classList.remove('translate-x-5');
            toggleBtn.querySelector('span').classList.add('translate-x-0');
            statusLabel.textContent = 'Inactive';
            statusLabel.classList.remove('text-gray-500');
            statusLabel.classList.add('text-red-500');
        }
    }

    loadVendor();

    async function loadVendor() {
        try {
            const response = await window.axios.get('/api/admin/vendors/' + vendorId);
            const vendor = response.data.data;
            form.name.value = vendor.user?.name || '';
            form.phone_number.value = vendor.user?.phone_number || '';
            form.national_id.value = vendor.user?.national_id || '';
            form.email.value = vendor.user?.email || '';
            form.store_name.value = vendor.store_name || '';
            form.address.value = vendor.address || '';
            form.description.value = vendor.description || '';
            isActive = vendor.is_active;
            updateToggleUI();
            document.getElementById('edit-loading').classList.add('hidden');
            document.getElementById('edit-card').classList.remove('hidden');
        } catch (error) {
            document.getElementById('edit-loading').innerHTML = '<p class="text-sm text-red-600">Failed to load vendor details.</p>';
        }
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        clearErrors();
        toggleLoading(true);

        const payload = {
            name: form.name.value.trim(),
            phone_number: form.phone_number.value.trim(),
            national_id: form.national_id.value.trim(),
            store_name: form.store_name.value.trim(),
            is_active: isActive,
        };
        if (form.email.value.trim()) payload.email = form.email.value.trim();
        if (form.password.value) payload.password = form.password.value;
        if (form.address.value.trim()) payload.address = form.address.value.trim();
        if (form.description.value.trim()) payload.description = form.description.value.trim();

        try {
            await window.axios.put('/api/admin/vendors/' + vendorId, payload);
            showAlert('edit-success', 'Vendor updated successfully!');
        } catch (error) {
            handleErrors(error);
        } finally {
            toggleLoading(false);
        }
    });

    function toggleLoading(loading) {
        document.getElementById('edit-btn').disabled = loading;
        document.getElementById('edit-spinner').classList.toggle('hidden', !loading);
        document.getElementById('edit-btn-text').textContent = loading ? 'Saving...' : 'Save Changes';
    }

    function clearErrors() {
        document.getElementById('edit-alert').classList.add('hidden');
        document.getElementById('edit-success').classList.add('hidden');
        document.querySelectorAll('[id$="-error"]').forEach(el => { el.classList.add('hidden'); el.textContent = ''; });
    }

    function showAlert(id, message) {
        const box = document.getElementById(id);
        document.getElementById(id + '-message').textContent = message;
        box.classList.remove('hidden');
        if (id.includes('success')) setTimeout(() => box.classList.add('hidden'), 3000);
    }

    function handleErrors(error) {
        if (error.response?.status === 422) {
            for (const [field, messages] of Object.entries(error.response.data.errors)) {
                const el = document.getElementById(field + '-error');
                if (el) { el.textContent = messages[0]; el.classList.remove('hidden'); }
            }
        } else {
            showAlert('edit-alert', error.response?.data?.message || 'An unexpected error occurred.');
        }
    }
});
</script>
@endpush
