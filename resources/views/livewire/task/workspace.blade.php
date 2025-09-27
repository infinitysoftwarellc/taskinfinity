@php
    $collapsedNoDate = $collapsedGroups['no-date'] ?? false;
    $collapsedCompleted = $collapsedGroups['completed'] ?? false;
    $activeList = collect($lists ?? [])->firstWhere('id', $activeListId);
    $viewLabel = collect($views ?? [])->firstWhere('slug', $activeView)['label'] ?? 'All';
@endphp

<div class="tasks-page">
    <div class="tasks-topbar">
        <div class="tasks-topbar-brand">Task Infinity</div>
        <nav class="tasks-topnav">
            @foreach ($views as $view)
                <button
                    type="button"
                    class="tasks-topnav-btn {{ $activeView === $view['slug'] ? 'is-active' : '' }}"
                    wire:click="setActiveView('{{ $view['slug'] }}')"
                >
                    <span class="tasks-topnav-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="{{ $view['icon'] }}" />
                        </svg>
                    </span>
                    {{ $view['label'] }}
                </button>
            @endforeach
        </nav>
        <div class="tasks-logout">
            <button type="button" title="Sair">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10 6H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h4" />
                    <path d="M14 12H3" />
                    <path d="m18 8 4 4-4 4" />
                </svg>
            </button>
        </div>
    </div>

    <div class="tasks-shell">
        <nav class="tasks-rail">
            <div class="tasks-rail-brand">TI</div>
            <button class="tasks-rail-btn is-active" type="button" title="Dashboard">🏠</button>
            <button class="tasks-rail-btn" type="button" title="Hoje">📅</button>
            <button class="tasks-rail-btn" type="button" title="7 dias">🗓️</button>
            <button class="tasks-rail-btn" type="button" title="Inbox">📥</button>
            <button class="tasks-rail-btn" type="button" title="Resumo">📈</button>
            <div class="tasks-rail-spacer"></div>
            <button class="tasks-rail-btn" type="button" title="Configurações">⚙️</button>
        </nav>

        @livewire('task.sidebar', [
            'views' => $views,
            'lists' => $lists,
            'tags' => $tags,
            'filtersDescription' => $filtersDescription,
            'activeView' => $activeView,
            'activeListId' => $activeListId,
        ])

        <section class="tasks-panel tasks-list-panel">
            <header class="tasks-list-header">
                <div class="tasks-list-header-meta">{{ $viewLabel }}</div>
                <h2 class="tasks-list-title">{{ $activeList['name'] ?? 'Minhas tarefas' }}</h2>
                <p class="tasks-list-description">Organize, priorize e acompanhe as tarefas que importam.</p>
            </header>

            <form class="tasks-add-form" wire:submit.prevent="createTask">
                <div class="tasks-input-field">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 5v14M5 12h14" />
                    </svg>
                    <input
                        type="text"
                        placeholder="Adicionar uma nova tarefa"
                        wire:model.defer="newTaskTitle"
                    />
                </div>
                <button type="submit" class="tasks-input-action">Adicionar</button>
            </form>

            @error('newTaskTitle')
                <div class="tasks-error">{{ $message }}</div>
            @enderror

            <div class="tasks-groups">
                <div class="tasks-group {{ $collapsedNoDate ? 'collapsed' : '' }}">
                    <div class="tasks-group-header" wire:click="toggleGroup('no-date')">
                        <span class="tasks-group-caret"></span>
                        <span>No date</span>
                        <span class="tasks-group-count">{{ $noDateTasks->count() }}</span>
                    </div>
                    @if (! $collapsedNoDate)
                        <div class="tasks-group-body">
                            @forelse ($noDateTasks as $task)
                                <div
                                    class="task-card {{ $task->status === 'done' ? 'is-complete' : '' }} {{ $selectedTask && $selectedTask->id === $task->id ? 'is-active' : '' }}"
                                    wire:key="task-card-{{ $task->id }}"
                                    wire:click="selectTask({{ $task->id }})"
                                >
                                    <div class="task-card-main">
                                        <button
                                            type="button"
                                            class="task-checkbox {{ $task->status === 'done' ? 'is-active' : '' }}"
                                            wire:click.stop="toggleTaskStatus({{ $task->id }})"
                                            aria-label="Alternar status"
                                        >
                                            <span>✓</span>
                                        </button>
                                        <div class="task-title-input">
                                            {{ $task->title }}
                                        </div>
                                    </div>
                                    <span class="task-pill">{{ $task->list?->name ?? 'Inbox' }}</span>
                                </div>
                            @empty
                                <div class="tasks-empty">
                                    Nenhuma tarefa sem data encontrada. Insira uma nova tarefa para começar.
                                </div>
                            @endforelse
                        </div>
                    @endif
                </div>

                <div class="tasks-group {{ $collapsedCompleted ? 'collapsed' : '' }}">
                    <div class="tasks-group-header" wire:click="toggleGroup('completed')">
                        <span class="tasks-group-caret"></span>
                        <span>Completed</span>
                        <span class="tasks-group-count">{{ $completedTasks->count() }}</span>
                    </div>
                    @if (! $collapsedCompleted)
                        <div class="tasks-group-body">
                            @forelse ($completedTasks as $task)
                                <div
                                    class="task-card is-complete {{ $selectedTask && $selectedTask->id === $task->id ? 'is-active' : '' }}"
                                    wire:key="task-card-completed-{{ $task->id }}"
                                    wire:click="selectTask({{ $task->id }})"
                                >
                                    <div class="task-card-main">
                                        <button
                                            type="button"
                                            class="task-checkbox is-active"
                                            wire:click.stop="toggleTaskStatus({{ $task->id }})"
                                            aria-label="Marcar como pendente"
                                        >
                                            <span>✓</span>
                                        </button>
                                        <div class="task-title-input">
                                            {{ $task->title }}
                                        </div>
                                    </div>
                                    <span class="task-pill">{{ $task->list?->name ?? 'Inbox' }}</span>
                                </div>
                            @empty
                                <div class="tasks-empty">
                                    Nenhuma tarefa concluída ainda. Complete uma tarefa para vê-la aqui.
                                </div>
                            @endforelse
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <aside class="tasks-panel tasks-detail-panel">
            <header class="tasks-detail-header">
                <h3 class="tasks-detail-title">Detalhes</h3>
                @if ($selectedTask)
                    <span class="tasks-tag-pill">{{ ucfirst($selectedTask->status) }}</span>
                @endif
            </header>
            <div class="tasks-detail-body">
                @if (! $selectedTask)
                    <p class="tasks-detail-placeholder">
                        Selecione uma tarefa para visualizar detalhes, descrição e datas importantes.
                    </p>
                @else
                    <div class="tasks-detail-form">
                        <div>
                            <h2 class="tasks-detail-task-title">{{ $selectedTask->title }}</h2>
                            <p class="tasks-detail-placeholder" style="margin-top:6px;">
                                Lista: {{ $selectedTask->list?->name ?? 'Inbox' }} • Prioridade: {{ ucfirst($selectedTask->priority) }}
                            </p>
                        </div>

                        <div class="tasks-detail-grid">
                            <div class="tasks-field">
                                <span>Data de vencimento</span>
                                <input
                                    type="date"
                                    class="tasks-input"
                                    value="{{ optional($selectedTask->due_at)->format('Y-m-d') }}"
                                    disabled
                                />
                            </div>
                            <div class="tasks-field">
                                <span>Pomodoros concluídos</span>
                                <input
                                    type="number"
                                    class="tasks-input"
                                    value="{{ $selectedTask->pomodoros_done }}"
                                    disabled
                                />
                            </div>
                        </div>

                        <div class="tasks-field">
                            <span>Descrição</span>
                            <textarea class="tasks-textarea" rows="6" disabled>{{ $selectedTask->description }}</textarea>
                        </div>

                        <div class="tasks-detail-actions">
                            <button type="button" class="tasks-button-secondary" disabled>Editar</button>
                            <button type="button" class="tasks-button-primary" disabled>Salvar</button>
                        </div>
                    </div>
                @endif
            </div>
        </aside>
    </div>
</div>
