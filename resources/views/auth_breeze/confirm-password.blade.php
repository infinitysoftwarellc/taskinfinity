@extends('auth_breeze.layout')

@section('title', __('Confirm password'))

@section('content')
    <div class="space-y-6">
        <header class="space-y-1 text-center">
            <h1 class="text-2xl font-semibold">{{ __('Confirm your password') }}</h1>
            <p class="text-sm text-zinc-500">{{ __('For your security, please confirm your password to continue.') }}</p>
        </header>

        @if (session('status'))
            <div class="rounded-lg bg-green-100 px-4 py-2 text-sm text-green-700">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
            @csrf

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

            <button
                type="submit"
                class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
                {{ __('Confirm') }}
            </button>
        </form>
    </div>
@endsection
