@extends('layouts.app')

@section('title', 'Order Details — SyriaZone')

@section('content')
<div class="bg-white dark:bg-gray-950">
    <div class="border-b border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-900">
        <div class="mx-auto max-w-screen-xl px-4 py-3 sm:px-6 lg:px-8">
            <nav class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                <a href="{{ route('home') }}" class="hover:text-brand-600 dark:hover:text-brand-400">Home</a>
                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                <a href="{{ route('profile') }}" class="hover:text-brand-600 dark:hover:text-brand-400">Profile</a>
                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                <span class="font-medium text-gray-900 dark:text-white">Order Details</span>
            </nav>
        </div>
    </div>

    <div class="mx-auto max-w-screen-xl px-4 py-8 sm:px-6 lg:px-8">
        <div id="order-loading" class="py-16 text-center">
            <div class="mx-auto h-10 w-10 animate-spin rounded-full border-4 border-gray-200 border-t-brand-500 dark:border-gray-700"></div>
            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">Loading order...</p>
        </div>

        <div id="order-error" class="hidden rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700 dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-300"></div>

        <div id="order-content" class="hidden space-y-5">
            <div class="rounded-2xl border border-gray-200/80 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h1 id="order-number" class="text-lg font-black text-gray-900 dark:text-white">—</h1>
                        <p id="order-meta" class="mt-1 text-xs text-gray-500 dark:text-gray-400">—</p>
                        <a href="{{ route('profile') }}" class="mt-2 inline-flex items-center gap-1 text-xs font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-400">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                            Back to Profile
                        </a>
                    </div>
                    <div class="flex items-center gap-2">
                        <span id="order-status" class="rounded-full px-3 py-1 text-xs font-semibold">pending</span>
                        <span id="order-payment" class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-300">cash</span>
                    </div>
                </div>
                <div class="mt-4 grid gap-3 rounded-xl border border-gray-100 bg-gray-50 p-3 text-xs dark:border-gray-800 dark:bg-gray-800/50 sm:grid-cols-3">
                    <div><p class="text-gray-400">Order ID</p><p id="order-id" class="mt-0.5 font-semibold text-gray-800 dark:text-gray-200">—</p></div>
                    <div><p class="text-gray-400">Vendor ID</p><p id="order-vendor-id" class="mt-0.5 font-semibold text-gray-800 dark:text-gray-200">—</p></div>
                    <div><p class="text-gray-400">Items Count</p><p id="order-items-count" class="mt-0.5 font-semibold text-gray-800 dark:text-gray-200">—</p></div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200/80 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="mb-3 text-base font-bold text-gray-900 dark:text-white">Items</h3>
                <div id="order-items" class="space-y-3"></div>
            </div>

            <div class="rounded-2xl border border-gray-200/80 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="mb-3 text-base font-bold text-gray-900 dark:text-white">Totals</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center justify-between text-gray-500 dark:text-gray-400"><span>Subtotal</span><span id="subtotal-val">0 SYP</span></div>
                    <div class="flex items-center justify-between text-gray-500 dark:text-gray-400"><span>Coupon</span><span id="coupon-val">—</span></div>
                    <div class="flex items-center justify-between text-gray-500 dark:text-gray-400"><span>Coupon Type</span><span id="coupon-type-val">—</span></div>
                    <div class="flex items-center justify-between text-gray-500 dark:text-gray-400"><span>Coupon Value</span><span id="coupon-value-val">—</span></div>
                    <div class="flex items-center justify-between text-gray-500 dark:text-gray-400"><span>Coupon Discount</span><span id="coupon-discount-val">0 SYP</span></div>
                    <div class="flex items-center justify-between border-t border-gray-100 pt-2 text-base font-black text-gray-900 dark:border-gray-800 dark:text-white"><span>Total</span><span id="total-val">0 SYP</span></div>
                </div>
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
        const response = await window.axios.get('/api/orders/' + orderId);
        const order = response.data.data;

        document.getElementById('order-number').textContent = order.order_number || ('Order #' + order.id);
        document.getElementById('order-meta').textContent = (order.created_at ? new Date(order.created_at).toLocaleDateString() : '—') + ' · ' + (order.vendor?.store_name || 'Unknown vendor');
        const statusEl = document.getElementById('order-status');
        const status = String(order.status || 'pending').toLowerCase();
        statusEl.textContent = status;
        statusEl.className = 'rounded-full px-3 py-1 text-xs font-semibold ' + statusClass(status);
        document.getElementById('order-payment').textContent = order.payment_way || 'cash';
        document.getElementById('order-id').textContent = order.id ?? '—';
        document.getElementById('order-vendor-id').textContent = order.vendor?.id ?? '—';
        document.getElementById('order-items-count').textContent = order.items_count ?? (order.items || []).length;

        document.getElementById('subtotal-val').textContent = Number.parseFloat(order.subtotal_amount || 0).toLocaleString() + ' SYP';
        document.getElementById('coupon-val').textContent = order.coupon?.code || '—';
        document.getElementById('coupon-type-val').textContent = order.coupon?.type || '—';
        document.getElementById('coupon-value-val').textContent = order.coupon?.value ? Number.parseFloat(order.coupon.value).toLocaleString() : '—';
        document.getElementById('coupon-discount-val').textContent = '- ' + Number.parseFloat(order.coupon_discount_amount || 0).toLocaleString() + ' SYP';
        document.getElementById('total-val').textContent = Number.parseFloat(order.total_amount || 0).toLocaleString() + ' SYP';

        const itemsEl = document.getElementById('order-items');
        itemsEl.innerHTML = (order.items || []).map(item => `
            <div class="rounded-xl border border-gray-200/80 p-3 dark:border-gray-800">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">${esc(item.product_name || 'Product')}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Product #${item.product_id ?? '—'} · Qty ${item.quantity}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Category: ${esc(item.category_name || '—')} · Subcategory: ${esc(item.subcategory_name || '—')}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Original: ${Number.parseFloat(item.original_unit_price || 0).toLocaleString()} SYP · Applied: ${Number.parseFloat(item.unit_price || 0).toLocaleString()} SYP</p>
                        ${item.has_discount ? `<p class="text-xs text-emerald-600 dark:text-emerald-400">Discount ${Number.parseFloat(item.applied_discount_percentage || 0).toLocaleString()}% · Saved ${Number.parseFloat(item.discount_amount || 0).toLocaleString()} SYP</p>` : ''}
                    </div>
                    <p class="text-sm font-black text-gray-900 dark:text-white">${Number.parseFloat(item.line_total || 0).toLocaleString()} SYP</p>
                </div>
            </div>
        `).join('');

        document.getElementById('order-loading').classList.add('hidden');
        document.getElementById('order-content').classList.remove('hidden');
    } catch (error) {
        document.getElementById('order-loading').classList.add('hidden');
        const err = document.getElementById('order-error');
        err.textContent = error.response?.data?.message || 'Failed to load order.';
        err.classList.remove('hidden');
    }

    function esc(value) {
        if (!value) {
            return '';
        }
        const d = document.createElement('div');
        d.textContent = value;
        return d.innerHTML;
    }

    function statusClass(status) {
        const classes = {
            pending: 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
            confirmed: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
            cancelled: 'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400',
        };
        return classes[status] || classes.pending;
    }
});
</script>
@endpush
