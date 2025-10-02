{{-- Dashboard landing page. --}}
@extends('layouts.app')

@section('title', __('Dashboard'))

@section('content')
    <div class="flex min-h-screen bg-zinc-100 text-zinc-900 dark:bg-zinc-950 dark:text-zinc-100">
        @include('app.shared.navigation')

        <main class="flex-1 px-4 py-12 sm:px-6 lg:px-10">
            <div class="mx-auto max-w-4xl rounded-xl bg-white p-8 shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-900 dark:ring-zinc-700">
                <h1 class="text-2xl font-semibold">{{ __('Dashboard') }}</h1>
                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ __("You're logged in!") }}</p>
            </div>
        </main>
    </div>
@endsection
