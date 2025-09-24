<div>
    // resources/views/livewire/tasks-page.blade.php
<div>
    <h1 class="text-3xl font-bold">{{ $pageTitle }}</h1>

    {{-- NEW TASK FORM --}}
    <form wire:submit.prevent="saveTask" class="mt-6">
        <input 
            type="text" 
            wire:model="newTaskDescription" 
            placeholder="Add a new task..."
            class="w-full bg-gray-900/80 border-gray-700 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
        >
    </form>
    {{-- END NEW TASK FORM --}}

    <div class="mt-8">
        {{-- Aqui você vai iterar e mostrar as tarefas --}}
        @forelse ($tasks as $task)
            <div class="bg-gray-800/50 p-4 rounded-lg mb-4">
                <p>{{ $task->description }}</p>
                @if ($task->due_date)
                    <span class="text-sm text-gray-400">Vence em: {{ $task->due_date->format('d/m/Y') }}</span>
                @endif
            </div>
        @empty
            <div class="bg-gray-800/50 p-4 rounded-lg text-center mt-4">
                <p>Nenhuma tarefa encontrada.</p>
            </div>
        @endforelse
    </div>
</div>
    <h1 class="text-3xl font-bold">{{ $pageTitle }}</h1>

    <div class="mt-8">
        {{-- Aqui você vai iterar e mostrar as tarefas --}}
        @forelse ($tasks as $task)
            <div class="bg-gray-800/50 p-4 rounded-lg mb-4">
                <p>{{ $task->description }}</p>
                @if ($task->due_date)
                    <span class="text-sm text-gray-400">Vence em: {{ $task->due_date->format('d/m/Y') }}</span>
                @endif
            </div>
        @empty
            <div class="bg-gray-800/50 p-4 rounded-lg text-center">
                <p>Nenhuma tarefa encontrada.</p>
            </div>
        @endforelse
    </div>
</div>