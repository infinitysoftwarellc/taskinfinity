<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <a href="{{ route('webapp.folders.show', $tasklist->folder->id) }}" class="text-indigo-600 hover:text-indigo-900">
                Pasta: {{ $tasklist->folder->name }}
            </a>
            <span class="text-gray-500 mx-2">/</span>
            {{ $tasklist->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- A mágica acontece aqui! --}}
            <livewire:task-manager :task-list="$tasklist" />
        </div>
    </div>
</x-app-layout>