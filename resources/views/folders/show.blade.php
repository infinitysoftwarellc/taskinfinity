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
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded relative" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Listas de Tarefas</h3>
                        <a href="{{ route('webapp.tasklists.create', ['folder_id' => $folder->id]) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Nova Lista
                        </a>
                    </div>

                    <ul class="space-y-4">
                        @forelse ($folder->taskLists as $taskList)
                            <li class="flex justify-between items-center p-4 border rounded-lg">
                                <div>
                                    <a href="{{ route('webapp.tasklists.show', $taskList) }}" class="text-lg font-semibold text-indigo-600 hover:text-indigo-800">{{ $taskList->name }}</a>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('webapp.tasklists.edit', $taskList) }}" class="text-sm text-gray-600 hover:text-gray-900">Editar</a>
                                    <form action="{{ route('webapp.tasklists.destroy', $taskList) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja deletar esta lista?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:text-red-900">Deletar</button>
                                    </form>
                                </div>
                            </li>
                        @empty
                            <p>Esta pasta ainda não tem nenhuma lista de tarefas. <a href="{{ route('webapp.tasklists.create', ['folder_id' => $folder->id]) }}" class="text-indigo-600 hover:underline">Crie uma agora!</a></p>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>