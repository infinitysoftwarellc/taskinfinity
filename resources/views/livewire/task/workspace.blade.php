<section class="space-y-6">
    @if ($viewPayload)
        @php
            $totalViewTasks = $viewPayload['slug'] === 'all'
                ? $viewPayload['lists']->sum(fn ($list) => $list['tasks']->count())
                : $viewPayload['tasks']->count();
        @endphp

        <header class="flex flex-col justify-between gap-4 rounded-3xl border border-[#212832] bg-[#13171c]/85 p-6 text-sm backdrop-blur md:flex-row md:items-center">
            <div>
                <p class="text-xs uppercase tracking-wider text-[#8b96a5]">{{ $totalViewTasks }} tarefas</p>
                <h1 class="text-2xl font-semibold text-[#e8eef5]">{{ $viewPayload['title'] }}</h1>
                <p class="mt-1 text-xs text-[#8b96a5]">{{ $viewPayload['description'] }}</p>
            </div>
            <div class="flex flex-1 flex-col gap-3 md:flex-row md:items-center md:justify-end">
                <div class="relative w-full md:max-w-xs">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#4c5664]">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 1 0 0-15 7.5 7.5 0 0 0 0 15z" />
                        </svg>
                    </span>
                    <input type="search" placeholder="Pesquisar tarefas" wire:model.debounce.400ms="search"
                        class="w-full rounded-2xl border border-[#212832] bg-[#0d1116]/80 py-2 pl-9 pr-4 text-sm text-[#e8eef5] placeholder-white/40 focus:border-[#3b82f6] focus:outline-none focus:ring-0" />
                </div>
                <p class="rounded-2xl border border-[#212832] bg-[#151a21]/80 px-4 py-2 text-xs text-[#8b96a5]">
                    Use a busca para filtrar tarefas por título ou descrição.
                </p>
            </div>
        </header>

        <div class="space-y-6 rounded-3xl border border-[#212832] bg-[#13171c]/85 p-6 backdrop-blur">
            @if ($viewPayload['slug'] === 'all')
                @forelse ($viewPayload['lists'] as $viewList)
                    <div class="space-y-3">
                        <div class="flex flex-col gap-1 text-[#e8eef5] sm:flex-row sm:items-center sm:justify-between">
                            <h2 class="text-lg font-semibold">{{ $viewList['name'] }}</h2>
                            <span class="text-xs uppercase tracking-wide text-[#6c7684]">{{ $viewList['tasks']->count() }} tarefas</span>
                        </div>
                        <div class="space-y-4">
                            @foreach ($viewList['tasks'] as $task)
                                <livewire:task.item :task="$task" :depth="$task->depth ?? 0" :key="'task-item-view-' . $viewList['id'] . '-' . $task->id" />
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-[#212832] bg-[#151a21]/80 p-6 text-sm text-[#8b96a5]">
                        Nenhuma tarefa encontrada. Crie tarefas nas suas listas para vê-las aqui.
                    </div>
                @endforelse
            @else
                <div class="space-y-4">
                    @forelse ($viewPayload['tasks'] as $task)
                        <div class="space-y-2">
                            @if ($task->relationLoaded('list') && $task->list)
                                <span class="inline-flex items-center gap-2 rounded-full border border-[#212832] bg-[#0d1116]/80 px-3 py-1 text-[11px] uppercase tracking-wide text-[#8b96a5]">
                                    {{ $task->list->name }}
                                </span>
                            @endif
                            <livewire:task.item :task="$task" :depth="$task->depth ?? 0" :key="'task-item-view-' . $task->id" />
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-[#212832] bg-[#151a21]/80 p-6 text-sm text-[#8b96a5]">
                            @if ($viewPayload['slug'] === 'today')
                                Nenhuma tarefa com prazo para hoje.
                            @else
                                Nenhuma tarefa com prazo para os próximos 7 dias.
                            @endif
                        </div>
                    @endforelse
                </div>
            @endif
        </div>
    @elseif ($list)
        <header class="flex flex-col justify-between gap-4 rounded-3xl border border-[#212832] bg-[#13171c]/85 p-6 text-sm backdrop-blur md:flex-row md:items-center">
            <div>
                <p class="text-xs uppercase tracking-wider text-[#8b96a5]">{{ $list->tasks_count }} tarefas</p>
                <h1 class="text-2xl font-semibold text-[#e8eef5]">{{ $list->name }}</h1>
            </div>
            <div class="flex flex-1 flex-col gap-3 md:flex-row md:items-center md:justify-end">
                <div class="relative w-full md:max-w-xs">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#4c5664]">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 1 0 0-15 7.5 7.5 0 0 0 0 15z" />
                        </svg>
                    </span>
                    <input type="search" placeholder="Pesquisar tarefas" wire:model.debounce.400ms="search"
                        class="w-full rounded-2xl border border-[#212832] bg-[#0d1116]/80 py-2 pl-9 pr-4 text-sm text-[#e8eef5] placeholder-white/40 focus:border-[#3b82f6] focus:outline-none focus:ring-0" />
                </div>
                <p class="rounded-2xl border border-[#212832] bg-[#151a21]/80 px-4 py-2 text-xs text-[#8b96a5]">
                    <span class="font-semibold text-[#e8eef5]">Dica:</span> pressione <kbd class="rounded bg-white/10 px-1">Shift</kbd> + <kbd class="rounded bg-white/10 px-1">Enter</kbd> no nome da tarefa para criar uma subtarefa instantaneamente.
                </p>
            </div>
        </header>

        <div class="space-y-5 rounded-3xl border border-[#212832] bg-[#13171c]/85 p-6 backdrop-blur">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <label for="new-task-title" class="sr-only">Nome da tarefa</label>
                <div class="flex w-full items-center gap-2 rounded-2xl border border-[#212832] bg-[#151a21]/80 px-4 py-2">
                    <svg class="h-4 w-4 text-[#6c7684]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <input id="new-task-title" type="text" placeholder="+ Adicionar tarefa"
                        wire:model.defer="newTaskTitle" wire:keydown.enter.prevent="createRootTask"
                        class="w-full bg-transparent text-sm text-[#e8eef5] placeholder-white/40 focus:outline-none focus:ring-0" />
                </div>
                <button type="button" wire:click="createRootTask"
                    class="inline-flex items-center justify-center rounded-2xl bg-[#3b82f6] px-4 py-2 text-xs font-semibold uppercase tracking-wide text-[#e8eef5] transition hover:bg-[#2563eb]">
                    Adicionar
                </button>
            </div>
            @error('newTaskTitle')
                <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
            @enderror

            <div class="space-y-3">
                @forelse ($tasks as $task)
                    <livewire:task.item :task="$task" :depth="$task->depth ?? 0" :key="'task-item-' . $task->id" />
                @empty
                    <div class="rounded-2xl border border-dashed border-[#212832] bg-[#151a21]/80 p-6 text-sm text-[#8b96a5]">
                        Nenhuma tarefa cadastrada ainda. Crie a primeira tarefa no campo acima.
                    </div>
                @endforelse
            </div>
        </div>
    @else
        <div class="rounded-3xl border border-dashed border-[#212832] bg-[#0d1116]/80 p-10 text-center text-sm text-[#a1acba]">
            <h2 class="text-xl font-semibold text-[#e8eef5]">Comece criando uma lista</h2>
            <p class="mt-2 text-[#8b96a5]">Use o botão “+ New” na barra lateral para criar listas e organizar suas tarefas sem recarregar a página.</p>
        </div>
    @endif

    @if ($showEditor && $editorTask)
        <div class="fixed inset-0 z-50 flex items-start justify-end bg-black/60 p-0 backdrop-blur-sm">
            <div class="relative flex h-full w-full max-w-3xl flex-col gap-6 overflow-y-auto bg-zinc-950/95 p-8 text-sm text-[#e8eef5] shadow-2xl">
                <button type="button" wire:click="closeEditor"
                    class="absolute right-6 top-6 flex h-10 w-10 items-center justify-center rounded-full border border-[#212832] text-[#a1acba] transition hover:border-[#3b82f6] hover:text-[#e8eef5]">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m6 6 12 12M6 18 18 6" />
                    </svg>
                </button>

                <header class="pr-12">
                    <p class="text-xs uppercase tracking-wider text-[#6c7684]">Editor avançado</p>
                    <h2 class="mt-1 text-2xl font-semibold text-[#e8eef5]">{{ $editorTask->title }}</h2>
                    <p class="mt-1 text-xs text-[#6c7684]">Edite todos os detalhes da tarefa sem sair da página.</p>
                </header>

                <form wire:submit.prevent="saveEditor" class="space-y-6">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label class="text-xs font-semibold uppercase tracking-wide text-[#8b96a5]">Nome</label>
                            <input type="text" wire:model.defer="editorForm.title"
                                class="mt-1 w-full rounded-2xl border border-[#212832] bg-[#0d1116]/80 px-4 py-2 text-sm text-[#e8eef5] focus:border-[#3b82f6]/60 focus:outline-none focus:ring-0" />
                            @error('editorForm.title')
                                <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-[#8b96a5]">Status</label>
                            <select wire:model="editorForm.status"
                                class="mt-1 w-full rounded-2xl border border-[#212832] bg-[#0d1116]/80 px-4 py-2 text-sm text-[#e8eef5] focus:border-[#3b82f6]/60 focus:outline-none focus:ring-0">
                                @foreach ($statusOptions as $option)
                                    <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                @endforeach
                            </select>
                            @error('editorForm.status')
                                <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-[#8b96a5]">Prioridade</label>
                            <select wire:model="editorForm.priority"
                                class="mt-1 w-full rounded-2xl border border-[#212832] bg-[#0d1116]/80 px-4 py-2 text-sm text-[#e8eef5] focus:border-[#3b82f6]/60 focus:outline-none focus:ring-0">
                                @foreach ($priorityOptions as $option)
                                    <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                @endforeach
                            </select>
                            @error('editorForm.priority')
                                <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-[#8b96a5]">Prazo</label>
                            <input type="datetime-local" wire:model="editorForm.due_at"
                                class="mt-1 w-full rounded-2xl border border-[#212832] bg-[#0d1116]/80 px-4 py-2 text-sm text-[#e8eef5] focus:border-[#3b82f6]/60 focus:outline-none focus:ring-0" />
                            @error('editorForm.due_at')
                                <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-[#8b96a5]">Estimativa de pomodoros</label>
                            <input type="number" min="0" wire:model="editorForm.estimate_pomodoros"
                                class="mt-1 w-full rounded-2xl border border-[#212832] bg-[#0d1116]/80 px-4 py-2 text-sm text-[#e8eef5] focus:border-[#3b82f6]/60 focus:outline-none focus:ring-0" />
                            @error('editorForm.estimate_pomodoros')
                                <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wide text-[#8b96a5]">Pomodoros concluídos</label>
                        <input type="number" min="0" wire:model="editorForm.pomodoros_done"
                            class="mt-1 w-full rounded-2xl border border-[#212832] bg-[#0d1116]/80 px-4 py-2 text-sm text-[#e8eef5] focus:border-[#3b82f6]/60 focus:outline-none focus:ring-0" />
                        @error('editorForm.pomodoros_done')
                            <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-semibold uppercase tracking-wide text-[#8b96a5]">Tags</label>
                        <span class="text-[11px] uppercase tracking-wide text-[#4c5664]">Opcional</span>
                    </div>

                    @if (empty($availableTags))
                        <p class="rounded-2xl border border-dashed border-[#212832] bg-[#0d1116]/80 p-4 text-xs text-[#8b96a5]">
                            Crie sua primeira tag abaixo para categorizar tarefas por contexto, prioridade ou cliente.
                        </p>
                    @else
                        <div class="flex flex-wrap gap-2">
                            @foreach ($availableTags as $tag)
                                <label for="editor-tag-{{ $tag['id'] }}"
                                    class="inline-flex items-center gap-2 rounded-full border border-[#212832]/70 bg-[#151a21]/80 px-3 py-1.5 text-[11px] font-semibold uppercase tracking-wide text-[#a1acba] transition hover:border-[#3b82f6] hover:text-[#e8eef5]">
                                    <input id="editor-tag-{{ $tag['id'] }}" type="checkbox" value="{{ $tag['id'] }}"
                                        wire:model="editorTagIds"
                                        class="h-3.5 w-3.5 rounded border-[#212832] bg-[#0d1116]/60 text-[#3b82f6] focus:ring-[#3b82f6]/60" />
                                    <span class="flex items-center gap-2">
                                        <span class="h-2.5 w-2.5 rounded-full" style="background-color: {{ $tag['color'] }}"></span>
                                        {{ $tag['name'] }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    @endif

                    <div class="grid gap-3 md:grid-cols-[minmax(0,1fr)_120px]">
                        <div>
                            <label class="text-[11px] font-semibold uppercase tracking-wide text-[#6c7684]">Nova tag</label>
                            <input type="text" wire:model.defer="newTagName" placeholder="ex: Cliente X"
                                class="mt-1 w-full rounded-2xl border border-[#212832] bg-[#0d1116]/80 px-4 py-2 text-sm text-[#e8eef5] placeholder-white/40 focus:border-[#3b82f6]/60 focus:outline-none focus:ring-0" />
                            @error('newTagName')
                                <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-[11px] font-semibold uppercase tracking-wide text-[#6c7684]">Cor</label>
                            <div class="mt-1 flex items-center gap-3 rounded-2xl border border-[#212832] bg-[#0d1116]/80 px-3 py-2">
                                <input type="color" wire:model="newTagColor" class="h-9 w-9 cursor-pointer rounded border-none bg-transparent p-0"
                                    title="Escolha a cor da tag" />
                                <span class="text-xs uppercase tracking-wide text-[#8b96a5]">{{ strtoupper($newTagColor) }}</span>
                            </div>
                            @error('newTagColor')
                                <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="button" wire:click="createTag"
                            class="inline-flex items-center gap-2 rounded-2xl border border-[#212832] bg-[#3b82f6]/80 px-4 py-2 text-[11px] font-semibold uppercase tracking-wide text-[#e8eef5] shadow-lg shadow-[#3b82f6]/20 transition hover:bg-[#2563eb]"
                            wire:loading.attr="disabled" wire:target="createTag">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            <span wire:loading.remove wire:target="createTag">Adicionar tag</span>
                            <span wire:loading wire:target="createTag">Salvando...</span>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-semibold uppercase tracking-wide text-[#8b96a5]">Descrição</label>
                    <textarea rows="6" wire:model.defer="editorForm.description"
                        class="mt-1 w-full rounded-2xl border border-[#212832] bg-[#0d1116]/80 px-4 py-3 text-sm text-[#e8eef5] placeholder-white/40 focus:border-[#3b82f6]/60 focus:outline-none focus:ring-0"></textarea>
                    @error('editorForm.description')
                            <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <button type="button" wire:click="closeEditor"
                            class="rounded-2xl border border-[#212832] bg-[#0d1116]/80 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-[#a1acba] transition hover:border-[#3b82f6] hover:text-[#e8eef5]">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="rounded-2xl bg-[#3b82f6] px-4 py-2 text-xs font-semibold uppercase tracking-wide text-[#e8eef5] shadow-lg shadow-[#3b82f6]/30 transition hover:bg-[#2563eb]">
                            Salvar alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</section>
