@extends('layouts.app')

@section('title', 'Admin Login — SyriaZone')

@section('content')
<div class="flex min-h-[calc(100vh-8rem)] items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
    <div class="w-full max-w-sm">
        <div class="card card-body">
            {{-- Header --}}
            <div class="mb-6 text-center">
                <span class="text-2xl font-bold tracking-tight text-gray-900">Syria<span class="text-brand-500">Zone</span></span>
                <div class="mt-2 inline-block rounded-md bg-navy-900 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-brand-400">Admin Panel</div>
                <p class="mt-3 text-sm text-gray-500">Sign in to access the admin dashboard</p>
            </div>

            {{-- Alerts --}}
            <x-alert type="error" id="admin-login-alert" />

            {{-- Form --}}
            <form id="admin-login-form" class="space-y-4" novalidate>
                <x-form.input
                    name="phone_number"
                    label="Phone Number"
                    type="tel"
                    placeholder="09XXXXXXXX"
                    :required="true"
                    autocomplete="tel"
                />

                <x-form.input
                    name="password"
                    label="Password"
                    type="password"
                    placeholder="Enter your password"
                    :required="true"
                    autocomplete="current-password"
                />

                <x-form.button type="submit" id="admin-login-btn">
                    <span id="admin-login-btn-text">Sign In</span>
                    <svg id="admin-login-spinner" class="ml-2 hidden h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </x-form.button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // If already authenticated as admin, redirect to dashboard
    if (window.Auth && window.Auth.isAuthenticated()) {
        window.axios.get('/api/user').then(function (response) {
            if (response.data.type === 1) {
                window.location.href = '{{ route("admin.dashboard") }}';
            }
        }).catch(function () {
            window.Auth.removeToken();
        });
    }

    const form = document.getElementById('admin-login-form');

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        clearErrors();

        const btn = document.getElementById('admin-login-btn');
        const spinner = document.getElementById('admin-login-spinner');
        const btnText = document.getElementById('admin-login-btn-text');

        btn.disabled = true;
        spinner.classList.remove('hidden');
        btnText.textContent = 'Signing in...';

        try {
            const response = await window.axios.post('/api/auth/login', {
                phone_number: form.phone_number.value.trim(),
                password: form.password.value,
            });

            const user = response.data.data.user;
            const token = response.data.data.token;

            window.Auth.setToken(token);

            const userResponse = await window.axios.get('/api/user');
            if (userResponse.data.type !== 1) {
                window.Auth.removeToken();
                showAlert('admin-login-alert', 'Access denied. Admin credentials required.');
                return;
            }

            window.location.href = '{{ route("admin.dashboard") }}';
        } catch (error) {
            handleErrors(error);
        } finally {
            btn.disabled = false;
            spinner.classList.add('hidden');
            btnText.textContent = 'Sign In';
        }
    });

    function clearErrors() {
        document.getElementById('admin-login-alert').classList.add('hidden');
        document.querySelectorAll('[id$="-error"]').forEach(el => {
            el.classList.add('hidden');
            el.textContent = '';
        });
    }

    function showAlert(id, message) {
        const box = document.getElementById(id);
        const msg = document.getElementById(id + '-message');
        msg.textContent = message;
        box.classList.remove('hidden');
    }

    function handleErrors(error) {
        if (error.response && error.response.status === 422) {
            const errors = error.response.data.errors;
            for (const [field, messages] of Object.entries(errors)) {
                const errorEl = document.getElementById(field + '-error');
                if (errorEl) {
                    errorEl.textContent = messages[0];
                    errorEl.classList.remove('hidden');
                }
            }
        } else {
            showAlert('admin-login-alert', error.response?.data?.message || 'Invalid credentials.');
        }
    }
});
</script>
@endpush
