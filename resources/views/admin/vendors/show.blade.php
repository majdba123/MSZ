@extends('layouts.admin')

@section('title', 'Vendor Details — SyriaZone Admin')
@section('page-title', 'Vendor Details')

@section('content')
<div class="mx-auto max-w-4xl">
    <nav class="mb-4 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.vendors.index') }}" class="hover:text-gray-700">Vendors</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        <span class="text-gray-900">Details</span>
    </nav>

    <div id="show-loading" class="py-16 text-center">
        <div class="mx-auto h-8 w-8 animate-spin rounded-full border-4 border-gray-200 border-t-brand-500"></div>
        <p class="mt-3 text-sm text-gray-500">Loading vendor...</p>
    </div>

    <div id="show-content" class="hidden space-y-5">
        {{-- Vendor Info Card --}}
        <div class="card">
            <div class="card-body border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900" id="vendor-store-name">—</h2>
                        <p class="mt-0.5 text-sm text-gray-500">Vendor Information</p>
                    </div>
                    <div class="flex gap-2">
                        <a id="edit-link" href="#" class="btn-primary btn-sm">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                            Edit
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="mb-6 rounded-lg bg-gray-50 px-4 py-3">
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-400">Owner</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900" id="vendor-owner">—</p>
                </div>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-gray-400">Store Name</p>
                        <p class="mt-1 text-lg font-semibold text-gray-900" id="vendor-store">—</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-gray-400">Status</p>
                        <p class="mt-1" id="vendor-status">—</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-gray-400">Phone Number</p>
                        <p class="mt-1 text-sm font-medium text-gray-900" id="vendor-phone">—</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-gray-400">Email</p>
                        <p class="mt-1 text-sm font-medium text-gray-900" id="vendor-email">—</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-gray-400">National ID</p>
                        <p class="mt-1 text-sm font-medium text-gray-900" id="vendor-national-id">—</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-gray-400">Created</p>
                        <p class="mt-1 text-sm font-medium text-gray-900" id="vendor-created">—</p>
                    </div>
                </div>

                <div class="mt-6 border-t border-gray-100 pt-6">
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-400">Address</p>
                    <p class="mt-2 text-sm text-gray-700" id="vendor-address">—</p>
                </div>

                <div class="mt-6 border-t border-gray-100 pt-6">
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-400">Description</p>
                    <p class="mt-2 text-sm text-gray-700" id="vendor-description">—</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', async function () {
    const vendorId = '{{ $vendorId }}';

    try {
        const res = await window.axios.get('/api/admin/vendors/' + vendorId);
        const v = res.data.data;

        document.getElementById('vendor-store-name').textContent = v.store_name || '—';
        document.getElementById('vendor-store').textContent = v.store_name || '—';
        document.getElementById('vendor-address').textContent = v.address || 'No address provided.';
        document.getElementById('vendor-description').textContent = v.description || 'No description provided.';
        document.getElementById('vendor-created').textContent = v.created_at ? new Date(v.created_at).toLocaleDateString() : '—';

        const ownerName = v.user?.name || '—';
        const ownerPhone = v.user?.phone_number || '';
        const ownerEmail = v.user?.email || '';
        const ownerNationalId = v.user?.national_id || '';

        document.getElementById('vendor-owner').textContent = ownerName;
        document.getElementById('vendor-phone').textContent = ownerPhone || '—';
        document.getElementById('vendor-email').textContent = ownerEmail || '—';
        document.getElementById('vendor-national-id').textContent = ownerNationalId || '—';

        const statusBadge = v.is_active
            ? '<span class="badge badge-success"><span class="mr-1 inline-block h-1.5 w-1.5 rounded-full bg-emerald-500"></span>Active</span>'
            : '<span class="badge badge-danger"><span class="mr-1 inline-block h-1.5 w-1.5 rounded-full bg-red-500"></span>Inactive</span>';
        document.getElementById('vendor-status').innerHTML = statusBadge;

        document.getElementById('edit-link').href = '/admin/vendors/' + vendorId + '/edit';

        document.getElementById('show-loading').classList.add('hidden');
        document.getElementById('show-content').classList.remove('hidden');
    } catch (e) {
        document.getElementById('show-loading').innerHTML = '<p class="text-red-500">Failed to load vendor.</p>';
    }
});
</script>
@endpush

