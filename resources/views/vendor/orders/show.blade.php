@extends('layouts.vendor')

@section('title', 'Order Details — SyriaZone Vendor')
@section('page-title', 'Order Details')

@section('content')
<div class="mx-auto max-w-5xl space-y-5">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('vendor.orders.index') }}" class="hover:text-gray-700">Orders</a>
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        <span class="text-gray-900">Details</span>
    </nav>

    <div id="order-loading" class="py-16 text-center">
        <div class="mx-auto h-8 w-8 animate-spin rounded-full border-4 border-gray-200 border-t-emerald-500"></div>
        <p class="mt-3 text-sm text-gray-500">Loading order details...</p>
    </div>

    <div id="order-content" class="hidden space-y-5">
        <div class="rounded-2xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 id="o-number" class="text-lg font-black text-gray-900 dark:text-white">—</h2>
                    <p id="o-meta" class="mt-1 text-xs text-gray-500 dark:text-gray-400">—</p>
                </div>
                <div class="flex items-center gap-2">
                    <span id="o-status" class="rounded-full px-2.5 py-1 text-[11px] font-semibold">pending</span>
                    <span id="o-payment" class="rounded-full bg-gray-100 px-2.5 py-1 text-[11px] font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-300">cash</span>
                </div>
            </div>
            <div class="mt-4 grid gap-3 rounded-xl border border-gray-100 bg-gray-50 p-3 text-xs dark:border-gray-800 dark:bg-gray-800/50 sm:grid-cols-4">
                <div><p class="text-gray-400">Order ID</p><p id="o-id" class="mt-0.5 font-semibold text-gray-800 dark:text-gray-200">—</p></div>
                <div><p class="text-gray-400">User ID</p><p id="o-user-id" class="mt-0.5 font-semibold text-gray-800 dark:text-gray-200">—</p></div>
                <div><p class="text-gray-400">Vendor ID</p><p id="o-vendor-id" class="mt-0.5 font-semibold text-gray-800 dark:text-gray-200">—</p></div>
                <div><p class="text-gray-400">Items Count</p><p id="o-items-count" class="mt-0.5 font-semibold text-gray-800 dark:text-gray-200">—</p></div>
            </div>
        </div>

        <div class="grid gap-5 lg:grid-cols-2">
            <div class="card p-4">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Customer Info</h3>
                <div class="mt-3 space-y-2 text-xs text-gray-600 dark:text-gray-300">
                    <p><span class="text-gray-400">Name:</span> <span id="o-user-name">—</span></p>
                    <p><span class="text-gray-400">Email:</span> <span id="o-user-email">—</span></p>
                </div>
            </div>
            <div class="card p-4">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Vendor Info</h3>
                <div class="mt-3 space-y-2 text-xs text-gray-600 dark:text-gray-300">
                    <p><span class="text-gray-400">Store:</span> <span id="o-vendor-name">—</span></p>
                </div>
            </div>
        </div>

        <div class="card p-4">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white">Items</h3>
            <div id="o-items" class="mt-3 space-y-3"></div>
        </div>

        <div class="card p-4">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white">Totals & Coupon</h3>
            <div class="mt-3 grid gap-2 text-sm text-gray-600 dark:text-gray-300 sm:grid-cols-2">
                <div class="flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2 dark:bg-gray-800"><span>Subtotal</span><span id="o-subtotal">0</span></div>
                <div class="flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2 dark:bg-gray-800"><span>Coupon Code</span><span id="o-coupon-code">—</span></div>
                <div class="flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2 dark:bg-gray-800"><span>Coupon Type</span><span id="o-coupon-type">—</span></div>
                <div class="flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2 dark:bg-gray-800"><span>Coupon Value</span><span id="o-coupon-value">—</span></div>
                <div class="flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2 dark:bg-gray-800"><span>Coupon Discount</span><span id="o-coupon-discount">0</span></div>
                <div class="flex items-center justify-between rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 font-black text-emerald-700 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-300"><span>Total</span><span id="o-total">0</span></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', async function () {
    const orderId = '{{ $orderId }}';

    try {
        const response = await window.axios.get('/api/vendor/orders/' + orderId);
        const order = response.data.data;

        document.getElementById('o-number').textContent = order.order_number || ('Order #' + order.id);
        document.getElementById('o-meta').textContent = (order.created_at ? new Date(order.created_at).toLocaleDateString() : '—') + ' · Last update: ' + (order.updated_at ? new Date(order.updated_at).toLocaleDateString() : '—');
        const status = String(order.status || 'pending').toLowerCase();
        const statusEl = document.getElementById('o-status');
        statusEl.textContent = status;
        statusEl.className = 'rounded-full px-2.5 py-1 text-[11px] font-semibold ' + statusClass(status);

        document.getElementById('o-payment').textContent = order.payment_way || 'cash';
        document.getElementById('o-id').textContent = order.id ?? '—';
        document.getElementById('o-user-id').textContent = order.user_id ?? '—';
        document.getElementById('o-vendor-id').textContent = order.vendor_id ?? '—';
        document.getElementById('o-items-count').textContent = order.items_count ?? (order.items || []).length;
        document.getElementById('o-user-name').textContent = order.user?.name || '—';
        document.getElementById('o-user-email').textContent = order.user?.email || '—';
        document.getElementById('o-vendor-name').textContent = order.vendor?.store_name || '—';

        document.getElementById('o-subtotal').textContent = money(order.subtotal_amount);
        document.getElementById('o-coupon-code').textContent = order.coupon_code || '—';
        document.getElementById('o-coupon-type').textContent = order.coupon_type || '—';
        document.getElementById('o-coupon-value').textContent = order.coupon_value ? Number.parseFloat(order.coupon_value).toLocaleString() : '—';
        document.getElementById('o-coupon-discount').textContent = '- ' + money(order.coupon_discount_amount);
        document.getElementById('o-total').textContent = money(order.total_amount);

        document.getElementById('o-items').innerHTML = (order.items || []).map((item) => {
            return `<div class="rounded-xl border border-gray-200/80 p-3 dark:border-gray-800">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">${esc(item.product_name || 'Product')}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Item #${item.id ?? '—'} · Product #${item.product_id ?? '—'} · Qty ${item.quantity}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Category: ${esc(item.product?.subcategory?.category?.name || '—')} · Subcategory: ${esc(item.product?.subcategory?.name || '—')}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Original: ${money(item.original_unit_price)} · Applied: ${money(item.unit_price)}</p>
                        ${item.has_discount ? `<p class="text-xs text-emerald-600 dark:text-emerald-400">Discount ${Number.parseFloat(item.applied_discount_percentage || 0).toLocaleString()}% · Saved ${money(item.discount_amount)}</p>` : ''}
                    </div>
                    <p class="text-sm font-black text-gray-900 dark:text-white">${money(item.line_total)}</p>
                </div>
            </div>`;
        }).join('');

        document.getElementById('order-loading').classList.add('hidden');
        document.getElementById('order-content').classList.remove('hidden');
    } catch (error) {
        document.getElementById('order-loading').innerHTML = `<p class="text-sm font-medium text-red-500">${esc(error.response?.data?.message || 'Failed to load order details.')}</p>`;
    }

    function money(v) {
        return Number.parseFloat(v || 0).toLocaleString() + ' SYP';
    }

    function statusClass(status) {
        const classes = {
            pending: 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
            confirmed: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
            cancelled: 'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400',
        };
        return classes[status] || classes.pending;
    }

    function esc(value) {
        if (!value) return '';
        const d = document.createElement('div');
        d.textContent = value;
        return d.innerHTML;
    }
});
</script>
@endpush
