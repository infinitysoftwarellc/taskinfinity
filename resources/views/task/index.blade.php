<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu com Rolagem Oculta</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        /* Oculta a barra de rolagem para navegadores baseados em WebKit (Chrome, Safari, Edge) */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        /* Oculta a barra de rolagem para Firefox */
        .no-scrollbar {
            -ms-overflow-style: none;  /* IE e Edge */
            scrollbar-width: none;  /* Firefox */
        }
    </style>
</head>
<body class="bg-gray-900 text-gray-200 font-sans">

    <div class="flex h-screen">

        <aside class="w-16 bg-gray-800 flex flex-col items-center py-4 border-r border-gray-700">
            <div class="mb-6">
                <div class="w-10 h-10 bg-gray-600 rounded-full flex items-center justify-center text-xl relative">
                    <i class="fas fa-user"></i>
                    <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-yellow-400 ring-2 ring-gray-800"></span>
                </div>
            </div>
            <nav class="flex flex-col items-center space-y-6">
                <a href="#" class="text-gray-400 hover:text-gray-100 p-2 rounded-md transition-colors duration-200">
                    <i class="fas fa-check-square text-2xl"></i>
                </a>
                <a href="#" class="text-gray-400 hover:text-gray-100 p-2 rounded-md transition-colors duration-200">
                    <i class="fas fa-chart-pie text-2xl"></i>
                </a>
                <a href="#" class="text-gray-400 hover:text-gray-100 p-2 rounded-md transition-colors duration-200">
                    <i class="fas fa-history text-2xl"></i>
                </a>
                <a href="#" class="text-gray-400 hover:text-gray-100 p-2 rounded-md transition-colors duration-200">
                    <i class="fas fa-star text-2xl"></i>
                </a>
                <a href="#" class="text-gray-400 hover:text-gray-100 p-2 rounded-md transition-colors duration-200">
                    <i class="fas fa-search text-2xl"></i>
                </a>
            </nav>
            <div class="mt-auto flex flex-col items-center space-y-6 mb-4">
                <a href="#" class="text-gray-400 hover:text-gray-100 p-2 rounded-md transition-colors duration-200">
                    <i class="fas fa-sync-alt text-2xl"></i>
                </a>
                <a href="#" class="text-gray-400 hover:text-gray-100 p-2 rounded-md transition-colors duration-200">
                    <i class="fas fa-bell text-2xl"></i>
                </a>
                <a href="#" class="text-gray-400 hover:text-gray-100 p-2 rounded-md transition-colors duration-200">
                    <i class="fas fa-question-circle text-2xl"></i>
                </a>
            </div>
        </aside>

        <div class="w-64 bg-gray-800 border-r border-gray-700 p-4 overflow-y-auto no-scrollbar">
            <nav class="space-y-2">
                <a href="#" class="flex items-center p-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
                    <i class="fas fa-folder mr-3 text-gray-400"></i>
                    <span>All</span>
                    <span class="ml-auto text-gray-400 text-sm">21</span>
                </a>
                <a href="#" class="flex items-center p-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
                    <i class="fas fa-calendar-day mr-3 text-gray-400"></i>
                    <span>Today</span>
                    <span class="ml-auto text-gray-400 text-sm">24</span>
                </a>
                <a href="#" class="flex items-center p-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
                    <i class="fas fa-calendar-week mr-3 text-gray-400"></i>
                    <span>Next 7 Days</span>
                    <span class="ml-auto text-gray-400 text-sm">11</span>
                </a>
                <a href="#" class="flex items-center p-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
                    <i class="fas fa-inbox mr-3 text-gray-400"></i>
                    <span>Inbox</span>
                </a>
                <a href="#" class="flex items-center p-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
                    <i class="fas fa-stream mr-3 text-gray-400"></i>
                    <span>Summary</span>
                </a>
            </nav>

            <div class="mt-8 mb-4 text-gray-500 text-sm uppercase tracking-wide">
                Lists
            </div>
            <nav class="space-y-2">
                <a href="#" class="flex items-center p-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
                    <i class="fas fa-stream mr-3 text-gray-400"></i>
                    <span>s</span>
                </a>
                <div class="relative">
                    <button class="w-full flex items-center p-2 rounded-md bg-gray-700 text-gray-100 hover:bg-gray-700 transition-colors duration-200 font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                        <i class="fas fa-folder mr-3 text-gray-300"></i>
                        <span>SOFTWAREINFINITY</span>
                        <span class="ml-auto text-gray-300 text-sm">21</span>
                    </button>
                    <div class="ml-8 mt-2 space-y-2">
                        <a href="#" class="flex items-center p-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
                            <i class="fas fa-stream mr-3 text-gray-400"></i>
                            <span>teste</span>
                        </a>
                        <a href="#" class="flex items-center p-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
                            <i class="fas fa-stream mr-3 text-gray-400"></i>
                            <span>Task Infinity</span>
                            <span class="ml-auto text-gray-400 text-sm">15</span>
                        </a>
                        <a href="#" class="flex items-center p-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
                            <i class="fas fa-stream mr-3 text-gray-400"></i>
                            <span>PORTFOLIO</span>
                            <span class="ml-auto text-gray-400 text-sm">6</span>
                        </a>
                    </div>
                </div>
                <a href="#" class="flex items-center p-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
                    <i class="fas fa-folder mr-3 text-blue-400"></i>
                    <span>Projetos Pessoais</span>
                    <span class="ml-auto text-gray-400 text-sm">8</span>
                </a>
                <a href="#" class="flex items-center p-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
                    <i class="fas fa-folder mr-3 text-green-400"></i>
                    <span>Trabalho</span>
                    <span class="ml-auto text-gray-400 text-sm">42</span>
                </a>
                <a href="#" class="flex items-center p-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
                    <i class="fas fa-folder mr-3 text-purple-400"></i>
                    <span>Estudos</span>
                    <span class="ml-auto text-gray-400 text-sm">18</span>
                </a>
                </nav>

            <div class="mt-8 mb-4 text-gray-500 text-sm uppercase tracking-wide">
                Filters
            </div>
            <div class="p-2 text-gray-400 text-sm">
                Display tasks filtered by list, date, priority, tag, and more
            </div>

            <div class="mt-8 mb-4 text-gray-500 text-sm uppercase tracking-wide">
                Tags
            </div>
            <div class="flex flex-wrap gap-2 p-2">
                <span class="px-2 py-1 bg-blue-600 text-white text-xs rounded-full">urgente</span>
                <span class="px-2 py-1 bg-red-600 text-white text-xs rounded-full">importante</span>
                <span class="px-2 py-1 bg-yellow-500 text-gray-900 text-xs rounded-full">reunião</span>
                <span class="px-2 py-1 bg-green-600 text-white text-xs rounded-full">email</span>
                <span class="px-2 py-1 bg-indigo-600 text-white text-xs rounded-full">design</span>
                <span class="px-2 py-1 bg-pink-600 text-white text-xs rounded-full">docs</span>
            </div>
            </div>

        <main class="flex-1 p-8 overflow-y-auto">
            <h1 class="text-3xl font-bold">Main Content Area</h1>
            <p class="mt-4">Este conteúdo principal também pode rolar independentemente da barra lateral.</p>
            <div class="h-screen bg-gray-800/50 mt-8 rounded-lg"></div>
            <div class="h-screen bg-gray-800/50 mt-8 rounded-lg"></div>
        </main>

    </div>

</body>
</html>