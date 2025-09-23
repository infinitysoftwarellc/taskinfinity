<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Criar Nova Tarefa em: <span class="font-bold">{{ $taskList->name }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('webapp.tasks.store') }}">
                        @csrf
                        <input type="hidden" name="task_list_id" value="{{ $taskList->id }}">

                        <div>
                            <x-input-label for="title" :value="__('Título da Tarefa')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="description" :value="__('Descrição (Opcional)')" />
                            <textarea id="description" name="description" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div>
                                <x-input-label for="due_date" :value="__('Data de Vencimento')" />
                                <x-text-input id="due_date" class="block mt-1 w-full" type="date" name="due_date" :value="old('due_date')" />
                            </div>

                            <div>
                                <x-input-label for="priority" :value="__('Prioridade')" />
                                <select name="priority" id="priority" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="low" @selected(old('priority') == 'low')>Baixa</option>
                                    <option value="medium" @selected(old('priority', 'medium') == 'medium')>Média</option>
                                    <option value="high" @selected(old('priority') == 'high')>Alta</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('webapp.tasklists.show', $taskList) }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancelar</a>
                            <x-primary-button>
                                {{ __('Salvar Tarefa') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>