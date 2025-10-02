<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-zinc-100 dark:bg-zinc-950">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ trim($__env->yieldContent('title', config('app.name', 'TaskInfinity'))) }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
        @livewireStyles
    </head>
    <body class="min-h-full font-sans antialiased text-zinc-900 dark:text-zinc-100">
        <div id="app" class="min-h-screen">
            @include('layouts.partials.flash')

            {{ $slot ?? '' }}

            @hasSection('content')
                @yield('content')
            @endif
        </div>

        @stack('modals')
        @livewireScripts
        @stack('scripts')
    </body>
</html>
