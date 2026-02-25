<section id="products" class="scroll-mt-20 bg-white py-10 sm:py-14">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div><h2 class="text-xl font-extrabold text-gray-900 sm:text-2xl">All Products</h2><p class="mt-1 text-sm text-gray-500">Find exactly what you need</p></div>
            <div class="flex flex-wrap items-center gap-2">
                <select id="filter-vendor" class="h-10 rounded-xl border border-gray-200 bg-white px-3 text-sm shadow-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none"><option value="">All Stores</option></select>
                <select id="filter-category" class="h-10 rounded-xl border border-gray-200 bg-white px-3 text-sm shadow-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none"><option value="">All Categories</option></select>
                <button id="apply-filters" class="h-10 rounded-xl bg-brand-500 px-5 text-sm font-semibold text-white shadow-sm hover:bg-brand-600">Filter</button>
                <button id="clear-filters" class="h-10 rounded-xl px-3 text-sm font-medium text-gray-500 hover:bg-gray-100">Clear</button>
            </div>
        </div>

        {{-- Skeleton --}}
        <div id="products-loading" class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 sm:gap-4">
            <div class="skeleton h-80 rounded-2xl"></div><div class="skeleton h-80 rounded-2xl"></div><div class="skeleton h-80 rounded-2xl"></div><div class="skeleton h-80 rounded-2xl"></div><div class="skeleton hidden h-80 rounded-2xl sm:block"></div>
        </div>

        {{-- Grid --}}
        <div id="products-grid" class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 sm:gap-4"></div>

        {{-- Empty --}}
        <div id="products-empty" class="mt-6 hidden py-16 text-center">
            <svg class="mx-auto h-14 w-14 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
            <p class="mt-4 text-base font-semibold text-gray-600">No products found</p>
            <p class="mt-1 text-sm text-gray-400">Try adjusting your filters</p>
        </div>

        {{-- Pagination --}}
        <div id="products-pagination" class="mt-8 flex flex-wrap items-center justify-center gap-1.5"></div>
    </div>
</section>
