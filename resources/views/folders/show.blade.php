<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <a href="{{ route('webapp.folders.index') }}" class="text-indigo-600 hover:text-indigo-900">Minhas Pastas</a>
                    <span class="text-gray-500 mx-2">/</span>
                    {{ $folder->name }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">{{ $folder->description }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Listas de Tarefas</h3>
                        <a href="{{ route('webapp.tasklists.index', $folder) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Gerenciar Listas
                        </a>
                    </div>
                     <p>Clique em "Gerenciar Listas" para ver, adicionar, editar ou remover listas de tarefas desta pasta.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>