<div class="space-y-4" style="margin-left: {{ max(0, $depth) * 1.5 }}rem;">
    <article class="rounded-2xl border border-white/10 bg-black/40 p-5 text-sm text-white shadow-inner shadow-black/20">
        <div class="flex flex-col gap-4">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                <div class="flex flex-1 flex-col gap-2">
                    <label class="text-xs font-semibold uppercase tracking-wide text-white/50">Nome da tarefa</label>
                    <input type="text" wire:model.debounce.400ms="title" wire:keydown.enter.prevent="saveTitle"
                        wire:keydown.shift.enter.prevent="quickSubtask"
                        class="w-full rounded-2xl border border-white/10 bg-black/30 px-4 py-2 text-sm text-white placeholder-white/40 focus:border-indigo-400/60 focus:outline-none focus:ring-0"
                        placeholder="Descreva rapidamente o que precisa ser feito" />
                    @error('title')
                        <p class="text-xs text-rose-300">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" wire:click="openEditor"
                        class="flex items-center gap-2 rounded-2xl border border-white/10 bg-white/5 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-white/80 transition hover:border-indigo-400/60 hover:text-white">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897z" />
                        </svg>
                        Abrir editor
                    </button>
                    <button type="button" wire:click="deleteTask"
                        class="flex items-center gap-2 rounded-2xl border border-white/10 bg-white/5 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-rose-200/80 transition hover:border-rose-400/60 hover:text-rose-100"
                        onclick="confirm('Tem certeza que deseja excluir esta tarefa e todas as subtarefas?') || event.stopImmediatePropagation()">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Excluir
                    </button>
                </div>
            </div>

            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                <label class="flex flex-col gap-1">
                    <span class="text-xs font-semibold uppercase tracking-wide text-white/50">Status</span>
                    <select wire:model="status"
                        class="rounded-2xl border border-white/10 bg-black/30 px-4 py-2 text-sm text-white focus:border-indigo-400/60 focus:outline-none focus:ring-0">
                        @foreach ($statusOptions as $option)
                            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="text-xs text-rose-300">{{ $message }}</p>
                    @enderror
                </label>

                <label class="flex flex-col gap-1">
                    <span class="text-xs font-semibold uppercase tracking-wide text-white/50">Prioridade</span>
                    <select wire:model="priority"
                        class="rounded-2xl border border-white/10 bg-black/30 px-4 py-2 text-sm text-white focus:border-indigo-400/60 focus:outline-none focus:ring-0">
                        @foreach ($priorityOptions as $option)
                            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                        @endforeach
                    </select>
                    @error('priority')
                        <p class="text-xs text-rose-300">{{ $message }}</p>
                    @enderror
                </label>

                <label class="flex flex-col gap-2">
                    <span class="text-xs font-semibold uppercase tracking-wide text-white/50">Prazo</span>
                    <div class="flex flex-col gap-2">
                        <div class="flex flex-col gap-2 sm:flex-row">
                            <input type="date" wire:model="dueDate"
                                class="flex-1 rounded-2xl border border-white/10 bg-black/30 px-4 py-2 text-sm text-white focus:border-indigo-400/60 focus:outline-none focus:ring-0"
                                placeholder="Selecione a data" />
                            <input type="time" wire:model="dueTime"
                                class="flex-1 rounded-2xl border border-white/10 bg-black/30 px-4 py-2 text-sm text-white focus:border-indigo-400/60 focus:outline-none focus:ring-0"
                                placeholder="Horário (opcional)" />
                        </div>
                        <p class="text-xs text-white/40">Escolha apenas a data ou adicione um horário se desejar.</p>
                    </div>
                    @error('dueDate')
                        <p class="text-xs text-rose-300">{{ $message }}</p>
                    @enderror
                    @error('dueTime')
                        <p class="text-xs text-rose-300">{{ $message }}</p>
                    @enderror
                </label>

                <div class="grid grid-cols-2 gap-2">
                    <label class="flex flex-col gap-1">
                        <span class="text-xs font-semibold uppercase tracking-wide text-white/50">Estimativa</span>
                        <input type="number" min="0" wire:model.debounce.500ms="estimatePomodoros"
                            class="rounded-2xl border border-white/10 bg-black/30 px-4 py-2 text-sm text-white focus:border-indigo-400/60 focus:outline-none focus:ring-0" />
                        @error('estimatePomodoros')
                            <p class="text-xs text-rose-300">{{ $message }}</p>
                        @enderror
                    </label>
                    <label class="flex flex-col gap-1">
                        <span class="text-xs font-semibold uppercase tracking-wide text-white/50">Feitos</span>
                        <input type="number" min="0" wire:model.debounce.500ms="pomodorosDone"
                            class="rounded-2xl border border-white/10 bg-black/30 px-4 py-2 text-sm text-white focus:border-indigo-400/60 focus:outline-none focus:ring-0" />
                        @error('pomodorosDone')
                            <p class="text-xs text-rose-300">{{ $message }}</p>
                        @enderror
                    </label>
                </div>
            </div>

            <div>
                <label class="text-xs font-semibold uppercase tracking-wide text-white/50">Descrição</label>
                <textarea rows="3" wire:model.debounce.800ms="description"
                    class="mt-1 w-full rounded-2xl border border-white/10 bg-black/30 px-4 py-3 text-sm text-white placeholder-white/40 focus:border-indigo-400/60 focus:outline-none focus:ring-0"
                    placeholder="Detalhe os passos da tarefa, cole links ou anotações"></textarea>
                @error('description')
                    <p class="text-xs text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-wrap items-center gap-3 border-t border-white/10 pt-4">
                <button type="button" wire:click="toggleSubtaskForm"
                    class="flex items-center gap-2 rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white/80 transition hover:border-indigo-400/60 hover:text-white">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Adicionar subtarefa
                </button>
                <span class="text-xs text-white/50">Atalho: Shift + Enter cria uma subtarefa instantânea.</span>
            </div>

            @if ($showSubtaskForm)
                <div class="flex flex-col gap-2 rounded-2xl border border-dashed border-white/10 bg-black/30 p-4">
                    <label class="text-xs font-semibold uppercase tracking-wide text-white/50">Nome da subtarefa</label>
                    <div class="flex items-center gap-2">
                        <input type="text" wire:model.defer="subtaskTitle" wire:keydown.enter.prevent="createSubtask"
                            class="w-full rounded-2xl border border-white/10 bg-black/40 px-4 py-2 text-sm text-white placeholder-white/40 focus:border-indigo-400/60 focus:outline-none focus:ring-0"
                            placeholder="Digite o nome da subtarefa" />
                        <button type="button" wire:click="createSubtask"
                            class="rounded-2xl bg-indigo-500 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white shadow-lg shadow-indigo-500/30 transition hover:bg-indigo-400">
                            Criar subtarefa
                        </button>
                    </div>
                    @error('subtaskTitle')
                        <p class="text-xs text-rose-300">{{ $message }}</p>
                    @enderror
                </div>
            @endif
        </div>
    </article>

    @if ($task->childrenRecursive->isNotEmpty())
        <div class="space-y-4 border-l border-white/10 pl-6">
            @foreach ($task->childrenRecursive as $child)
                <livewire:task.item :task="$child" :depth="$depth + 1" :key="'task-item-' . $child->id" />
            @endforeach
        </div>
    @endif
</div>
