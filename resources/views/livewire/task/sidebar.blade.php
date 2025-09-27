<aside class="si-sidebar">
    {{-- ALERTA (opcional) --}}
    @if (session()->has('task_lists.created'))
        <div class="si-alert">
            {{ session('task_lists.created') }}
        </div>
    @endif

    {{-- CABEÇALHO DA SIDEBAR --}}
    <div class="si-panel-header">
        <div class="si-title">Listas</div>
    </div>

    {{-- CONTEÚDO ROLÁVEL COM SCROLL INVISÍVEL --}}
    <div class="si-sb-scroll">
        {{-- VIEWS --}}
        <div class="si-section">
            <div class="si-heading">Views</div>
            <ul class="si-nav-list">
                @foreach ($views as $view)
                    @php $isActive = $activeView === $view['slug']; @endphp
                    <li>
                        <button type="button" wire:click="openView('{{ $view['slug'] }}')"
                                class="si-nav-item {{ $isActive ? 'is-active' : '' }}">
                            <span class="si-chip">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="{{ $view['icon'] }}" />
                                </svg>
                            </span>
                            <span>{{ $view['label'] }}</span>
                            <span class="si-count">{{ $view['count'] }}</span>
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="si-divider"></div>

        {{-- LISTS --}}
        <div class="si-section">
            <div class="si-lists-head">
                <div class="si-heading" style="padding:0">Lists</div>
                <button type="button" wire:click="$set('showCreateList', true)" class="si-new">+ New</button>
            </div>

            @if ($lists->isEmpty())
                <p class="si-filters-box" style="border-style: dashed;">
                    Crie sua primeira lista para organizar suas tarefas.
                </p>
            @else
                <ul class="si-list-list">
                    @foreach ($lists as $list)
                        <li wire:key="task-list-{{ $list->id }}">
                            <button type="button"
                                    wire:click="openList({{ $list->id }})"
                                    class="si-list-item {{ $activeListId === $list->id ? 'is-active' : '' }}">
                                <span class="si-list-name">{{ $list->name }}</span>
                                <span class="si-list-right">{{ $list->tasks_count }}</span>
                            </button>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="si-divider"></div>

        {{-- FILTERS --}}
        <div class="si-section">
            <div class="si-heading">Filters</div>
            <p class="si-filters-box">
                {{ $filtersDescription }}
            </p>
        </div>

        <div class="si-divider"></div>

        {{-- TAGS --}}
        <div class="si-section">
            <div class="si-heading">Tags</div>
            @if (empty($tags))
                <p class="si-filters-box" style="border-style:dashed;">
                    Crie tags ao editar uma tarefa para organizá-las por contexto.
                </p>
            @else
                <ul class="si-tag-list">
                    @foreach ($tags as $tag)
                        <li class="si-tag-item">
                            <span class="flex items-center gap-3 uppercase tracking-wide" style="letter-spacing:.06em;">
                                <span class="si-tag-dot" style="background-color: {{ $tag['color'] }}"></span>
                                {{ $tag['label'] }}
                            </span>
                            <span class="si-tag-right">{{ $tag['tasks_count'] }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    {{-- MODAL (inalterado; permanece fora da área rolável) --}}
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
{{-- FIM DA SIDEBAR --}}