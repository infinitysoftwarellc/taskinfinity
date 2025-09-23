{{-- resources/views/livewire/task-manager.blade.php --}}
<div>
    {{-- ... (código do botão Adicionar Tarefa e título) ... --}}

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <ul class="divide-y divide-gray-200">
            @forelse ($tasks as $task)
                <li class="p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center hover:bg-gray-50" wire:key="task-{{ $task->id }}">
                    <div class="flex items-center mb-2 sm:mb-0 flex-grow">
                        <input type="checkbox"
                               class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                               wire:click="toggleCompleted({{ $task->id }})"
                               @if($task->completed_at) checked @endif>
                        <div class="ml-3">
                            <p class="text-lg font-semibold {{ $task->completed_at ? 'text-gray-500 line-through' : 'text-gray-900' }}">
                                {{ $task->title }}
                            </p>
                            
                            {{-- INÍCIO DA EXIBIÇÃO DAS TAGS --}}
                            <div class="flex flex-wrap gap-1 mt-1">
                                @foreach($task->tags as $tag)
                                    <span class="inline-block bg-gray-200 rounded-full px-2 py-1 text-xs font-semibold text-gray-700">
                                        {{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>
                            {{-- FIM DA EXIBIÇÃO DAS TAGS --}}

                            @if($task->due_date)
                                <span class="text-sm text-gray-500 block mt-1">
                                    Vencimento: {{ \Carbon\Carbon::parse($task->due_date)->format('d/m/Y') }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="space-x-2 self-end sm:self-center flex-shrink-0">
                        <button wire:click="showEditModal({{ $task->id }})" class="text-blue-500 hover:text-blue-700 font-medium">Editar</button>
                        <button wire:click="confirmDelete({{ $task->id }})" class="text-red-500 hover:text-red-700 font-medium">Deletar</button>
                    </div>
                </li>
            @empty
                <li class="p-4 text-center text-gray-500">
                    Nenhuma tarefa encontrada.
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
                {{-- Campos title, description, due_date ... --}}

                {{-- INÍCIO DOS CAMPOS DE TAG --}}
                <hr class="my-4">
                
                {{-- Seletor de Tags Existentes --}}
                <div>
                    <x-input-label for="tags" value="Tags" />
                    <div class="flex flex-wrap gap-2 p-2 border rounded-md mt-1">
                        @forelse($availableTags as $tag)
                            <label class="inline-flex items-center">
                                <input type="checkbox" wire:model="selectedTags" value="{{ $tag->id }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-600">{{ $tag->name }}</span>
                            </label>
                        @empty
                             <span class="text-sm text-gray-500">Nenhuma tag criada ainda.</span>
                        @endforelse
                    </div>
                     <x-input-error :messages="$errors->get('selectedTags')" class="mt-2" />
                </div>
                
                {{-- Campo para Criar Nova Tag --}}
                <div>
                    <x-input-label for="newTag" value="Ou crie uma nova tag" />
                     <div class="flex items-center mt-1">
                        <x-text-input wire:model.defer="newTag" id="newTag" class="block w-full" type="text" placeholder="Nome da nova tag" wire:keydown.enter.prevent="addNewTag" />
                        <x-primary-button type="button" wire:click="addNewTag" class="ml-2">Adicionar</x-primary-button>
                    </div>
                    <x-input-error :messages="$errors->get('newTag')" class="mt-2" />
                </div>
                {{-- FIM DOS CAMPOS DE TAG --}}

            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button type="button" x-on:click="$dispatch('close')">
                    Cancelar
                </x-secondary-button>
                <x-primary-button class="ml-3">
                    Salvar Tarefa
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    {{-- ... (Modal de confirmação de exclusão) ... --}}
</div>