<div>
    @if ($open)
        <div class="ti-modal-backdrop" wire:click="closeModal">
            <div class="ti-modal" wire:click.stop>
                <form class="ti-modal-form" wire:submit.prevent="save">
                    <header class="ti-modal-header">
                        <div>
                            <h3>Nova subtarefa</h3>
                            <p>Crie uma subtarefa para organizar melhor sua tarefa principal.</p>
                        </div>
                        <button class="ti-modal-close" type="button" wire:click="closeModal">
                            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                            <span class="sr-only">Fechar</span>
                        </button>
                    </header>

                    <div class="ti-modal-body">
                        <div class="ti-modal-row">
                            <label class="ti-modal-label" for="subtask-title">Título</label>
                            <input
                                id="subtask-title"
                                type="text"
                                class="ti-modal-control"
                                placeholder="Descreva a subtarefa"
                                wire:model.defer="title"
                                autofocus
                            />
                            @error('title')
                                <p class="ti-modal-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="ti-modal-row">
                            <label class="ti-modal-label" for="subtask-parent">Adicionar dentro de</label>
                            <select
                                id="subtask-parent"
                                class="ti-modal-control"
                                wire:model="parentId"
                            >
                                <option value="">Sem pai (nível raiz)</option>
                                @foreach ($parentOptions as $id => $label)
                                    <option value="{{ $id }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="ti-modal-hint">
                            Você pode criar até {{ $maxSubtasks }} subtarefas por nível.
                        </div>
                    </div>

                    <footer class="ti-modal-footer">
                        <button type="button" class="ghost" wire:click="closeModal">Cancelar</button>
                        <button type="submit" class="primary">Adicionar</button>
                    </footer>
                </form>
            </div>
        </div>
    @endif
</div>
