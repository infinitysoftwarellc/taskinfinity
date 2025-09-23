<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <a href="{{ route('webapp.folders.index') }}" class="text-indigo-600 hover:text-indigo-900">Minhas Pastas</a>
                    <span class="text-gray-500 mx-2">/</span>
                    <a href="{{ route('webapp.folders.show', $tasklist->folder) }}" class="text-indigo-600 hover:text-indigo-900">{{ $tasklist->folder->name }}</a>
                    <span class="text-gray-500 mx-2">/</span>
                    {{ $tasklist->name }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded relative" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Tarefas</h3>
                        <a href="{{ route('webapp.tasks.create', ['task_list_id' => $tasklist->id]) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Nova Tarefa
                        </a>
                    </div>

                    <ul class="space-y-4">
                        @forelse ($tasklist->tasks as $task)
                            <li class="flex justify-between items-center p-4 border rounded-lg">
                                <div>
                                    <span class="text-lg font-semibold">{{ $task->title }}</span>
                                    <p class="text-sm text-gray-500">{{ $task->description }}</p>
                                    <div class="text-xs text-gray-500 mt-1">
                                        <span>Prioridade: {{ $task->priority }}</span> | <span>Status: {{ $task->status }}</span>
                                        @if($task->due_date)
                                        | <span>Vencimento: {{ $task->due_date->format('d/m/Y') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('webapp.tasks.edit', $task) }}" class="text-sm text-gray-600 hover:text-gray-900">Editar</a>
                                    <form action="{{ route('webapp.tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja deletar esta tarefa?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:text-red-900">Deletar</button>
                                    </form>
                                </div>
                            </li>
                        @empty
                            <p>Esta lista ainda não tem nenhuma tarefa. <a href="{{ route('webapp.tasks.create', ['task_list_id' => $tasklist->id]) }}" class="text-indigo-600 hover:underline">Crie uma agora!</a></p>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>