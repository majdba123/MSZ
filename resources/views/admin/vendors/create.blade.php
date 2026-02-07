@extends('layouts.admin')

@section('title', 'Add Vendor — SyriaZone Admin')
@section('page-title', 'Add Vendor')

@section('content')
<div class="mx-auto max-w-2xl">
    {{-- Breadcrumb --}}
    <nav class="mb-4 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.vendors.index') }}" class="hover:text-gray-700">Vendors</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        <span class="text-gray-900">Create</span>
    </nav>

    <div class="card">
        <div class="card-body border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-900">Create New Vendor</h2>
            <p class="mt-0.5 text-sm text-gray-500">Create a vendor account with user credentials and store details.</p>
        </div>

        <div class="card-body">
            <x-alert type="error" id="create-alert" />
            <x-alert type="success" id="create-success" />

            <form id="create-vendor-form" class="mt-1 space-y-6" novalidate>
                {{-- User Account --}}
                <fieldset>
                    <legend class="text-sm font-semibold text-gray-900">User Account</legend>
                    <p class="mb-4 text-xs text-gray-500">The vendor's login credentials.</p>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <x-form.input name="name" label="Full Name" placeholder="Vendor's full name" :required="true" />
                        <x-form.input name="phone_number" label="Phone Number" type="tel" placeholder="09XXXXXXXX" :required="true" />
                        <x-form.input name="national_id" label="National ID" placeholder="National ID number" :required="true" />
                        <x-form.input name="email" label="Email" type="email" placeholder="vendor@example.com (optional)" />
                        <div class="sm:col-span-2">
                            <x-form.input name="password" label="Password" type="password" placeholder="Min 6 characters" :required="true" />
                        </div>
                    </div>
                </fieldset>

                <div class="border-t border-gray-100"></div>

                {{-- Store Details --}}
                <fieldset>
                    <legend class="text-sm font-semibold text-gray-900">Store Details</legend>
                    <p class="mb-4 text-xs text-gray-500">Information about the vendor's store.</p>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <x-form.input name="store_name" label="Store Name" placeholder="Store display name" :required="true" />
                        <x-form.input name="address" label="Address" placeholder="Store address (optional)" />
                    </div>
                    <div class="mt-4">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" rows="3" placeholder="Brief store description (optional)" class="form-textarea"></textarea>
                        <p class="form-error" id="description-error"></p>
                    </div>
                </fieldset>

                {{-- Actions --}}
                <div class="flex flex-col-reverse gap-2 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end">
                    <a href="{{ route('admin.vendors.index') }}" class="btn-secondary">Cancel</a>
                    <button type="submit" id="create-btn" class="btn-primary">
                        <span id="create-btn-text">Create Vendor</span>
                        <svg id="create-spinner" class="hidden h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
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
    const form = document.getElementById('create-vendor-form');

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        clearErrors();
        toggleLoading(true);

        const payload = {
            name: form.name.value.trim(),
            phone_number: form.phone_number.value.trim(),
            national_id: form.national_id.value.trim(),
            password: form.password.value,
            store_name: form.store_name.value.trim(),
        };
        if (form.email.value.trim()) payload.email = form.email.value.trim();
        if (form.address.value.trim()) payload.address = form.address.value.trim();
        if (form.description.value.trim()) payload.description = form.description.value.trim();

        try {
            await window.axios.post('/api/admin/vendors', payload);
            showAlert('create-success', 'Vendor created successfully! Redirecting...');
            setTimeout(() => { window.location.href = '{{ route("admin.vendors.index") }}'; }, 800);
        } catch (error) {
            handleErrors(error);
        } finally {
            toggleLoading(false);
        }
    });

    function toggleLoading(loading) {
        document.getElementById('create-btn').disabled = loading;
        document.getElementById('create-spinner').classList.toggle('hidden', !loading);
        document.getElementById('create-btn-text').textContent = loading ? 'Creating...' : 'Create Vendor';
    }

    function clearErrors() {
        document.getElementById('create-alert').classList.add('hidden');
        document.getElementById('create-success').classList.add('hidden');
        document.querySelectorAll('[id$="-error"]').forEach(el => { el.classList.add('hidden'); el.textContent = ''; });
    }

    function showAlert(id, message) {
        const box = document.getElementById(id);
        document.getElementById(id + '-message').textContent = message;
        box.classList.remove('hidden');
    }

    function handleErrors(error) {
        if (error.response?.status === 422) {
            for (const [field, messages] of Object.entries(error.response.data.errors)) {
                const el = document.getElementById(field + '-error');
                if (el) { el.textContent = messages[0]; el.classList.remove('hidden'); }
            }
        } else {
            showAlert('create-alert', error.response?.data?.message || 'An unexpected error occurred.');
        }
    }
});
</script>
@endpush
