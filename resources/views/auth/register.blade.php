@extends('layouts.app')

@section('title', 'Create Account — SyriaZone')

@section('content')
<div class="flex min-h-[calc(100vh-8rem)] items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
    <div class="w-full max-w-md">
        {{-- Card --}}
        <div class="rounded-xl bg-white p-8 shadow-xl ring-1 ring-gray-100">
            {{-- Header --}}
            <div class="mb-8 text-center">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">Create your account</h1>
                <p class="mt-2 text-sm text-gray-500">Join SyriaZone today</p>
            </div>

            {{-- Alert --}}
            <x-alert type="error" id="register-alert" />
            <x-alert type="success" id="register-success" />

            {{-- Form --}}
            <form id="register-form" class="space-y-5" novalidate>
                <x-form.input
                    name="name"
                    label="Full Name"
                    placeholder="Enter your full name"
                    :required="true"
                    autocomplete="name"
                />

                <x-form.input
                    name="phone_number"
                    label="Phone Number"
                    type="tel"
                    placeholder="09XXXXXXXX"
                    :required="true"
                    autocomplete="tel"
                />

                <x-form.input
                    name="national_id"
                    label="National ID"
                    placeholder="Enter your national ID"
                    :required="true"
                />

                <x-form.input
                    name="email"
                    label="Email Address"
                    type="email"
                    placeholder="you@example.com (optional)"
                    autocomplete="email"
                />

                <x-form.input
                    name="password"
                    label="Password"
                    type="password"
                    placeholder="Min 6 characters (optional)"
                    autocomplete="new-password"
                />

                <x-form.input
                    name="password_confirmation"
                    label="Confirm Password"
                    type="password"
                    placeholder="Repeat your password"
                    autocomplete="new-password"
                />

                <x-form.button type="submit" id="register-btn">
                    <span id="register-btn-text">Create Account</span>
                    <svg id="register-spinner" class="ml-2 hidden h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </x-form.button>
            </form>

            {{-- Footer --}}
            <p class="mt-6 text-center text-sm text-gray-500">
                Already have an account?
                <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:text-brand-500">Sign in</a>
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('register-form');

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        clearErrors();

        const btn = document.getElementById('register-btn');
        const spinner = document.getElementById('register-spinner');
        const btnText = document.getElementById('register-btn-text');

        btn.disabled = true;
        spinner.classList.remove('hidden');
        btnText.textContent = 'Creating account...';

        const payload = {
            name: form.name.value.trim(),
            phone_number: form.phone_number.value.trim(),
            national_id: form.national_id.value.trim(),
        };

        if (form.email.value.trim()) {
            payload.email = form.email.value.trim();
        }
        if (form.password.value) {
            payload.password = form.password.value;
            payload.password_confirmation = form.password_confirmation.value;
        }

        try {
            const response = await window.axios.post('/api/auth/register', payload);

            window.Auth.setToken(response.data.data.token);
            showAlert('register-success', 'Account created successfully! Redirecting...');

            setTimeout(() => {
                window.location.href = '{{ url("/") }}';
            }, 500);
        } catch (error) {
            handleErrors(error);
        } finally {
            btn.disabled = false;
            spinner.classList.add('hidden');
            btnText.textContent = 'Create Account';
        }
    });

    function clearErrors() {
        document.getElementById('register-alert').classList.add('hidden');
        document.getElementById('register-success').classList.add('hidden');
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
            showAlert('register-alert', error.response?.data?.message || 'An unexpected error occurred.');
        }
    }
});
</script>
@endpush

