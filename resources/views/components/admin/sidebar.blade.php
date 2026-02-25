@php
    $currentRoute = request()->route()?->getName() ?? '';
@endphp

<aside id="admin-sidebar"
       class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col bg-navy-900 transition-transform duration-300 ease-in-out lg:translate-x-0">

    {{-- Logo --}}
    <div class="flex h-14 shrink-0 items-center gap-3 border-b border-white/10 px-6">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
            <span class="text-xl font-bold tracking-tight text-white">Syria<span class="text-brand-400">Zone</span></span>
        </a>
        <span class="rounded-md bg-brand-500/15 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-brand-400">Admin</span>

        {{-- Close button (mobile) --}}
        <button onclick="closeSidebar()" class="ml-auto rounded-md p-1 text-gray-400 hover:text-white lg:hidden">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto px-4 py-5">
        <p class="mb-2 px-3 text-[10px] font-bold uppercase tracking-widest text-gray-500">Overview</p>

        {{-- Dashboard --}}
        <a href="{{ route('admin.dashboard') }}"
           class="mb-0.5 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-150
                  {{ str_starts_with($currentRoute, 'admin.dashboard') ? 'bg-white/10 text-white' : 'text-gray-400 hover:bg-white/5 hover:text-gray-200' }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
            </svg>
            Dashboard
        </a>

        <p class="mb-2 mt-6 px-3 text-[10px] font-bold uppercase tracking-widest text-gray-500">Management</p>

        {{-- Vendors --}}
        <a href="{{ route('admin.vendors.index') }}"
           class="mb-0.5 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-150
                  {{ str_starts_with($currentRoute, 'admin.vendors') ? 'bg-white/10 text-white' : 'text-gray-400 hover:bg-white/5 hover:text-gray-200' }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.15c0 .415.336.75.75.75z"/>
            </svg>
            Vendors
            @if(str_starts_with($currentRoute, 'admin.vendors'))
                <span class="ml-auto h-1.5 w-1.5 rounded-full bg-brand-400"></span>
            @endif
        </a>

        {{-- Products --}}
        <a href="{{ route('admin.products.index') }}"
           class="mb-0.5 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-150
                  {{ str_starts_with($currentRoute, 'admin.products') ? 'bg-white/10 text-white' : 'text-gray-400 hover:bg-white/5 hover:text-gray-200' }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
            </svg>
            Products
            @if(str_starts_with($currentRoute, 'admin.products'))
                <span class="ml-auto h-1.5 w-1.5 rounded-full bg-brand-400"></span>
            @endif
        </a>

        {{-- Coupons --}}
        <a href="{{ route('admin.coupons.index') }}"
           class="mb-0.5 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-150
                  {{ str_starts_with($currentRoute, 'admin.coupons') ? 'bg-white/10 text-white' : 'text-gray-400 hover:bg-white/5 hover:text-gray-200' }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6.75V6A2.25 2.25 0 0014.25 3.75h-4.5A2.25 2.25 0 007.5 6v.75m9 0V18A2.25 2.25 0 0114.25 20.25h-4.5A2.25 2.25 0 017.5 18V6.75m9 0h-9"/>
            </svg>
            Coupons
            @if(str_starts_with($currentRoute, 'admin.coupons'))
                <span class="ml-auto h-1.5 w-1.5 rounded-full bg-brand-400"></span>
            @endif
        </a>

        {{-- Categories --}}
        <a href="{{ route('admin.categories.index') }}"
           class="mb-0.5 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-150
                  {{ str_starts_with($currentRoute, 'admin.categories') ? 'bg-white/10 text-white' : 'text-gray-400 hover:bg-white/5 hover:text-gray-200' }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h69.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
            </svg>
            Categories
            @if(str_starts_with($currentRoute, 'admin.categories'))
                <span class="ml-auto h-1.5 w-1.5 rounded-full bg-brand-400"></span>
            @endif
        </a>

        {{-- Subcategories --}}
        <a href="{{ route('admin.subcategories.index') }}"
           class="mb-0.5 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-150
                  {{ str_starts_with($currentRoute, 'admin.subcategories') ? 'bg-white/10 text-white' : 'text-gray-400 hover:bg-white/5 hover:text-gray-200' }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
            </svg>
            Subcategories
            @if(str_starts_with($currentRoute, 'admin.subcategories'))
                <span class="ml-auto h-1.5 w-1.5 rounded-full bg-brand-400"></span>
            @endif
        </a>

        {{-- Users --}}
        <a href="{{ route('admin.users.index') }}"
           class="mb-0.5 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-150
                  {{ str_starts_with($currentRoute, 'admin.users') ? 'bg-white/10 text-white' : 'text-gray-400 hover:bg-white/5 hover:text-gray-200' }}">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
            </svg>
            Users
            @if(str_starts_with($currentRoute, 'admin.users'))
                <span class="ml-auto h-1.5 w-1.5 rounded-full bg-brand-400"></span>
            @endif
        </a>
    </nav>

    {{-- Footer --}}
    <div class="border-t border-white/10 px-6 py-3">
        <p class="text-[11px] text-gray-500">&copy; {{ date('Y') }} SyriaZone</p>
    </div>
</aside>
