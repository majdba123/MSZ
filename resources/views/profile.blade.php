@extends('layouts.app')

@section('title', 'My Profile — SyriaZone')

@section('content')
<div class="bg-white dark:bg-gray-950">
    <div class="border-b border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-900">
        <div class="mx-auto max-w-screen-2xl px-4 py-3 sm:px-6 lg:px-8">
            <nav class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                <a href="{{ route('home') }}" class="hover:text-brand-600 dark:hover:text-brand-400">Home</a>
                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                <span class="font-medium text-gray-900 dark:text-white">My Profile</span>
            </nav>
        </div>
    </div>

    <div class="mx-auto max-w-screen-2xl px-4 py-10 sm:px-6 lg:px-8">
        {{-- Loading --}}
        <div id="profile-loading" class="py-20 text-center">
            <div class="mx-auto h-12 w-12 animate-spin rounded-full border-4 border-gray-200 border-t-brand-500 dark:border-gray-700"></div>
            <p class="mt-4 text-sm font-medium text-gray-500 dark:text-gray-400">Loading profile...</p>
        </div>

        {{-- Not Authenticated --}}
        <div id="profile-guest" class="hidden py-20 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
            <p class="mt-4 text-base font-bold text-gray-900 dark:text-white">Please sign in to view your profile</p>
            <a href="{{ route('login') }}" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-brand-500 px-6 py-3 text-sm font-bold text-white hover:bg-brand-600">Sign In</a>
        </div>

        {{-- Profile Form --}}
        <div id="profile-content" class="hidden">
            <div class="mb-8">
                <h1 class="text-2xl font-black text-gray-900 dark:text-white">My Profile</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Update your personal information and manage favourites</p>
            </div>

            <div class="grid gap-8 lg:grid-cols-3">
            {{-- Left Column: Profile Form --}}
            <div class="lg:col-span-1">

            {{-- Avatar Section --}}
            <div class="mb-6 rounded-2xl border border-gray-200/80 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <label class="mb-4 block text-sm font-bold text-gray-900 dark:text-white">Profile Photo</label>
                <div class="flex items-center gap-6">
                    <div class="relative">
                        <div id="avatar-preview" class="flex h-24 w-24 items-center justify-center overflow-hidden rounded-full bg-gradient-to-br from-brand-400 to-brand-600 ring-4 ring-white shadow-lg dark:ring-gray-800">
                            <span id="avatar-initial" class="text-3xl font-black text-white">?</span>
                        </div>
                        <label for="avatar-input" class="absolute -bottom-1 -right-1 flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-brand-500 text-white shadow-lg transition-all hover:bg-brand-600 hover:scale-110">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/></svg>
                        </label>
                        <input type="file" id="avatar-input" class="hidden" accept="image/*">
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Upload a new avatar</p>
                        <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500">JPG, PNG or GIF. Max 2MB.</p>
                        <button id="avatar-remove" class="mt-2 hidden text-xs font-bold text-red-500 hover:text-red-700 dark:hover:text-red-400">Remove photo</button>
                    </div>
                </div>
            </div>

            {{-- Form Fields --}}
            <div class="mb-6 rounded-2xl border border-gray-200/80 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="mb-5 text-base font-bold text-gray-900 dark:text-white">Personal Information</h3>
                <form id="profile-form" class="space-y-5">
                    <div>
                        <label for="p-name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Full Name</label>
                        <input type="text" id="p-name" class="block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-900 shadow-sm transition-all focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-brand-500" placeholder="Your full name">
                        <p id="err-name" class="mt-1 hidden text-xs text-red-500"></p>
                    </div>
                    <div>
                        <label for="p-email" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Email Address</label>
                        <input type="email" id="p-email" class="block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-900 shadow-sm transition-all focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-brand-500" placeholder="you@example.com">
                        <p id="err-email" class="mt-1 hidden text-xs text-red-500"></p>
                    </div>
                    <div>
                        <label for="p-phone" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Phone Number</label>
                        <input type="text" id="p-phone" class="block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-900 shadow-sm transition-all focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-brand-500" placeholder="09XXXXXXXX">
                        <p id="err-phone_number" class="mt-1 hidden text-xs text-red-500"></p>
                    </div>

                    {{-- Read-only fields --}}
                    <div class="grid grid-cols-2 gap-4 rounded-xl border border-gray-100 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-800/50">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Account Type</p>
                            <p id="p-type" class="mt-0.5 text-sm font-bold text-gray-900 dark:text-white">—</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Member Since</p>
                            <p id="p-since" class="mt-0.5 text-sm font-bold text-gray-900 dark:text-white">—</p>
                        </div>
                    </div>

                    <div id="save-alert" class="hidden items-center gap-3 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Profile updated successfully!</span>
                    </div>
                    <div id="save-error" class="hidden items-center gap-3 rounded-xl bg-red-50 px-4 py-3 text-sm font-medium text-red-700 dark:bg-red-500/10 dark:text-red-400">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                        <span id="save-error-msg">Something went wrong.</span>
                    </div>

                    <button type="submit" id="save-btn" class="flex w-full items-center justify-center gap-2 rounded-xl bg-gray-900 py-3.5 text-sm font-bold text-white transition-all hover:bg-brand-600 active:scale-[.97] dark:bg-white dark:text-gray-900 dark:hover:bg-brand-500 dark:hover:text-white">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        Save Changes
                    </button>
                </form>
            </div>
            </div>{{-- end left column --}}

            {{-- Right Column: Favourites --}}
            <div class="lg:col-span-2">
                <div class="rounded-2xl border border-gray-200/80 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                    <div class="mb-5 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-50 dark:bg-red-500/10">
                                <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">My Favourites</h3>
                                <p id="fav-count" class="text-xs text-gray-400 dark:text-gray-500">Loading...</p>
                            </div>
                        </div>
                    </div>

                    <div id="fav-loading" class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                        <div class="skeleton h-52 rounded-xl"></div><div class="skeleton h-52 rounded-xl"></div><div class="skeleton h-52 rounded-xl"></div>
                    </div>
                    <div id="fav-grid" class="grid grid-cols-2 gap-3 sm:grid-cols-3"></div>
                    <div id="fav-empty" class="hidden py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-200 dark:text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                        <p class="mt-3 text-sm font-bold text-gray-500 dark:text-gray-400">No favourites yet</p>
                        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Click the heart icon on any product to add it here</p>
                        <a href="{{ route('products.index') }}" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-gray-900 px-5 py-2.5 text-sm font-bold text-white hover:bg-brand-600 dark:bg-white dark:text-gray-900 dark:hover:bg-brand-500 dark:hover:text-white">Browse Products</a>
                    </div>
                </div>

                <div class="mt-6 rounded-2xl border border-gray-200/80 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                    <div class="mb-5 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">Order History</h3>
                            <p id="orders-count" class="text-xs text-gray-400 dark:text-gray-500">Loading...</p>
                        </div>
                        <p class="rounded-full bg-gray-100 px-3 py-1 text-[11px] font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-300">Payment: Cash only</p>
                    </div>

                    <div id="orders-loading" class="space-y-3">
                        <div class="skeleton h-20 rounded-xl"></div>
                        <div class="skeleton h-20 rounded-xl"></div>
                        <div class="skeleton h-20 rounded-xl"></div>
                    </div>

                    <div id="orders-list" class="space-y-3"></div>

                    <div id="orders-empty" class="hidden py-10 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-200 dark:text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h11.25m-11.25 4.5h11.25m-11.25 4.5h11.25M3.75 6.75h.008v.008H3.75V6.75zm0 4.5h.008v.008H3.75v-.008zm0 4.5h.008v.008H3.75v-.008z"/></svg>
                        <p class="mt-3 text-sm font-bold text-gray-500 dark:text-gray-400">No orders yet</p>
                    </div>

                    <div id="orders-pagination" class="mt-5 hidden items-center justify-between border-t border-gray-100 pt-4 dark:border-gray-800">
                        <p id="orders-page-info" class="text-xs text-gray-500 dark:text-gray-400"></p>
                        <div class="flex gap-2">
                            <button id="orders-prev" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-700 transition-colors hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Prev</button>
                            <button id="orders-next" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-700 transition-colors hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Next</button>
                        </div>
                    </div>
                </div>
            </div>{{-- end right column --}}
            </div>{{-- end grid --}}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', async function () {
    const $ = id => document.getElementById(id);

    if (!window.Auth || !window.Auth.isAuthenticated()) {
        $('profile-loading').classList.add('hidden');
        $('profile-guest').classList.remove('hidden');
        return;
    }

    let user = window.Auth.getUser();
    if (!user) {
        try {
            const res = await window.axios.get('/api/user');
            user = res.data.data || res.data;
            window.Auth.setUser(user);
        } catch (e) {
            $('profile-loading').classList.add('hidden');
            $('profile-guest').classList.remove('hidden');
            return;
        }
    }

    fillForm(user);
    $('profile-loading').classList.add('hidden');
    $('profile-content').classList.remove('hidden');
    loadFavourites();
    let currentOrdersPage = 1;
    loadOrderHistory();

    let pendingAvatar = null;

    $('orders-prev').addEventListener('click', () => {
        if (currentOrdersPage > 1) {
            currentOrdersPage -= 1;
            loadOrderHistory();
        }
    });
    $('orders-next').addEventListener('click', () => {
        currentOrdersPage += 1;
        loadOrderHistory();
    });

    function fillForm(u) {
        $('p-name').value = u.name || '';
        $('p-email').value = u.email || '';
        $('p-phone').value = u.phone_number || '';
        const roleMap = { 0: 'Customer', 1: 'Admin', 2: 'Vendor' };
        $('p-type').textContent = roleMap[u.type] || 'Customer';
        $('p-since').textContent = u.created_at ? new Date(u.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : '—';

        const preview = $('avatar-preview');
        if (u.avatar_url) {
            preview.innerHTML = `<img src="${u.avatar_url}" class="h-full w-full object-cover" alt="">`;
            $('avatar-remove').classList.remove('hidden');
        } else {
            preview.innerHTML = `<span class="text-3xl font-black text-white">${(u.name || '?').charAt(0).toUpperCase()}</span>`;
            $('avatar-remove').classList.add('hidden');
        }
    }

    $('avatar-input').addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        if (file.size > 2 * 1024 * 1024) {
            alert('Image must be under 2MB');
            this.value = '';
            return;
        }
        pendingAvatar = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            $('avatar-preview').innerHTML = `<img src="${e.target.result}" class="h-full w-full object-cover" alt="">`;
            $('avatar-remove').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    });

    $('avatar-remove').addEventListener('click', () => {
        pendingAvatar = 'remove';
        const initial = ($('p-name').value || '?').charAt(0).toUpperCase();
        $('avatar-preview').innerHTML = `<span class="text-3xl font-black text-white">${initial}</span>`;
        $('avatar-remove').classList.add('hidden');
        $('avatar-input').value = '';
    });

    function escH(s){if(!s)return '';const d=document.createElement('div');d.textContent=s;return d.innerHTML;}

    async function loadFavourites() {
        try {
            const res = await window.axios.get('/api/favourites');
            const products = res.data.data || [];
            $('fav-loading').classList.add('hidden');
            $('fav-count').textContent = products.length + ' product' + (products.length !== 1 ? 's' : '');

            if (!products.length) {
                $('fav-empty').classList.remove('hidden');
                return;
            }

            $('fav-grid').innerHTML = products.map(p => {
                const photoUrl = p.first_photo_url || '';
                return `<div class="group relative overflow-hidden rounded-xl border border-gray-200/80 bg-white transition-all hover:shadow-md dark:border-gray-800 dark:bg-gray-900">
                    <button onclick="removeFav(${p.id},this.closest('.group'))" class="absolute right-2 top-2 z-10 flex h-8 w-8 items-center justify-center rounded-full bg-white/90 text-red-500 shadow-sm backdrop-blur-sm transition-all hover:scale-110 hover:bg-red-50 dark:bg-gray-900/90 dark:hover:bg-red-500/10" title="Remove from favourites">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z"/></svg>
                    </button>
                    <a href="/products/${p.id}">
                        <div class="aspect-square overflow-hidden bg-gray-50 dark:bg-gray-800">
                            ${photoUrl ? `<img src="${escH(photoUrl)}" class="h-full w-full object-contain p-3 transition-transform duration-300 group-hover:scale-105" loading="lazy" alt="">` : `<div class="flex h-full items-center justify-center"><svg class="h-10 w-10 text-gray-200 dark:text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159"/></svg></div>`}
                        </div>
                        <div class="p-3">
                            ${p.vendor ? `<p class="mb-0.5 truncate text-[10px] text-gray-400 dark:text-gray-500">${escH(p.vendor.store_name)}</p>` : ''}
                            <h4 class="line-clamp-2 text-xs font-bold text-gray-900 group-hover:text-brand-600 dark:text-white dark:group-hover:text-brand-400">${escH(p.name)}</h4>
                            <div class="mt-1.5 flex items-baseline gap-1"><span class="text-sm font-black text-gray-900 dark:text-white">${parseFloat(p.price).toLocaleString()}</span><span class="text-[10px] text-gray-400">SYP</span></div>
                        </div>
                    </a>
                </div>`;
            }).join('');
        } catch(e) {
            $('fav-loading').classList.add('hidden');
            $('fav-empty').classList.remove('hidden');
            $('fav-count').textContent = '0 products';
        }
    }

    async function loadOrderHistory() {
        try {
            const res = await window.axios.get('/api/orders?page=' + currentOrdersPage);
            const orders = res.data.data || [];
            const meta = res.data.meta || {};

            $('orders-loading').classList.add('hidden');
            $('orders-count').textContent = (meta.total || orders.length) + ' order' + ((meta.total || orders.length) !== 1 ? 's' : '');

            if (!orders.length) {
                $('orders-list').innerHTML = '';
                $('orders-empty').classList.remove('hidden');
                $('orders-pagination').classList.add('hidden');
                return;
            }

            $('orders-empty').classList.add('hidden');
            $('orders-list').innerHTML = orders.map(renderOrderCard).join('');

            if ((meta.last_page || 1) > 1) {
                $('orders-pagination').classList.remove('hidden');
                $('orders-pagination').classList.add('flex');
                $('orders-page-info').textContent = `Page ${meta.current_page} of ${meta.last_page}`;
                $('orders-prev').disabled = meta.current_page <= 1;
                $('orders-next').disabled = meta.current_page >= meta.last_page;
            } else {
                $('orders-pagination').classList.add('hidden');
                $('orders-pagination').classList.remove('flex');
            }
        } catch (e) {
            $('orders-loading').classList.add('hidden');
            $('orders-empty').classList.remove('hidden');
            $('orders-count').textContent = '0 orders';
        }
    }

    function renderOrderCard(order) {
        const date = order.created_at ? new Date(order.created_at).toLocaleDateString() : '—';
        const coupon = order.coupon;
        const statusBadge = orderStatusBadge(order.status);
        const itemsHtml = (order.items || []).map(item => {
            const original = parseFloat(item.original_unit_price || 0).toLocaleString();
            const unit = parseFloat(item.unit_price || 0).toLocaleString();
            const total = parseFloat(item.line_total || 0).toLocaleString();
            const discountPart = item.has_discount
                ? `<p class="text-[11px] text-emerald-600 dark:text-emerald-400">Discount ${parseFloat(item.applied_discount_percentage || 0)}% · Saved ${parseFloat(item.discount_amount || 0).toLocaleString()} SYP</p>`
                : '';

            return `<div class="rounded-lg border border-gray-100 p-2.5 dark:border-gray-800">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="truncate text-xs font-bold text-gray-900 dark:text-white">${escH(item.product_name)}</p>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400">Qty ${item.quantity} · ${unit} SYP each</p>
                        ${item.has_discount ? `<p class="text-[11px] text-gray-400 line-through">Original ${original} SYP</p>` : ''}
                        ${discountPart}
                    </div>
                    <p class="shrink-0 text-xs font-black text-gray-800 dark:text-gray-100">${total} SYP</p>
                </div>
            </div>`;
        }).join('');

        return `<div class="rounded-xl border border-gray-200/80 p-4 dark:border-gray-800">
            <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                <div>
                    <p class="text-xs font-bold text-gray-900 dark:text-white">${escH(order.order_number)}</p>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">${date} · ${escH(order.vendor?.store_name || 'Unknown vendor')}</p>
                </div>
                <div class="flex items-center gap-2">
                    ${statusBadge}
                    <span class="rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-300">${escH(order.payment_way || 'cash')}</span>
                </div>
            </div>

            <div class="space-y-2">${itemsHtml}</div>

            <div class="mt-3 border-t border-gray-100 pt-3 text-xs dark:border-gray-800">
                <div class="flex items-center justify-between text-gray-500 dark:text-gray-400"><span>Subtotal</span><span>${parseFloat(order.subtotal_amount || 0).toLocaleString()} SYP</span></div>
                <div class="mt-1 flex items-center justify-between text-gray-500 dark:text-gray-400"><span>Coupon</span><span>${coupon ? escH(coupon.code) : '—'}</span></div>
                <div class="mt-1 flex items-center justify-between text-gray-500 dark:text-gray-400"><span>Coupon Discount</span><span>- ${parseFloat(order.coupon_discount_amount || 0).toLocaleString()} SYP</span></div>
                <div class="mt-2 flex items-center justify-between text-sm font-black text-gray-900 dark:text-white"><span>Total</span><span>${parseFloat(order.total_amount || 0).toLocaleString()} SYP</span></div>
                <div class="mt-3">
                    <a href="/orders/${order.id}" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 px-2.5 py-1 text-[11px] font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">View Details</a>
                </div>
            </div>
        </div>`;
    }

    function orderStatusBadge(status) {
        const normalized = String(status || '').toLowerCase();
        const map = {
            pending: 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
            confirmed: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
            cancelled: 'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400'
        };

        return `<span class="rounded-full px-2 py-0.5 text-[10px] font-bold ${map[normalized] || map.pending}">${escH(normalized || 'pending')}</span>`;
    }

    window.removeFav = async function(productId, cardEl) {
        try {
            await window.axios.delete('/api/favourites/' + productId);
            if (window._favIds) window._favIds.delete(productId);
            if (cardEl) {
                cardEl.style.transition = 'opacity .3s, transform .3s';
                cardEl.style.opacity = '0';
                cardEl.style.transform = 'scale(.95)';
                setTimeout(() => { cardEl.remove(); updateFavCount(); }, 300);
            }
        } catch(e) {}
    };

    function updateFavCount() {
        const grid = $('fav-grid');
        const count = grid ? grid.children.length : 0;
        $('fav-count').textContent = count + ' product' + (count !== 1 ? 's' : '');
        if (count === 0) $('fav-empty').classList.remove('hidden');
    }

    $('profile-form').addEventListener('submit', async function (e) {
        e.preventDefault();
        const btn = $('save-btn');
        btn.disabled = true;
        btn.innerHTML = '<div class="h-5 w-5 animate-spin rounded-full border-2 border-white/30 border-t-white"></div> Saving...';
        $('save-alert').classList.add('hidden');
        $('save-error').classList.add('hidden');
        document.querySelectorAll('[id^="err-"]').forEach(el => { el.classList.add('hidden'); el.textContent = ''; });

        try {
            const fd = new FormData();
            fd.append('name', $('p-name').value.trim());
            fd.append('email', $('p-email').value.trim());
            fd.append('phone_number', $('p-phone').value.trim());
            if (pendingAvatar && pendingAvatar !== 'remove') {
                fd.append('avatar', pendingAvatar);
            } else if (pendingAvatar === 'remove') {
                fd.append('avatar', '');
            }

            const res = await window.axios.post('/api/profile', fd, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });

            const updatedUser = res.data.data;
            window.Auth.setUser(updatedUser);
            pendingAvatar = null;
            fillForm(updatedUser);

            $('save-alert').classList.remove('hidden');
            $('save-alert').classList.add('flex');
            setTimeout(() => { $('save-alert').classList.add('hidden'); $('save-alert').classList.remove('flex'); }, 4000);

            if (typeof updateNavbar === 'function') updateNavbar();
        } catch (err) {
            if (err.response && err.response.status === 422) {
                const errors = err.response.data.errors || {};
                Object.keys(errors).forEach(key => {
                    const el = $('err-' + key);
                    if (el) { el.textContent = errors[key][0]; el.classList.remove('hidden'); }
                });
            } else {
                $('save-error').classList.remove('hidden');
                $('save-error').classList.add('flex');
                $('save-error-msg').textContent = err.response?.data?.message || 'Something went wrong.';
            }
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg> Save Changes';
        }
    });
});
</script>
@endpush
