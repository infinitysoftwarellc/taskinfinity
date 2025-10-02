<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-zinc-100">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ trim($__env->yieldContent('title', config('app.name', 'TaskInfinity'))) }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-full bg-gradient-to-br from-zinc-100 via-white to-zinc-200 font-sans antialiased text-zinc-900">
        <div class="flex min-h-screen flex-col items-center justify-center px-4 py-12">
            <a href="{{ url('/') }}" class="mb-10 inline-flex items-center gap-2 text-xl font-semibold tracking-tight text-indigo-600">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-indigo-600 text-white">TI</span>
                <span>{{ config('app.name', 'TaskInfinity') }}</span>
            </a>

            <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-xl ring-1 ring-zinc-200">
                @yield('content')
            </div>
        </div>

        @livewireScripts
    </body>
</html>
