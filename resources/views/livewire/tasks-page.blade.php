<div>
    <h1 class="text-3xl font-bold text-white">{{ $pageTitle }}</h1>

    <form wire:submit="saveTask" class="mt-6">
        <input 
            type="text" 
            wire:model.live="newTaskDescription" {{-- Use .live para feedback instantâneo --}}
            placeholder="Add a new task in {{ $pageTitle }}..."
            class="w-full bg-gray-800 border-2 border-gray-700 rounded-lg text-white focus:ring-indigo-500 focus:border-indigo-500"
        >
    </form>

    <div class="mt-8 space-y-4">
        @forelse ($tasks as $task)
            <div class="bg-gray-800/80 p-4 rounded-lg border border-gray-700">
                <p class="text-gray-200">{{ $task->description }}</p>
                @if ($task->due_date)
                    <span class="text-xs text-gray-400">Vence em: {{ $task->due_date->format('d/m/Y') }}</span>
                @endif
            </div>
        @empty
            <div class="bg-gray-800/50 p-6 rounded-lg text-center border border-dashed border-gray-700">
                <p class="text-gray-400">Nenhuma tarefa aqui. Que tal adicionar uma?</p>
            </div>
        @endforelse
    </div>
</div>