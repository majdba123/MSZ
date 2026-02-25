<div id="cart-modal" class="fixed inset-0 z-[60] hidden" style="animation:fadeIn .2s ease-out;">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="window.closeCartModal && window.closeCartModal()"></div>
    <div class="absolute right-0 top-0 flex h-full w-full max-w-lg flex-col bg-white shadow-2xl" style="animation:slideInRight .3s ease-out;">
        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-50"><svg class="h-5 w-5 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg></div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Shopping Cart</h3>
                    <p class="text-xs text-gray-500" id="cart-item-count">0 items</p>
                </div>
            </div>
            <button id="close-cart" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600" onclick="window.closeCartModal && window.closeCartModal()"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        {{-- Items --}}
        <div class="flex-1 overflow-y-auto px-6 py-4" style="scrollbar-width:thin;">
            <div id="cart-items" class="space-y-3"></div>
            <div id="cart-empty" class="hidden py-20 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
                <p class="mt-4 font-semibold text-gray-600">Your cart is empty</p>
                <p class="mt-1 text-sm text-gray-400">Start adding products to see them here</p>
            </div>
        </div>
        {{-- Footer --}}
        <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-500">Total</span>
                <span id="cart-total" class="text-xl font-bold text-gray-900">0.00 <span class="text-sm font-normal text-gray-400">SYP</span></span>
            </div>
            <button id="checkout-btn" class="mt-3 hidden w-full rounded-xl bg-brand-500 py-3 text-sm font-bold text-white shadow-md transition-all hover:bg-brand-600 active:scale-[.98]">Proceed to Checkout</button>
        </div>
    </div>
</div>
