<div>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Suas Pastas</h2>
        <x-primary-button wire:click="showCreateModal">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Adicionar Pasta
        </x-primary-button>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <ul class="divide-y divide-gray-200">
            @forelse ($folders as $folder)
                <li class="p-4 flex justify-between items-center hover:bg-gray-50" wire:key="folder-{{ $folder->id }}">
                    <div>
                        <a href="{{ route('webapp.folders.show', $folder) }}" class="text-lg font-semibold text-indigo-600 hover:text-indigo-800">{{ $folder->name }}</a>
                        <p class="text-sm text-gray-500">
                            Contém {{ $folder->taskLists()->count() }} lista(s) de tarefas
                        </p>
                    </div>
                    <div class="space-x-2">
                        <button wire:click="showEditModal({{ $folder->id }})" class="text-blue-500 hover:text-blue-700 font-medium">Renomear</button>
                        <button wire:click="confirmDelete({{ $folder->id }})" class="text-red-500 hover:text-red-700 font-medium">Deletar</button>
                    </div>
                </li>
            @empty
                <li class="p-4 text-center text-gray-500">
                    Você ainda não tem nenhuma pasta. Que tal criar uma?
                </li>
            @endforelse
        </ul>
    </div>

    <div class="mt-4">
        {{ $folders->links() }}
    </div>

    {{-- Modal de criação/edição --}}
    <x-modal name="folder-modal" maxWidth="2xl" focusable>
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ $editingFolder ? 'Renomear Pasta' : 'Criar Nova Pasta' }}
            </h2>

            <div class="mt-4">
                <x-input-label for="name" value="Nome da Pasta" class="sr-only" />
                <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" placeholder="Nome da Pasta" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button wire:click="closeModal">
                    Cancelar
                </x-secondary-button>
                <x-primary-button class="ml-3" wire:click="save">
                    Salvar
                </x-primary-button>
            </div>
        </div>
    </x-modal>

    {{-- Modal de confirmação de exclusão --}}
    <x-modal name="confirm-folder-deletion" maxWidth="lg" focusable>
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                Tem certeza?
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                Você realmente deseja deletar esta pasta? Todas as listas e tarefas dentro dela serão perdidas.
            </p>
            <div class="mt-6 flex justify-end">
                <x-secondary-button wire:click="closeModal">
                    Cancelar
                </x-secondary-button>
                <x-danger-button class="ml-3" wire:click="delete">
                    Sim, Deletar
                </x-danger-button>
            </div>
        </div>
    </x-modal>
</div>
