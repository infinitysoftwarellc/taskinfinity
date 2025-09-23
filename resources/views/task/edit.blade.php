<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Tarefa: ') . $task->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('webapp.tasks.update', $task) }}">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="title" :value="__('Título da Tarefa')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $task->title)" required autofocus />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="description" :value="__('Descrição (Opcional)')" />
                            <textarea id="description" name="description" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('description', $task->description) }}</textarea>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-4 mt-4">
                            <div>
                                <x-input-label for="due_date" :value="__('Data de Vencimento')" />
                                <x-text-input id="due_date" class="block mt-1 w-full" type="date" name="due_date" :value="old('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : '')" />
                            </div>

                            <div>
                                <x-input-label for="priority" :value="__('Prioridade')" />
                                <select name="priority" id="priority" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="low" @selected(old('priority', $task->priority) == 'low')>Baixa</option>
                                    <option value="medium" @selected(old('priority', $task->priority) == 'medium')>Média</option>
                                    <option value="high" @selected(old('priority', $task->priority) == 'high')>Alta</option>
                                </select>
                            </div>

                            <div>
                                <x-input-label for="status" :value="__('Status')" />
                                <select name="status" id="status" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="todo" @selected(old('status', $task->status) == 'todo')>A Fazer</option>
                                    <option value="in_progress" @selected(old('status', $task->status) == 'in_progress')>Em Progresso</option>
                                    <option value="done" @selected(old('status', $task->status) == 'done')>Concluída</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('webapp.tasklists.show', $task->taskList) }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancelar</a>
                            <x-primary-button>
                                {{ __('Atualizar Tarefa') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>