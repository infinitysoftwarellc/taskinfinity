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
                                    <path d="{{ $view['icon'] }}" />
                                </svg>
                            </span>
                            <span>{{ $view['label'] }}</span>
                            <span class="tasks-nav-count">{{ $view['count'] }}</span>
                        </button>
                    </li>
                @endforeach
            </ul>
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
    </div>

    @if ($showCreateList)
        <div class="tasks-modal-backdrop" wire:click.self="$set('showCreateList', false)">
            <div class="tasks-modal" wire:click.stop>
                <button type="button" class="tasks-modal-close" wire:click="$set('showCreateList', false)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <h2>Nova lista</h2>
                <p>Adicione uma nova lista sem recarregar a página.</p>

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
                        @error('form.name')
                            <span class="tasks-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="tasks-field">
                        <label for="list-view-mode">Visualização</label>
                        <select id="list-view-mode" wire:model.defer="form.view_mode" class="tasks-select">
                            <option value="list">Lista</option>
                            <option value="kanban">Kanban</option>
                            <option value="timeline">Linha do tempo</option>
                        </select>
                        @error('form.view_mode')
                            <span class="tasks-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="tasks-modal-actions">
                        <button type="button" class="tasks-button-secondary" wire:click="$set('showCreateList', false)">
                            Cancelar
                        </button>
                        <button type="submit" class="tasks-button-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove>Criar lista</span>
                            <span wire:loading>Salvando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</aside>
