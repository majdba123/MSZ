<section id="vendors" class="scroll-mt-20 bg-gray-50 py-10 sm:py-14">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between">
            <div><h2 class="text-xl font-extrabold text-gray-900 sm:text-2xl">Featured Stores</h2><p class="mt-1 text-sm text-gray-500">Shop from trusted vendors</p></div>
            <div class="flex gap-1.5">
                <button onclick="document.getElementById('vendors-track').scrollBy({left:-300,behavior:'smooth'})" class="flex h-9 w-9 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 shadow-sm hover:border-brand-300 hover:text-brand-600 hover:shadow"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg></button>
                <button onclick="document.getElementById('vendors-track').scrollBy({left:300,behavior:'smooth'})" class="flex h-9 w-9 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 shadow-sm hover:border-brand-300 hover:text-brand-600 hover:shadow"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg></button>
            </div>
        </div>
        <div id="vendors-loading" class="mt-6 flex gap-4 overflow-hidden"><div class="skeleton h-52 w-64 flex-shrink-0 rounded-2xl"></div><div class="skeleton h-52 w-64 flex-shrink-0 rounded-2xl"></div><div class="skeleton h-52 w-64 flex-shrink-0 rounded-2xl"></div><div class="skeleton h-52 w-64 flex-shrink-0 rounded-2xl"></div></div>
        <div id="vendors-track" class="mt-6 flex gap-4 overflow-x-auto scroll-smooth pb-2" style="scrollbar-width:none;-ms-overflow-style:none;"></div>
        <div id="vendors-empty" class="mt-6 hidden py-8 text-center text-sm text-gray-400">No stores available yet.</div>
    </div>
</section>
