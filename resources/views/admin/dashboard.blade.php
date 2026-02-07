@extends('layouts.admin')

@section('title', 'Dashboard — SyriaZone Admin')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Welcome --}}
    <div class="card card-body">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-900">Welcome back!</h2>
                <p class="mt-0.5 text-sm text-gray-500">Here's what's happening with your platform today.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.vendors.create') }}" class="btn-primary btn-sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Add Vendor
                </a>
                <a href="{{ route('admin.users.create') }}" class="btn-secondary btn-sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    Add User
                </a>
            </div>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        {{-- Total Users --}}
        <div class="card card-body">
            <div class="flex items-start justify-between">
                <div class="min-w-0 flex-1">
                    <p class="truncate text-xs font-semibold uppercase tracking-wider text-gray-500">Total Users</p>
                    <p id="stat-users" class="mt-2 text-2xl font-bold text-gray-900">—</p>
                </div>
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                </div>
            </div>
        </div>

        {{-- Total Vendors --}}
        <div class="card card-body">
            <div class="flex items-start justify-between">
                <div class="min-w-0 flex-1">
                    <p class="truncate text-xs font-semibold uppercase tracking-wider text-gray-500">Total Vendors</p>
                    <p id="stat-vendors" class="mt-2 text-2xl font-bold text-gray-900">—</p>
                </div>
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72"/></svg>
                </div>
            </div>
        </div>

        {{-- Active Vendors --}}
        <div class="card card-body">
            <div class="flex items-start justify-between">
                <div class="min-w-0 flex-1">
                    <p class="truncate text-xs font-semibold uppercase tracking-wider text-gray-500">Active Vendors</p>
                    <p id="stat-active-vendors" class="mt-2 text-2xl font-bold text-emerald-600">—</p>
                </div>
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        {{-- Inactive Vendors --}}
        <div class="card card-body">
            <div class="flex items-start justify-between">
                <div class="min-w-0 flex-1">
                    <p class="truncate text-xs font-semibold uppercase tracking-wider text-gray-500">Inactive Vendors</p>
                    <p id="stat-inactive-vendors" class="mt-2 text-2xl font-bold text-red-600">—</p>
                </div>
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-red-50 text-red-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick links --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <a href="{{ route('admin.vendors.index') }}" class="card card-body group flex items-center gap-4 transition-shadow hover:shadow-md">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-600 transition-colors group-hover:bg-brand-100">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35"/></svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-gray-900">Manage Vendors</p>
                <p class="text-xs text-gray-500">View, edit, and toggle vendor accounts</p>
            </div>
            <svg class="h-5 w-5 shrink-0 text-gray-400 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        </a>

        <a href="{{ route('admin.users.index') }}" class="card card-body group flex items-center gap-4 transition-shadow hover:shadow-md">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600 transition-colors group-hover:bg-blue-100">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-gray-900">Manage Users</p>
                <p class="text-xs text-gray-500">View, edit, and manage user accounts</p>
            </div>
            <svg class="h-5 w-5 shrink-0 text-gray-400 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', async function () {
    try {
        const [usersRes, vendorsRes] = await Promise.all([
            window.axios.get('/api/admin/users?page=1'),
            window.axios.get('/api/admin/vendors?page=1'),
        ]);

        document.getElementById('stat-users').textContent = usersRes.data.meta?.total ?? '0';

        const totalVendors = vendorsRes.data.meta?.total ?? 0;
        document.getElementById('stat-vendors').textContent = totalVendors;

        const vendors = vendorsRes.data.data || [];
        let active = 0, inactive = 0;
        vendors.forEach(v => { v.is_active ? active++ : inactive++; });

        document.getElementById('stat-active-vendors').textContent = active;
        document.getElementById('stat-inactive-vendors').textContent = inactive;
    } catch (e) {
        // Stats unavailable
    }
});
</script>
@endpush
