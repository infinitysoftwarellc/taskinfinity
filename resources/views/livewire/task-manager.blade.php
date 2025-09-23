<div>
    {{-- Cabeçalho com Título da Lista e Botão de Adicionar Tarefa --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">{{ $taskList->name }}</h2>
        <x-primary-button wire:click="showCreateModal">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Adicionar Tarefa
        </x-primary-button>
    </div>

    {{-- Container da Lista de Tarefas --}}
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <ul class="divide-y divide-gray-200">
            @forelse ($tasks as $task)
                <li class="p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center hover:bg-gray-50 transition-colors duration-200" wire:key="task-{{ $task->id }}">
                    {{-- Seção Principal da Tarefa (Checkbox, Título, Tags, Data) --}}
                    <div class="flex items-center mb-2 sm:mb-0 flex-grow">
                        <input type="checkbox"
                               class="h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer"
                               wire:click="toggleCompleted({{ $task->id }})"
                               @if($task->completed_at) checked @endif>
                        <div class="ml-4">
                            <p class="text-lg font-semibold {{ $task->completed_at ? 'text-gray-400 line-through' : 'text-gray-900' }}">
                                {{ $task->title }}
                            </p>
                            <div class="flex flex-wrap gap-2 mt-1">
                                @foreach($task->tags as $tag)
                                    <span class="inline-block bg-gray-200 rounded-full px-2 py-1 text-xs font-semibold text-gray-700">
                                        {{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>
                            @if($task->due_date)
                                <span class="text-sm text-gray-500 block mt-1">
                                    Vencimento: {{ $task->due_date->format('d/m/Y') }}
                                </span>
                            @endif
                        </div>
                    </div>
                    {{-- Botões de Ação --}}
                    <div class="space-x-3 self-end sm:self-center flex-shrink-0">
                        <button wire:click="showEditModal({{ $task->id }})" class="font-medium text-indigo-600 hover:text-indigo-800">Editar</button>
                        <button wire:click="confirmDelete({{ $task->id }})" class="font-medium text-red-600 hover:text-red-800">Deletar</button>
                    </div>
                </li>
            @empty
                <li class="p-6 text-center text-gray-500">
                    Nenhuma tarefa foi adicionada a esta lista ainda.
                </li>
            @endforelse
        </ul>
    </div>

    {{-- Paginação --}}
    @if($tasks->hasPages())
        <div class="mt-6">
            {{ $tasks->links() }}
        </div>
    @endif

    {{-- Modal de criação/edição de Tarefa --}}
    <x-modal name="task-modal" maxWidth="2xl" focusable>
        <form wire:submit.prevent="save" class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ $editingTask ? 'Editar Tarefa' : 'Criar Nova Tarefa' }}
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                {{ $editingTask ? 'Altere os detalhes da sua tarefa.' : 'Preencha os campos para adicionar uma nova tarefa.' }}
            </p>

            <div class="mt-6 space-y-6">
                {{-- Campos do Formulário da Tarefa --}}
                <div>
                    <x-input-label for="title" value="Título" />
                    <x-text-input wire:model="title" id="title" class="block mt-1 w-full" type="text" placeholder="Ex: Fazer compras" />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="description" value="Descrição" />
                    <textarea wire:model="description" id="description" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Detalhes da tarefa..."></textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="due_date" value="Data de Vencimento" />
                    <x-text-input wire:model="due_date" id="due_date" class="block mt-1 w-full" type="date" />
                    <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
                </div>
                
                <hr class="pt-2">
                
                {{-- Seção de Tags --}}
                <div>
                    <x-input-label for="tags" value="Tags" />
                    <div class="flex flex-wrap gap-3 p-2 border rounded-md mt-1 max-h-32 overflow-y-auto">
                        @forelse($availableTags as $tag)
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="selectedTags" value="{{ $tag->id }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-600">{{ $tag->name }}</span>
                            </label>
                        @empty
                             <span class="text-sm text-gray-500">Nenhuma tag criada ainda.</span>
                        @endforelse
                    </div>
                     <x-input-error :messages="$errors->get('selectedTags')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="newTag" value="Ou crie e adicione uma nova tag" />
                     <div class="flex items-center mt-1">
                        <x-text-input wire:model.defer="newTag" id="newTag" class="block w-full" type="text" placeholder="Nome da nova tag" wire:keydown.enter.prevent="addNewTag" />
                        <x-primary-button type="button" wire:click="addNewTag" class="ml-2 flex-shrink-0">
                            Adicionar
                        </x-primary-button>
                    </div>
                    <x-input-error :messages="$errors->get('newTag')" class="mt-2" />
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                {{-- BOTÃO CORRIGIDO: Agora usa wire:click para chamar o método do backend --}}
                <x-secondary-button type="button" wire:click="closeModal">
                    Cancelar
                </x-secondary-button>

                <x-primary-button class="ml-3">
                    Salvar
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    {{-- Modal de confirmação de exclusão --}}
    <x-modal name="confirm-task-deletion" focusable>
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                Tem certeza que deseja deletar esta tarefa?
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                Uma vez que a tarefa for deletada, todos os seus dados serão permanentemente removidos.
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