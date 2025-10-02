@extends('auth_breeze.layout')

@section('title', __('Two-factor challenge'))

@section('content')
    <div class="space-y-6">
        <header class="space-y-1 text-center">
            <h1 class="text-2xl font-semibold">{{ __('Two-factor authentication') }}</h1>
            <p class="text-sm text-zinc-500">
                {{ __('Please confirm access to your account by entering the authentication code provided by your authenticator application.') }}
            </p>
        </header>

        <form method="POST" action="{{ route('two-factor.login') }}" class="space-y-5">
            @csrf

            <div class="space-y-2">
                <label for="code" class="block text-sm font-medium text-left">{{ __('Code') }}</label>
                <input
                    id="code"
                    type="text"
                    name="code"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-center text-sm tracking-[0.5em] focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                />
                @error('code')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <label for="recovery_code" class="block text-sm font-medium text-left">{{ __('Recovery code') }}</label>
                <input
                    id="recovery_code"
                    type="text"
                    name="recovery_code"
                    class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                />
                @error('recovery_code')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
                {{ __('Authenticate') }}
            </button>
        </form>
    </div>
@endsection
