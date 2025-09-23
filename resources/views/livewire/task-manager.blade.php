<div>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Tarefas</h2>
        <x-primary-button wire:click="showCreateModal">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Adicionar Tarefa
        </x-primary-button>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <ul class="divide-y divide-gray-200">
            @forelse ($tasks as $task)
                <li class="p-4 flex justify-between items-center hover:bg-gray-50" wire:key="task-{{ $task->id }}">
                    <div class="flex items-center">
                        <input type="checkbox"
                               class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                               wire:click="toggleCompleted({{ $task->id }})"
                               @if($task->completed) checked @endif>
                        <div class="ml-3">
                            <p class="text-lg font-semibold {{ $task->completed ? 'text-gray-500 line-through' : 'text-gray-900' }}">
                                {{ $task->title }}
                            </p>
                            @if($task->due_date)
                                <span class="text-sm text-gray-500">
                                    Vencimento: {{ $task->due_date->format('d/m/Y') }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="space-x-2">
                        <button wire:click="showEditModal({{ $task->id }})" class="text-blue-500 hover:text-blue-700 font-medium">Editar</button>
                        <button wire:click="confirmDelete({{ $task->id }})" class="text-red-500 hover:text-red-700 font-medium">Deletar</button>
                    </div>
                </li>
            @empty
                <li class="p-4 text-center text-gray-500">
                    Nenhuma tarefa por aqui. Adicione uma nova!
                </li>
            @endforelse
        </ul>
    </div>

    <div class="mt-4">
        {{ $tasks->links() }}
    </div>

    {{-- Modal de criação/edição de Tarefa --}}
    <x-modal name="task-modal" maxWidth="2xl" focusable>
        <form wire:submit.prevent="save" class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ $editingTask ? 'Editar Tarefa' : 'Criar Nova Tarefa' }}
            </h2>

            <div class="mt-4 space-y-4">
                <div>
                    <x-input-label for="title" value="Título" />
                    <x-text-input wire:model="title" id="title" class="block mt-1 w-full" type="text" />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="description" value="Descrição (Opcional)" />
                    <textarea wire:model="description" id="description" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="due_date" value="Data de Vencimento (Opcional)" />
                    <x-text-input wire:model="due_date" id="due_date" class="block mt-1 w-full" type="date" />
                    <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button type="button" wire:click="closeModal">
                    Cancelar
                </x-secondary-button>
                <x-primary-button class="ml-3">
                    Salvar Tarefa
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    {{-- Modal de confirmação de exclusão --}}
    <x-modal name="confirm-task-deletion" maxWidth="lg" focusable>
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                Tem certeza que deseja deletar?
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                Esta ação não poderá ser desfeita.
            </p>
            <div class="mt-6 flex justify-end">
                <x-secondary-button wire:click="closeModal">
                    Cancelar
                </x-secondary-button>
                <x-danger-button class="ml-3" wire:click="delete">
                    Deletar Tarefa
                </x-danger-button>
            </div>
        </div>
    </x-modal>
</div>