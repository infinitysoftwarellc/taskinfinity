<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Lista de Tarefas: ') . $tasklist->name }}
            </h2>
            <div class="flex items-center space-x-2">
                 <a href="{{ route('webapp.folders.show', $tasklist->folder_id) }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50">
                    Voltar para a Pasta
                </a>
                <a href="{{ route('webapp.tasklists.edit', $tasklist) }}" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                    Editar Nome
                </a>
                <form action="{{ route('webapp.tasklists.destroy', $tasklist) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja deletar esta lista de tarefas?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-opacity-50">
                        Deletar Lista
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    {{-- Seção para Adicionar Nova Tarefa --}}
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-2">Adicionar Nova Tarefa</h3>
                        {{-- O formulário para adicionar tarefas irá aqui --}}
                        <p class="text-gray-500">Funcionalidade de adição de tarefas a ser implementada.</p>
                    </div>

                    {{-- Seção para Listar Tarefas Existentes --}}
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Tarefas</h3>
                        <div class="space-y-4">
                            @forelse ($tasklist->tasks as $task)
                                <div class="p-4 border rounded-md">
                                    <p>{{ $task->name }}</p>
                                    {{-- Adicione mais detalhes da tarefa se necessário --}}
                                </div>
                            @empty
                                <p>Nenhuma tarefa encontrada nesta lista.</p>
                            @endforelse
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>