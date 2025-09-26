<aside class="relative flex h-full flex-col overflow-hidden rounded-[18px] border border-[#212832] bg-[#13171c]/85 text-[#e8eef5] shadow-xl shadow-black/40">
    <header class="flex items-center justify-between border-b border-[#212832] px-5 py-4">
        <span class="text-sm font-semibold tracking-wide">Listas</span>
        <button type="button" wire:click="$set('showCreateList', true)"
            class="rounded-full border border-[#212832] bg-[#151a21] px-3 py-1 text-[10px] font-semibold uppercase tracking-wide text-[#8b96a5] transition hover:border-[#3b82f6] hover:text-white">
            + Nova
        </button>
    </header>

    @if (session()->has('task_lists.created'))
        <div class="mx-5 mt-4 rounded-[14px] border border-emerald-400/40 bg-emerald-500/15 px-4 py-3 text-xs text-emerald-200">
            {{ session('task_lists.created') }}
        </div>
    @endif

    <div class="flex-1 overflow-y-auto px-5 py-5" style="scrollbar-width: thin;">
        <section class="space-y-3">
            <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-[#8b96a5]">Visões</p>
            <ul class="space-y-2 text-sm">
                @foreach ($views as $view)
                    <li>
                        <button type="button" wire:click="openView('{{ $view['slug'] }}')"
                            class="flex w-full items-center justify-between rounded-[14px] border border-transparent bg-[#151a21] px-4 py-3 text-left font-medium text-[#8b96a5] transition hover:border-[#212832] hover:text-white {{ $activeView === $view['slug'] ? 'border-[#3b82f6]/60 text-white shadow-lg shadow-[#3b82f6]/40' : '' }}">
                            <span class="flex items-center gap-3">
                                <span class="flex h-9 w-9 items-center justify-center rounded-[12px] bg-[#0d1116] text-[#8b96a5]">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="{{ $view['icon'] }}" />
                                    </svg>
                                </span>
                                {{ $view['label'] }}
                            </span>
                            <span class="rounded-full border border-[#212832] bg-[#0d1116] px-2 py-0.5 text-xs font-semibold text-[#8b96a5]">
                                {{ $view['count'] }}
                            </span>
                        </button>
                    </li>
                @endforeach
            </ul>
        </section>

        <div class="my-6 h-px bg-[#212832]"></div>

        <section class="space-y-3">
            <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-[#8b96a5]">Listas</p>
            @if ($lists->isEmpty())
                <p class="rounded-[14px] border border-dashed border-[#212832] bg-[#0d1116] p-4 text-xs text-[#8b96a5]">
                    Crie sua primeira lista para organizar suas tarefas.
                </p>
            @else
                <ul class="space-y-2 text-sm">
                    @foreach ($lists as $list)
                        <li wire:key="task-list-{{ $list->id }}">
                            <button type="button" wire:click="openList({{ $list->id }})"
                                class="flex w-full items-center justify-between rounded-[12px] border border-transparent px-3 py-2 text-left text-[#8b96a5] transition hover:border-[#212832] hover:bg-[#151a21] hover:text-white {{ $activeListId === $list->id ? 'border-[#3b82f6]/60 bg-[#151a21] text-white shadow-lg shadow-[#3b82f6]/40' : '' }}">
                                <span class="font-medium uppercase tracking-wide">{{ $list->name }}</span>
                                <span class="text-xs text-[#6c7684]">{{ $list->tasks_count }}</span>
                            </button>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <div class="my-6 h-px bg-[#212832]"></div>

        <section class="space-y-3">
            <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-[#8b96a5]">Filtros</p>
            <p class="rounded-[14px] border border-[#212832] bg-[#0d1116] p-4 text-xs text-[#8b96a5]">
                {{ $filtersDescription }}
            </p>
        </section>

        <div class="my-6 h-px bg-[#212832]"></div>

        <section class="space-y-3">
            <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-[#8b96a5]">Tags</p>
            @if (empty($tags))
                <p class="rounded-[14px] border border-dashed border-[#212832] bg-[#0d1116] p-4 text-xs text-[#8b96a5]">
                    Crie tags ao editar uma tarefa para organizá-las por contexto.
                </p>
            @else
                <ul class="space-y-2 text-sm text-[#8b96a5]">
                    @foreach ($tags as $tag)
                        <li class="flex items-center justify-between gap-3 rounded-[12px] border border-transparent px-3 py-2 transition hover:border-[#212832] hover:bg-[#151a21] hover:text-white">
                            <span class="flex items-center gap-3 uppercase tracking-wide">
                                <span class="h-2.5 w-2.5 rounded-full" style="background-color: {{ $tag['color'] }}"></span>
                                {{ $tag['label'] }}
                            </span>
                            <span class="text-[11px] font-semibold uppercase tracking-wide text-[#6c7684]">
                                {{ $tag['tasks_count'] }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>

    <footer class="flex items-center justify-between border-t border-[#212832] px-5 py-4 text-xs text-[#8b96a5]">
        <span class="flex items-center gap-2">⚙️ <span class="uppercase tracking-wide">Configurações</span></span>
        <span class="uppercase tracking-wide">Dark</span>
    </footer>

    @if ($showCreateList)
        <div class="absolute inset-0 z-20 flex items-center justify-center">
            <div class="fixed inset-0 bg-black/60" wire:click="$set('showCreateList', false)"></div>
            <div class="relative w-full max-w-sm rounded-[20px] border border-[#212832] bg-[#0f1216]/95 p-6 text-sm text-[#e8eef5] shadow-2xl shadow-black/60">
                <button type="button" wire:click="$set('showCreateList', false)"
                    class="absolute right-4 top-4 rounded-full border border-transparent bg-[#151a21] p-1 text-[#8b96a5] transition hover:border-[#212832] hover:text-white">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <h2 class="text-lg font-semibold text-white">Nova lista</h2>
                <p class="mt-1 text-xs text-[#8b96a5]">Adicione uma nova lista sem recarregar a página.</p>

                <form wire:submit.prevent="createList" class="mt-4 space-y-4">
                    <div>
                        <label for="list-name" class="block text-xs font-semibold uppercase tracking-wide text-[#8b96a5]">Nome</label>
                        <input id="list-name" type="text" wire:model.defer="form.name"
                            class="mt-2 w-full rounded-[14px] border border-[#212832] bg-[#0d1116] px-3 py-2 text-sm text-white placeholder-[#6c7684] focus:border-[#3b82f6] focus:outline-none focus:ring-0"
                            placeholder="ex: Projetos" autocomplete="off" />
                        @error('form.name')
                            <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="list-view-mode" class="block text-xs font-semibold uppercase tracking-wide text-[#8b96a5]">Visualização</label>
                        <select id="list-view-mode" wire:model.defer="form.view_mode"
                            class="mt-2 w-full rounded-[14px] border border-[#212832] bg-[#0d1116] px-3 py-2 text-sm text-white focus:border-[#3b82f6] focus:outline-none focus:ring-0">
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
                            class="rounded-[14px] border border-[#212832] px-4 py-2 text-xs font-semibold uppercase tracking-wide text-[#8b96a5] transition hover:border-[#3b82f6] hover:text-white">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="flex items-center gap-2 rounded-[14px] bg-[#3b82f6] px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white shadow-lg shadow-[#3b82f6]/40 transition hover:bg-[#2563eb]"
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
