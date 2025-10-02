@extends('auth_breeze.layout')

@section('title', __('Log in'))

@section('content')
    <div class="space-y-6">
        <header class="space-y-1 text-center">
            <h1 class="text-2xl font-semibold">{{ __('Log in to your account') }}</h1>
            <p class="text-sm text-zinc-500">{{ __('Enter your email and password below to continue.') }}</p>
        </header>

        @if (session('status'))
            <div class="rounded-lg bg-green-100 px-4 py-2 text-sm text-green-700">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div class="space-y-2">
                <label for="email" class="block text-sm font-medium text-left">{{ __('Email') }}</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
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
                    autocomplete="current-password"
                    class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                />
                @error('password')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="remember" class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500">
                    <span>{{ __('Remember me') }}</span>
                </label>
                @if (Route::has('password.request'))
                    <a class="text-indigo-600 hover:text-indigo-500" href="{{ route('password.request') }}">{{ __('Forgot your password?') }}</a>
                @endif
            </div>

            <button
                type="submit"
                class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
                {{ __('Log in') }}
            </button>
        </form>

        <p class="text-center text-sm text-zinc-500">
            {{ __('Need an account?') }}
            <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500">{{ __('Create one now') }}</a>
        </p>
    </div>
@endsection
