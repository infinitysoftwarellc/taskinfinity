@extends('auth_breeze.layout')

@section('title', __('Reset password'))

@section('content')
    <div class="space-y-6">
        <header class="space-y-1 text-center">
            <h1 class="text-2xl font-semibold">{{ __('Reset your password') }}</h1>
            <p class="text-sm text-zinc-500">{{ __('Create a new password to access your account again.') }}</p>
        </header>

        <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="space-y-2">
                <label for="email" class="block text-sm font-medium text-left">{{ __('Email') }}</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email', $request->email) }}"
                    required
                    autocomplete="username"
                    class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                />
                @error('email')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <label for="password" class="block text-sm font-medium text-left">{{ __('Password') }}</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                />
                @error('password')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <label for="password_confirmation" class="block text-sm font-medium text-left">{{ __('Confirm Password') }}</label>
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                />
            </div>

            <button
                type="submit"
                class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
                {{ __('Reset password') }}
            </button>
        </form>
    </div>
@endsection
