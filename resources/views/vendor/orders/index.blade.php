@extends('layouts.vendor')

@section('title', 'Orders — SyriaZone Vendor')
@section('page-title', 'Orders')

@section('content')
<div class="space-y-4">
    <div class="card p-4">
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
            <input id="f-product" type="text" placeholder="Product name" class="rounded-lg border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
            <select id="f-status" class="rounded-lg border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <select id="f-category" class="rounded-lg border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
                <option value="">All Categories</option>
            </select>
            <select id="f-subcategory" class="rounded-lg border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
                <option value="">All Subcategories</option>
            </select>
            <button id="f-reset" class="rounded-lg border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Reset</button>
        </div>
    </div>

    <div id="orders-loading" class="py-14 text-center">
        <div class="mx-auto h-8 w-8 animate-spin rounded-full border-4 border-gray-200 border-t-emerald-500"></div>
        <p class="mt-3 text-sm text-gray-500">Loading orders...</p>
    </div>

    <div id="orders-empty" class="hidden card py-14 text-center">
        <p class="text-sm font-semibold text-gray-600 dark:text-gray-300">No orders found.</p>
    </div>

    <div id="orders-list" class="hidden space-y-3"></div>

    <div id="orders-pagination" class="hidden items-center justify-between border-t border-gray-100 pt-3 dark:border-gray-800">
        <p id="orders-page-info" class="text-xs text-gray-500"></p>
        <div class="flex gap-2">
            <button id="orders-prev" class="btn-secondary btn-xs">Prev</button>
            <button id="orders-next" class="btn-secondary btn-xs">Next</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const state = { page: 1, categories: [] };
    const $ = id => document.getElementById(id);

    loadCategories();
    loadOrders();

    ['f-product', 'f-status', 'f-category', 'f-subcategory'].forEach((id) => {
        $(id).addEventListener('change', () => { state.page = 1; loadOrders(); });
        if (id === 'f-product') {
            $(id).addEventListener('input', debounce(() => { state.page = 1; loadOrders(); }, 300));
        }
    });

    $('f-category').addEventListener('change', function () {
        fillSubcategories(this.value);
        state.page = 1;
        loadOrders();
    });

    $('f-reset').addEventListener('click', function () {
        ['f-product', 'f-status', 'f-category', 'f-subcategory'].forEach((id) => $(id).value = '');
        fillSubcategories('');
        state.page = 1;
        loadOrders();
    });

    $('orders-prev').addEventListener('click', () => { if (state.page > 1) { state.page--; loadOrders(); } });
    $('orders-next').addEventListener('click', () => { state.page++; loadOrders(); });

    async function loadCategories() {
        try {
            const response = await window.axios.get('/api/vendor/allowed-categories');
            const categories = response.data.data || [];
            state.categories = categories;
            $('f-category').innerHTML = '<option value="">All Categories</option>' + categories.map(c => `<option value="${c.id}">${esc(c.name)}</option>`).join('');
        } catch (e) {}
    }

    function fillSubcategories(categoryId) {
        if (!categoryId) {
            $('f-subcategory').innerHTML = '<option value="">All Subcategories</option>';
            return;
        }
        const category = state.categories.find(c => String(c.id) === String(categoryId));
        const subs = category?.subcategories || [];
        $('f-subcategory').innerHTML = '<option value="">All Subcategories</option>' + subs.map(s => `<option value="${s.id}">${esc(s.name)}</option>`).join('');
    }

    async function loadOrders() {
        toggleLoading(true);
        try {
            const params = new URLSearchParams({ page: String(state.page) });
            const product = $('f-product').value.trim();
            const status = $('f-status').value;
            const category = $('f-category').value;
            const subcategory = $('f-subcategory').value;
            if (product) params.set('product', product);
            if (status) params.set('status', status);
            if (category) params.set('category_id', category);
            if (subcategory) params.set('subcategory_id', subcategory);

            const response = await window.axios.get('/api/vendor/orders?' + params.toString());
            const orders = response.data.data || [];
            const meta = response.data.meta || {};

            if (!orders.length) {
                $('orders-empty').classList.remove('hidden');
                $('orders-list').classList.add('hidden');
                $('orders-pagination').classList.add('hidden');
                return;
            }

            $('orders-empty').classList.add('hidden');
            $('orders-list').classList.remove('hidden');
            $('orders-list').innerHTML = orders.map(orderCard).join('');
            $('orders-pagination').classList.remove('hidden');
            $('orders-pagination').classList.add('flex');
            $('orders-page-info').textContent = `Page ${meta.current_page} of ${meta.last_page} · ${meta.total} orders`;
            $('orders-prev').disabled = meta.current_page <= 1;
            $('orders-next').disabled = meta.current_page >= meta.last_page;
        } catch (e) {
            $('orders-empty').classList.remove('hidden');
            $('orders-list').classList.add('hidden');
            $('orders-pagination').classList.add('hidden');
        } finally {
            toggleLoading(false);
        }
    }

    function orderCard(order) {
        const date = order.created_at ? new Date(order.created_at).toLocaleDateString() : '—';
        const items = (order.items || []).slice(0, 3).map(i => `<li class="text-xs text-gray-500 dark:text-gray-400">${esc(i.product_name)} · Qty ${i.quantity}</li>`).join('');
        const extraItems = (order.items || []).length > 3 ? `<li class="text-xs font-semibold text-gray-400">+ ${(order.items || []).length - 3} more items</li>` : '';
        return `<div class="card p-4">
            <div class="flex flex-wrap items-start justify-between gap-2">
                <div>
                    <p class="text-sm font-bold text-gray-900 dark:text-white">${esc(order.order_number || ('Order #' + order.id))}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">${date} · ${esc(order.user?.name || 'Unknown user')}</p>
                </div>
                ${statusBadge(order.status)}
            </div>
            <ul class="mt-2 space-y-1">${items || '<li class="text-xs text-gray-400">No items.</li>'}${extraItems}</ul>
            <div class="mt-3 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                <span>Total: <strong class="text-gray-800 dark:text-gray-100">${Number.parseFloat(order.total_amount || 0).toLocaleString()} SYP</strong></span>
                <div class="flex items-center gap-2">
                    <span>${paymentBadge(order.payment_way)}</span>
                    <a href="/vendor/orders/${order.id}" class="rounded-lg border border-gray-200 px-2.5 py-1 text-[11px] font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">View Details</a>
                </div>
            </div>
        </div>`;
    }

    function toggleLoading(show) {
        $('orders-loading').classList.toggle('hidden', !show);
    }

    function esc(value) {
        if (!value) return '';
        const d = document.createElement('div');
        d.textContent = value;
        return d.innerHTML;
    }

    function statusBadge(status) {
        const s = String(status || 'pending').toLowerCase();
        const cls = {
            pending: 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
            confirmed: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
            cancelled: 'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400'
        };
        return `<span class="rounded-full px-2.5 py-1 text-[11px] font-semibold ${cls[s] || cls.pending}">${esc(s)}</span>`;
    }

    function paymentBadge(paymentWay) {
        return `<span class="rounded-full bg-gray-100 px-2.5 py-1 text-[11px] font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-300">${esc(paymentWay || 'cash')}</span>`;
    }

    function debounce(fn, wait) {
        let timer = null;
        return function (...args) {
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, args), wait);
        };
    }
});
</script>
@endpush
