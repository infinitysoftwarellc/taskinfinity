<aside class="relative flex flex-col gap-8 rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur">
    @if (session()->has('task_lists.created'))
        <div class="rounded-2xl border border-emerald-400/40 bg-emerald-500/10 p-3 text-xs text-emerald-200">
            {{ session('task_lists.created') }}
        </div>
    @endif

    <div>
        <p class="text-xs font-semibold uppercase tracking-widest text-white/50">Views</p>
        <ul class="mt-4 space-y-2">
            @foreach ($views as $view)
                <li>
                    <button type="button" wire:click="openView('{{ $view['slug'] }}')"
                        class="flex w-full items-center justify-between rounded-2xl px-4 py-3 text-left text-sm font-medium transition {{ $activeView === $view['slug'] ? 'bg-indigo-500/20 text-white' : 'bg-white/10 text-white/80 hover:bg-white/15' }}">
                        <span class="flex items-center gap-3">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-black/40 text-white/70">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="{{ $view['icon'] }}" />
                                </svg>
                            </span>
                            {{ $view['label'] }}
                        </span>
                        <span class="rounded-full bg-white/10 px-2 py-0.5 text-xs font-semibold text-white/70">
                            {{ $view['count'] }}
                        </span>
                    </button>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="h-px bg-white/10"></div>

    <div>
        <div class="mb-3 flex items-center justify-between text-xs font-semibold uppercase tracking-widest text-white/50">
            <span>Lists</span>
            <button type="button" wire:click="$set('showCreateList', true)"
                class="rounded-full bg-white/10 px-3 py-1 text-[10px] font-semibold text-white/70 transition hover:bg-white/20">
                + New
            </button>
        </div>
        @if ($lists->isEmpty())
            <p class="rounded-2xl border border-dashed border-white/10 bg-black/40 p-4 text-xs text-white/60">
                Crie sua primeira lista para organizar suas tarefas.
            </p>
        @else
            <ul class="space-y-2 text-sm">
                @foreach ($lists as $list)
                    <li wire:key="task-list-{{ $list->id }}">
                        <button type="button" wire:click="openList({{ $list->id }})"
                            class="flex w-full items-center justify-between rounded-2xl px-3 py-2 text-left text-white/80 transition hover:bg-white/10 {{ $activeListId === $list->id ? 'bg-white/10 text-white' : '' }}">
                            <span class="font-medium uppercase tracking-wide">{{ $list->name }}</span>
                            <span class="text-xs text-white/50">{{ $list->tasks_count }}</span>
                        </button>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="h-px bg-white/10"></div>

    <div>
        <p class="text-xs font-semibold uppercase tracking-widest text-white/50">Filters</p>
        <p class="mt-3 rounded-2xl bg-black/30 p-4 text-xs text-white/60">
            {{ $filtersDescription }}
        </p>
    </div>

    <div class="h-px bg-white/10"></div>

    <div>
        <p class="text-xs font-semibold uppercase tracking-widest text-white/50">Tags</p>
        @if (empty($tags))
            <p class="mt-3 rounded-2xl border border-dashed border-white/10 bg-black/40 p-4 text-xs text-white/60">
                Crie tags ao editar uma tarefa para organizá-las por contexto.
            </p>
        @else
            <ul class="mt-3 space-y-2 text-sm text-white/80">
                @foreach ($tags as $tag)
                    <li class="flex items-center justify-between gap-3 rounded-2xl px-3 py-2 transition hover:bg-white/10">
                        <span class="flex items-center gap-3 uppercase tracking-wide">
                            <span class="h-2.5 w-2.5 rounded-full" style="background-color: {{ $tag['color'] }}"></span>
                            {{ $tag['label'] }}
                        </span>
                        <span class="text-[11px] font-semibold uppercase tracking-wide text-white/40">
                            {{ $tag['tasks_count'] }}
                        </span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    @if ($showCreateList)
        <div class="absolute inset-0 z-20 flex items-center justify-center">
            <div class="fixed inset-0 bg-black/60" wire:click="$set('showCreateList', false)"></div>
            <div class="relative w-full max-w-sm rounded-3xl border border-white/10 bg-gray-900/95 p-6 text-sm shadow-2xl">
                <button type="button" wire:click="$set('showCreateList', false)"
                    class="absolute right-4 top-4 rounded-full bg-white/10 p-1 text-white/60 transition hover:bg-white/20 hover:text-white">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <h2 class="text-lg font-semibold text-white">Nova lista</h2>
                <p class="mt-1 text-xs text-white/60">Adicione uma nova lista sem recarregar a página.</p>

                <form wire:submit.prevent="createList" class="mt-4 space-y-4">
                    <div>
                        <label for="list-name" class="block text-xs font-medium uppercase tracking-wide text-white/60">Nome</label>
                        <input id="list-name" type="text" wire:model.defer="form.name"
                            class="mt-2 w-full rounded-2xl border border-white/10 bg-black/40 px-3 py-2 text-sm text-white placeholder-white/40 focus:border-indigo-400 focus:outline-none focus:ring-0"
                            placeholder="ex: Projetos" autocomplete="off" />
                        @error('form.name')
                            <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="list-view-mode" class="block text-xs font-medium uppercase tracking-wide text-white/60">Visualização</label>
                        <select id="list-view-mode" wire:model.defer="form.view_mode"
                            class="mt-2 w-full rounded-2xl border border-white/10 bg-black/40 px-3 py-2 text-sm text-white focus:border-indigo-400 focus:outline-none focus:ring-0">
                            <option value="list">Lista</option>
                            <option value="kanban">Kanban</option>
                            <option value="timeline">Linha do tempo</option>
                        </select>
                        @error('form.view_mode')
                            <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="$set('showCreateList', false)"
                            class="rounded-2xl border border-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white/60 transition hover:border-white/30 hover:text-white">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="flex items-center gap-2 rounded-2xl bg-indigo-500 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white shadow-lg shadow-indigo-500/30 transition hover:bg-indigo-400"
                            wire:loading.attr="disabled">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            <span wire:loading.remove>Criar lista</span>
                            <span wire:loading>Salvando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</aside>
