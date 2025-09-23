<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Minhas Pastas') }}
            </h2>
            <a href="{{ route('webapp.folders.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Nova Pasta
            </a>
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
                    <ul class="space-y-4">
                        @forelse ($folders as $folder)
                            <li class="flex justify-between items-center p-4 border rounded-lg">
                                <div>
                                    <a href="{{ route('webapp.folders.show', $folder) }}" class="text-lg font-semibold text-indigo-600 hover:text-indigo-800">{{ $folder->name }}</a>
                                    <p class="text-sm text-gray-500">{{ $folder->description }}</p>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('webapp.folders.edit', $folder) }}" class="text-sm text-gray-600 hover:text-gray-900">Editar</a>
                                    <form action="{{ route('webapp.folders.destroy', $folder) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja deletar esta pasta?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:text-red-900">Deletar</button>
                                    </form>
                                </div>
                            </li>
                        @empty
                            <p>Você ainda não tem nenhuma pasta. <a href="{{ route('webapp.folders.create') }}" class="text-indigo-600 hover:underline">Crie uma agora!</a></p>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>