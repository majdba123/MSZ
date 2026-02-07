@extends('layouts.app')

@section('title', 'SyriaZone')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    {{-- Hero Section --}}
    <div class="mb-12 rounded-2xl bg-gradient-to-r from-navy-900 to-navy-700 p-12 text-center shadow-2xl">
        <h1 class="text-4xl font-bold tracking-tight text-white sm:text-5xl">
            Welcome to <span class="text-brand-500">SyriaZone</span>
        </h1>
        <p class="mx-auto mt-4 max-w-2xl text-lg text-gray-300">
            Your trusted platform. Fast, reliable, and secure.
        </p>
        <div class="mt-8 flex justify-center gap-4" id="hero-actions">
            <a href="{{ route('register') }}" id="hero-register" class="btn-primary px-8 py-3 text-base">
                Get Started
            </a>
            <a href="{{ route('login') }}" id="hero-login" class="btn-secondary border-gray-500 bg-transparent px-8 py-3 text-base text-white hover:bg-white/10">
                Sign In
            </a>
        </div>
    </div>

    {{-- Features Grid --}}
    <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
        <div class="rounded-xl bg-white p-8 shadow-md ring-1 ring-gray-100 transition-shadow hover:shadow-lg">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-brand-100 text-brand-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">Secure</h3>
            <p class="mt-2 text-sm text-gray-500">Industry-standard security with token-based authentication.</p>
        </div>

        <div class="rounded-xl bg-white p-8 shadow-md ring-1 ring-gray-100 transition-shadow hover:shadow-lg">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-brand-100 text-brand-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">Fast</h3>
            <p class="mt-2 text-sm text-gray-500">Optimized for speed with modern API architecture.</p>
        </div>

        <div class="rounded-xl bg-white p-8 shadow-md ring-1 ring-gray-100 transition-shadow hover:shadow-lg">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-brand-100 text-brand-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">Reliable</h3>
            <p class="mt-2 text-sm text-gray-500">Built on Laravel with enterprise-grade reliability.</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.Auth && window.Auth.isAuthenticated()) {
            const heroRegister = document.getElementById('hero-register');
            const heroLogin = document.getElementById('hero-login');
            if (heroRegister) heroRegister.classList.add('hidden');
            if (heroLogin) heroLogin.classList.add('hidden');
        }
    });
</script>
@endpush

