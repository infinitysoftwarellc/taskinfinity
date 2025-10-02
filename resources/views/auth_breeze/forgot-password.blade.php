@extends('auth_breeze.layout')

@section('title', __('Forgot password'))

@section('content')
    <div class="space-y-6">
        <header class="space-y-1 text-center">
            <h1 class="text-2xl font-semibold">{{ __('Forgot your password?') }}</h1>
            <p class="text-sm text-zinc-500">{{ __('Enter your email to receive a password reset link.') }}</p>
        </header>

        @if (session('status'))
            <div class="rounded-lg bg-green-100 px-4 py-2 text-sm text-green-700">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
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
                    class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                />
                @error('email')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
                {{ __('Email password reset link') }}
            </button>
        </form>

        <p class="text-center text-sm text-zinc-500">
            <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">{{ __('Back to login') }}</a>
        </p>
    </div>
@endsection
