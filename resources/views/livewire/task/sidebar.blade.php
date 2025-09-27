<aside class="tasks-panel tasks-sidebar" wire:key="tasks-sidebar-{{ $activeView }}-{{ $activeListId }}">
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
                            class="tasks-nav-btn {{ $activeView === $view['slug'] ? 'is-active' : '' }}"
                            wire:click="$dispatch('tasks-sidebar:view-selected', { view: '{{ $view['slug'] }}' })"
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
                <button
                    type="button"
                    class="task-secondary-btn"
                    style="padding:6px 12px; font-size:10px; letter-spacing:0.12em; text-transform:uppercase;"
                    wire:click="$dispatch('task-lists:open-create')"
                >
                    + New
                </button>
            </div>

            @if (empty($lists))
                <div class="tasks-info-card">
                    Crie sua primeira lista para organizar suas tarefas.
                </div>
            @else
                <ul class="tasks-nav-list">
                    @foreach ($lists as $list)
                        <li wire:key="tasks-sidebar-list-{{ $list['id'] }}">
                            <button
                                type="button"
                                class="tasks-nav-btn {{ (int) $activeListId === (int) $list['id'] ? 'is-active' : '' }}"
                                wire:click="$dispatch('tasks-sidebar:list-selected', { listId: {{ $list['id'] }} })"
                            >
                                <span style="font-weight:600; letter-spacing:0.08em; text-transform:uppercase;">{{ $list['name'] }}</span>
                                <span class="tasks-nav-count">{{ $list['tasks_count'] }}</span>
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
                        <div class="tasks-tag" wire:key="tasks-sidebar-tag-{{ $tag['id'] }}">
                            <span class="tasks-tag-dot" style="background: {{ $tag['color'] ?? '#9ca3af' }}"></span>
                            <span style="text-transform:uppercase; letter-spacing:0.08em;">{{ $tag['label'] }}</span>
                            <span class="tasks-nav-count" style="min-width:auto; padding:2px 8px; border-color:rgba(37,48,74,0.7);">{{ $tag['tasks_count'] }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</aside>
