@extends('auth_breeze.layout')

@section('title', __('Verify email'))

@section('content')
    <div class="space-y-6 text-center">
        <header class="space-y-1">
            <h1 class="text-2xl font-semibold">{{ __('Verify your email address') }}</h1>
            <p class="text-sm text-zinc-500">
                {{ __('Thanks for signing up! Before getting started, please verify your email address by clicking the link we just emailed to you.') }}
            </p>
        </header>

        @if (session('status') === 'verification-link-sent')
            <div class="rounded-lg bg-green-100 px-4 py-2 text-sm text-green-700">
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}" class="space-y-4">
            @csrf
            <button
                type="submit"
                class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
                {{ __('Resend verification email') }}
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="text-sm text-zinc-500">
            @csrf
            <button type="submit" class="text-indigo-600 hover:text-indigo-500">{{ __('Log out') }}</button>
        </form>
    </div>
@endsection
