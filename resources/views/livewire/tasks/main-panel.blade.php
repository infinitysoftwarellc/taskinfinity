@php
    $panelTitle = data_get($panel, 'title', '');
    $panelCount = data_get($panel, 'count');
    $inputPlaceholder = data_get($panel, 'inputPlaceholder', 'Add task');
@endphp

<main class="main panel">
    <div class="toolbar">
        <div class="title">
            {{ $panelTitle }}
            @if (!is_null($panelCount))
                <span class="bubble">{{ $panelCount }}</span>
            @endif
        </div>
        <div class="spacer"></div>
        <button class="icon-btn" title="Ordenar"><i data-lucide="sort-desc"></i></button>
        <button class="icon-btn" title="Opções"><i data-lucide="more-horizontal"></i></button>
    </div>

    <div class="add-row">
        <input class="add-input" placeholder="{{ $inputPlaceholder }}" />
    </div>

    @foreach (data_get($panel, 'groups', []) as $groupIndex => $group)
        @php
            $groupExpanded = (bool) data_get($group, 'expanded', true);
            $groupBodyStyle = $groupExpanded ? '' : 'display:none;';
        @endphp
        <section class="group" aria-expanded="{{ $groupExpanded ? 'true' : 'false' }}" wire:key="group-{{ $groupIndex }}">
            <header class="group-header" data-toggle="group">
                <i class="chev" data-lucide="chevron-down"></i>
                <span class="group-title">{{ data_get($group, 'title', '') }}</span>
                @if ($count = data_get($group, 'count'))
                    <span class="group-count">{{ $count }}</span>
                @endif
            </header>
            <div class="group-body" style="{{ $groupBodyStyle }}">
                @foreach (data_get($group, 'subgroups', []) as $subIndex => $subgroup)
                    @php
                        $subExpanded = (bool) data_get($subgroup, 'expanded', true);
                        $taskListStyle = $subExpanded ? '' : 'display:none;';
                    @endphp
                    <div class="subgroup" aria-expanded="{{ $subExpanded ? 'true' : 'false' }}" wire:key="subgroup-{{ $groupIndex }}-{{ $subIndex }}">
                        <div class="subgroup-toggle" data-toggle="subgroup">
                            <i class="chev" data-lucide="chevron-down"></i>
                            <span class="name">{{ data_get($subgroup, 'name', '') }}</span>
                            @if ($meta = data_get($subgroup, 'meta'))
                                <span class="meta" style="margin-left:auto; color:var(--muted)">{{ $meta }}</span>
                            @endif
                        </div>

                        <div class="task-list" style="{{ $taskListStyle }}">
                            @if ($ghost = data_get($subgroup, 'ghost'))
                                <div class="task ghost">
                                    <div class="checkbox" aria-hidden="true"></div>
                                    <div class="title-line">
                                        <span class="title" style="opacity:.6">{{ data_get($ghost, 'title', 'No Title') }}</span>
                                    </div>
                                    @if ($ghostMeta = data_get($ghost, 'meta'))
                                        <div class="meta">{{ $ghostMeta }}</div>
                                    @endif
                                </div>
                            @endif

                            @foreach (data_get($subgroup, 'tasks', []) as $taskIndex => $task)
                                @php
                                    $hasSubtasks = filled(data_get($task, 'subtasks'));
                                    $taskExpanded = (bool) data_get($task, 'expanded', true);
                                @endphp
                                @if ($hasSubtasks)
                                    <div class="task has-subtasks" aria-expanded="{{ $taskExpanded ? 'true' : 'false' }}" wire:key="task-{{ $groupIndex }}-{{ $subIndex }}-{{ $taskIndex }}">
                                        <button class="checkbox" aria-label="marcar"></button>
                                        <div class="expander" title="Expandir/ocultar subtarefas"><i data-lucide="chevron-down"></i></div>
                                        <div class="title-line"><span class="title">{{ data_get($task, 'title', '') }}</span></div>
                                        @if ($meta = data_get($task, 'meta'))
                                            <div class="meta">{{ $meta }}</div>
                                        @endif
                                    </div>
                                    <div class="subtasks" style="{{ $taskExpanded ? '' : 'display:none;' }}">
                                        @foreach (data_get($task, 'subtasks', []) as $subtaskIndex => $subtask)
                                            <div class="subtask" wire:key="subtask-{{ $groupIndex }}-{{ $subIndex }}-{{ $taskIndex }}-{{ $subtaskIndex }}">
                                                <button class="checkbox"></button>
                                                <div class="title">{{ data_get($subtask, 'title', '') }}</div>
                                                @if ($meta = data_get($subtask, 'meta'))
                                                    <div class="meta">{{ $meta }}</div>
                                                @endif
                                            </div>
                                        @endforeach
                                        <div class="add-subtask">
                                            <i data-lucide="plus"></i>
                                            <input type="text" placeholder="Add subtask" class="add-subtask-input" />
                                        </div>
                                    </div>
                                @else
                                    <div class="task" wire:key="task-{{ $groupIndex }}-{{ $subIndex }}-{{ $taskIndex }}">
                                        <button class="checkbox" aria-label="marcar"></button>
                                        <div class="title-line"><span class="title">{{ data_get($task, 'title', '') }}</span></div>
                                        @if ($meta = data_get($task, 'meta'))
                                            <div class="meta">{{ $meta }}</div>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endforeach
</main>
