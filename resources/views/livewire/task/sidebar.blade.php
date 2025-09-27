<<<<<<< HEAD
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
=======
<aside class="tasks-panel tasks-sidebar">
    <div class="tasks-panel-scroll">
        @if (session()->has('task_lists.created'))
            <div class="tasks-alert tasks-alert-success">
                {{ session('task_lists.created') }}
            </div>
        @endif

        <section class="tasks-sidebar-section">
            <h4 class="tasks-section-title">Views</h4>
            <ul class="tasks-nav-list">
                @foreach ($views as $view)
                    <li>
                        <button
                            type="button"
                            wire:click="openView('{{ $view['slug'] }}')"
                            class="tasks-nav-btn {{ $activeView === $view['slug'] ? 'is-active' : '' }}"
                        >
                            <span class="tasks-nav-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
>>>>>>> 203975097f3789755df396b163864b5bc02f99ae
                                    <path d="{{ $view['icon'] }}" />
                                </svg>
                            </span>
                            <span>{{ $view['label'] }}</span>
<<<<<<< HEAD
                            <span class="si-count">{{ $view['count'] }}</span>
=======
                            <span class="tasks-nav-count">{{ $view['count'] }}</span>
>>>>>>> 203975097f3789755df396b163864b5bc02f99ae
                        </button>
                    </li>
                @endforeach
            </ul>
<<<<<<< HEAD
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
=======
        </section>

        <section class="tasks-sidebar-section">
            <div class="tasks-section-title" style="display:flex; align-items:center; justify-content:space-between; gap:8px;">
                <span>Lists</span>
                <button type="button" wire:click="$set('showCreateList', true)" class="task-secondary-btn" style="padding:6px 12px; font-size:10px; letter-spacing:0.12em; text-transform:uppercase;">
                    + New
                </button>
            </div>
            @if ($lists->isEmpty())
                <div class="tasks-info-card">
                    Crie sua primeira lista para organizar suas tarefas.
                </div>
            @else
                <ul class="tasks-nav-list">
                    @foreach ($lists as $list)
                        <li wire:key="task-list-{{ $list->id }}">
                            <button
                                type="button"
                                wire:click="openList({{ $list->id }})"
                                class="tasks-nav-btn {{ $activeListId === $list->id ? 'is-active' : '' }}"
                            >
                                <span style="font-weight:600; letter-spacing:0.08em; text-transform:uppercase;">{{ $list->name }}</span>
                                <span class="tasks-nav-count">{{ $list->tasks_count }}</span>
                            </button>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <section class="tasks-sidebar-section">
            <h4 class="tasks-section-title">Filters</h4>
            <div class="tasks-info-card">
                {{ $filtersDescription }}
            </div>
        </section>

        <section class="tasks-sidebar-section">
            <h4 class="tasks-section-title">Tags</h4>
            @if (empty($tags))
                <div class="tasks-info-card">
                    Crie tags ao editar uma tarefa para organizá-las por contexto.
                </div>
            @else
                <div class="tasks-tags">
                    @foreach ($tags as $tag)
                        <div class="tasks-tag">
                            <span class="tasks-tag-dot" style="background: {{ $tag['color'] }}"></span>
                            <span style="text-transform:uppercase; letter-spacing:0.08em;">{{ $tag['label'] }}</span>
                            <span class="tasks-nav-count" style="min-width:auto; padding:2px 8px; border-color:rgba(37,48,74,0.7);">{{ $tag['tasks_count'] }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
>>>>>>> 203975097f3789755df396b163864b5bc02f99ae
    </div>

    {{-- MODAL (inalterado; permanece fora da área rolável) --}}
    @if ($showCreateList)
<<<<<<< HEAD
        <div class="absolute inset-0 z-20 flex items-center justify-center">
            <div class="fixed inset-0 bg-black/60" wire:click="$set('showCreateList', false)"></div>
            <div class="relative w-full max-w-sm rounded-3xl border border-white/10 bg-gray-900/95 p-6 text-sm shadow-2xl">
                <button type="button" wire:click="$set('showCreateList', false)"
                        class="absolute right-4 top-4 rounded-full bg-white/10 p-1 text-white/60 transition hover:bg-white/20 hover:text-white">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
=======
        <div class="tasks-modal-backdrop" wire:click.self="$set('showCreateList', false)">
            <div class="tasks-modal" wire:click.stop>
                <button type="button" class="tasks-modal-close" wire:click="$set('showCreateList', false)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
>>>>>>> 203975097f3789755df396b163864b5bc02f99ae
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <h2>Nova lista</h2>
                <p>Adicione uma nova lista sem recarregar a página.</p>

<<<<<<< HEAD
                <form wire:submit.prevent="createList" class="mt-4 space-y-4">
                    <div>
                        <label for="list-name" class="block text-xs font-medium uppercase tracking-wide text-white/60">Nome</label>
                        <input id="list-name" type="text" wire:model.defer="form.name"
                               class="mt-2 w-full rounded-2xl border border-white/10 bg-black/40 px-3 py-2 text-sm text-white placeholder-white/40 focus:border-indigo-400 focus:outline-none focus:ring-0"
                               placeholder="ex: Projetos" autocomplete="off" />
=======
                <form wire:submit.prevent="createList" class="tasks-modal-form">
                    <div class="tasks-field">
                        <label for="list-name">Nome</label>
                        <input
                            id="list-name"
                            type="text"
                            wire:model.defer="form.name"
                            class="tasks-input"
                            placeholder="ex: Projetos"
                            autocomplete="off"
                        />
>>>>>>> 203975097f3789755df396b163864b5bc02f99ae
                        @error('form.name')
                            <span class="tasks-error">{{ $message }}</span>
                        @enderror
                    </div>

<<<<<<< HEAD
                    <div>
                        <label for="list-view-mode" class="block text-xs font-medium uppercase tracking-wide text-white/60">Visualização</label>
                        <select id="list-view-mode" wire:model.defer="form.view_mode"
                                class="mt-2 w-full rounded-2xl border border-white/10 bg-black/40 px-3 py-2 text-sm text-white focus:border-indigo-400 focus:outline-none focus:ring-0">
=======
                    <div class="tasks-field">
                        <label for="list-view-mode">Visualização</label>
                        <select id="list-view-mode" wire:model.defer="form.view_mode" class="tasks-select">
>>>>>>> 203975097f3789755df396b163864b5bc02f99ae
                            <option value="list">Lista</option>
                            <option value="kanban">Kanban</option>
                            <option value="timeline">Linha do tempo</option>
                        </select>
                        @error('form.view_mode')
                            <span class="tasks-error">{{ $message }}</span>
                        @enderror
                    </div>

<<<<<<< HEAD
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
=======
                    <div class="tasks-modal-actions">
                        <button type="button" class="tasks-button-secondary" wire:click="$set('showCreateList', false)">
                            Cancelar
                        </button>
                        <button type="submit" class="tasks-button-primary" wire:loading.attr="disabled">
>>>>>>> 203975097f3789755df396b163864b5bc02f99ae
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