{{-- Settings landing page. --}}
@extends('layouts.app')

@section('title', __('Settings'))

@section('content')
    <div class="flex min-h-screen bg-zinc-100 dark:bg-zinc-950">
        @include('app.shared.navigation')

        <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
            <div class="rounded-xl border border-dashed border-zinc-300 bg-white p-10 text-center shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <h1 class="text-3xl font-semibold text-zinc-900 dark:text-white">{{ __('Settings') }}</h1>
                <p class="mt-4 text-base text-zinc-600 dark:text-zinc-300">
                    {{ __('Choose an item from the menu to configure the application.') }}
                </p>
            </div>
        </main>
    </div>
@endsection
