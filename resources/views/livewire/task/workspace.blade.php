<section class="space-y-6">
    @if ($viewPayload)
        @php
            $totalViewTasks = $viewPayload['slug'] === 'all'
                ? $viewPayload['lists']->sum(fn ($list) => $list['tasks']->count())
                : $viewPayload['tasks']->count();
        @endphp

        <header class="flex flex-col justify-between gap-4 rounded-3xl border border-white/5 bg-white/5 p-6 text-sm backdrop-blur md:flex-row md:items-center">
            <div>
                <p class="text-xs uppercase tracking-wider text-white/60">{{ $totalViewTasks }} tarefas</p>
                <h1 class="text-2xl font-semibold text-white">{{ $viewPayload['title'] }}</h1>
                <p class="mt-1 text-xs text-white/60">{{ $viewPayload['description'] }}</p>
            </div>
            <div class="flex flex-1 flex-col gap-3 md:flex-row md:items-center md:justify-end">
                <div class="relative w-full md:max-w-xs">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/40">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 1 0 0-15 7.5 7.5 0 0 0 0 15z" />
                        </svg>
                    </span>
                    <input type="search" placeholder="Pesquisar tarefas" wire:model.debounce.400ms="search"
                        class="w-full rounded-2xl border border-white/10 bg-black/40 py-2 pl-9 pr-4 text-sm text-white placeholder-white/40 focus:border-white/30 focus:outline-none focus:ring-0" />
                </div>
                <p class="rounded-2xl border border-white/10 bg-black/30 px-4 py-2 text-xs text-white/60">
                    Use a busca para filtrar tarefas por título ou descrição.
                </p>
            </div>
        </header>

        <div class="space-y-6 rounded-3xl border border-white/5 bg-white/5 p-6 backdrop-blur">
            @if ($viewPayload['slug'] === 'all')
                @forelse ($viewPayload['lists'] as $viewList)
                    <div class="space-y-3">
                        <div class="flex flex-col gap-1 text-white sm:flex-row sm:items-center sm:justify-between">
                            <h2 class="text-lg font-semibold">{{ $viewList['name'] }}</h2>
                            <span class="text-xs uppercase tracking-wide text-white/50">{{ $viewList['tasks']->count() }} tarefas</span>
                        </div>
                        <div class="space-y-4">
                            @foreach ($viewList['tasks'] as $task)
                                <livewire:task.item :task="$task" :depth="$task->depth ?? 0" :key="'task-item-view-' . $viewList['id'] . '-' . $task->id" />
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-white/10 bg-black/30 p-6 text-sm text-white/60">
                        Nenhuma tarefa encontrada. Crie tarefas nas suas listas para vê-las aqui.
                    </div>
                @endforelse
            @else
                <div class="space-y-4">
                    @forelse ($viewPayload['tasks'] as $task)
                        <div class="space-y-2">
                            @if ($task->relationLoaded('list') && $task->list)
                                <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-black/40 px-3 py-1 text-[11px] uppercase tracking-wide text-white/60">
                                    {{ $task->list->name }}
                                </span>
                            @endif
                            <livewire:task.item :task="$task" :depth="$task->depth ?? 0" :key="'task-item-view-' . $task->id" />
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-white/10 bg-black/30 p-6 text-sm text-white/60">
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
        <header class="flex flex-col justify-between gap-4 rounded-3xl border border-white/5 bg-white/5 p-6 text-sm backdrop-blur md:flex-row md:items-center">
            <div>
                <p class="text-xs uppercase tracking-wider text-white/60">{{ $list->tasks_count }} tarefas</p>
                <h1 class="text-2xl font-semibold text-white">{{ $list->name }}</h1>
            </div>
            <div class="flex flex-1 flex-col gap-3 md:flex-row md:items-center md:justify-end">
                <div class="relative w-full md:max-w-xs">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/40">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 1 0 0-15 7.5 7.5 0 0 0 0 15z" />
                        </svg>
                    </span>
                    <input type="search" placeholder="Pesquisar tarefas" wire:model.debounce.400ms="search"
                        class="w-full rounded-2xl border border-white/10 bg-black/40 py-2 pl-9 pr-4 text-sm text-white placeholder-white/40 focus:border-white/30 focus:outline-none focus:ring-0" />
                </div>
                <p class="rounded-2xl border border-white/10 bg-black/30 px-4 py-2 text-xs text-white/60">
                    <span class="font-semibold text-white">Dica:</span> pressione <kbd class="rounded bg-white/10 px-1">Shift</kbd> + <kbd class="rounded bg-white/10 px-1">Enter</kbd> no nome da tarefa para criar uma subtarefa instantaneamente.
                </p>
            </div>
        </header>

        <div class="space-y-6 rounded-3xl border border-white/5 bg-white/5 p-6 backdrop-blur">
            <div>
                <label for="new-task-title" class="block text-xs font-semibold uppercase tracking-wide text-white/60">Criar tarefa rapidamente</label>
                <div class="mt-2 flex items-center gap-3">
                    <input id="new-task-title" type="text" placeholder="Digite o nome da tarefa e pressione Enter"
                        wire:model.defer="newTaskTitle" wire:keydown.enter.prevent="createRootTask"
                        class="w-full rounded-2xl border border-white/10 bg-black/40 px-4 py-2 text-sm text-white placeholder-white/40 focus:border-indigo-400/60 focus:outline-none focus:ring-0" />
                    <button type="button" wire:click="createRootTask"
                        class="hidden rounded-2xl bg-indigo-500 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white shadow-lg shadow-indigo-500/30 transition hover:bg-indigo-400 sm:flex">
                        Criar
                    </button>
                </div>
                @error('newTaskTitle')
                    <p class="mt-2 text-xs text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-4">
                @forelse ($tasks as $task)
                    <livewire:task.item :task="$task" :depth="$task->depth ?? 0" :key="'task-item-' . $task->id" />
                @empty
                    <div class="rounded-2xl border border-dashed border-white/10 bg-black/30 p-6 text-sm text-white/60">
                        Nenhuma tarefa cadastrada nesta lista ainda. Comece criando uma tarefa no campo acima e organize subtarefas com até 7 níveis de hierarquia.
                    </div>
                @endforelse
            </div>
        </div>
    @else
        <div class="rounded-3xl border border-dashed border-white/10 bg-black/40 p-10 text-center text-sm text-white/70">
            <h2 class="text-xl font-semibold text-white">Comece criando uma lista</h2>
            <p class="mt-2 text-white/60">Use o botão “+ New” na barra lateral para criar listas e organizar suas tarefas sem recarregar a página.</p>
        </div>
    @endif

    @if ($showEditor && $editorTask)
        <div class="fixed inset-0 z-50 flex items-start justify-end bg-black/60 p-0 backdrop-blur-sm">
            <div class="relative flex h-full w-full max-w-3xl flex-col gap-6 overflow-y-auto bg-zinc-950/95 p-8 text-sm text-white shadow-2xl">
                <button type="button" wire:click="closeEditor"
                    class="absolute right-6 top-6 flex h-10 w-10 items-center justify-center rounded-full border border-white/10 text-white/70 transition hover:border-white/30 hover:text-white">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m6 6 12 12M6 18 18 6" />
                    </svg>
                </button>

                <header class="pr-12">
                    <p class="text-xs uppercase tracking-wider text-white/50">Editor avançado</p>
                    <h2 class="mt-1 text-2xl font-semibold text-white">{{ $editorTask->title }}</h2>
                    <p class="mt-1 text-xs text-white/50">Edite todos os detalhes da tarefa sem sair da página.</p>
                </header>

                <form wire:submit.prevent="saveEditor" class="space-y-6">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label class="text-xs font-semibold uppercase tracking-wide text-white/60">Nome</label>
                            <input type="text" wire:model.defer="editorForm.title"
                                class="mt-1 w-full rounded-2xl border border-white/10 bg-black/40 px-4 py-2 text-sm text-white focus:border-indigo-400/60 focus:outline-none focus:ring-0" />
                            @error('editorForm.title')
                                <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-white/60">Status</label>
                            <select wire:model="editorForm.status"
                                class="mt-1 w-full rounded-2xl border border-white/10 bg-black/40 px-4 py-2 text-sm text-white focus:border-indigo-400/60 focus:outline-none focus:ring-0">
                                @foreach ($statusOptions as $option)
                                    <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                @endforeach
                            </select>
                            @error('editorForm.status')
                                <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-white/60">Prioridade</label>
                            <select wire:model="editorForm.priority"
                                class="mt-1 w-full rounded-2xl border border-white/10 bg-black/40 px-4 py-2 text-sm text-white focus:border-indigo-400/60 focus:outline-none focus:ring-0">
                                @foreach ($priorityOptions as $option)
                                    <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                @endforeach
                            </select>
                            @error('editorForm.priority')
                                <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-white/60">Prazo</label>
                            <input type="datetime-local" wire:model="editorForm.due_at"
                                class="mt-1 w-full rounded-2xl border border-white/10 bg-black/40 px-4 py-2 text-sm text-white focus:border-indigo-400/60 focus:outline-none focus:ring-0" />
                            @error('editorForm.due_at')
                                <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-white/60">Estimativa de pomodoros</label>
                            <input type="number" min="0" wire:model="editorForm.estimate_pomodoros"
                                class="mt-1 w-full rounded-2xl border border-white/10 bg-black/40 px-4 py-2 text-sm text-white focus:border-indigo-400/60 focus:outline-none focus:ring-0" />
                            @error('editorForm.estimate_pomodoros')
                                <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-white/60">Pomodoros concluídos</label>
                            <input type="number" min="0" wire:model="editorForm.pomodoros_done"
                                class="mt-1 w-full rounded-2xl border border-white/10 bg-black/40 px-4 py-2 text-sm text-white focus:border-indigo-400/60 focus:outline-none focus:ring-0" />
                            @error('editorForm.pomodoros_done')
                                <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wide text-white/60">Descrição</label>
                        <textarea rows="6" wire:model.defer="editorForm.description"
                            class="mt-1 w-full rounded-2xl border border-white/10 bg-black/40 px-4 py-3 text-sm text-white placeholder-white/40 focus:border-indigo-400/60 focus:outline-none focus:ring-0"></textarea>
                        @error('editorForm.description')
                            <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <button type="button" wire:click="closeEditor"
                            class="rounded-2xl border border-white/10 bg-black/40 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white/70 transition hover:border-white/30 hover:text-white">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="rounded-2xl bg-indigo-500 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white shadow-lg shadow-indigo-500/30 transition hover:bg-indigo-400">
                            Salvar alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</section>
