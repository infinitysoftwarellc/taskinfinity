<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-900 text-gray-200">
    
    <div class="flex h-screen">
        
        <aside class="w-16 bg-gray-800 flex flex-col items-center py-4 border-r border-gray-700 flex-shrink-0">
            <div class="mb-6">
                <div class="w-10 h-10 bg-gray-600 rounded-full flex items-center justify-center text-xl relative">
                    <i class="fas fa-user"></i>
                    <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-yellow-400 ring-2 ring-gray-800"></span>
                </div>
            </div>
            <nav class="flex flex-col items-center space-y-6">
                <a href="#" class="text-gray-400 hover:text-gray-100 p-2 rounded-md"><i class="fas fa-check-square text-2xl"></i></a>
                <a href="#" class="text-gray-400 hover:text-gray-100 p-2 rounded-md"><i class="fas fa-chart-pie text-2xl"></i></a>
                <a href="#" class="text-gray-400 hover:text-gray-100 p-2 rounded-md"><i class="fas fa-history text-2xl"></i></a>
                <a href="#" class="text-gray-400 hover:text-gray-100 p-2 rounded-md"><i class="fas fa-star text-2xl"></i></a>
                <a href="#" class="text-gray-400 hover:text-gray-100 p-2 rounded-md"><i class="fas fa-search text-2xl"></i></a>
            </nav>
            <div class="mt-auto flex flex-col items-center space-y-6 mb-4">
                <a href="#" class="text-gray-400 hover:text-gray-100 p-2 rounded-md"><i class="fas fa-sync-alt text-2xl"></i></a>
                <a href="#" class="text-gray-400 hover:text-gray-100 p-2 rounded-md"><i class="fas fa-bell text-2xl"></i></a>
                <a href="#" class="text-gray-400 hover:text-gray-100 p-2 rounded-md"><i class="fas fa-question-circle text-2xl"></i></a>
            </div>
        </aside>

        <div class="flex-1 flex overflow-hidden">
             {{ $slot }}
        </div>

    </div>

    @livewireScripts
</body>
</html>