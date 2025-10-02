{{-- This Blade view renders the livewire tasks details interface. --}}
@php
    $multiSelectionIds = $multiSelection ?? [];
    $isMultiSelection = !empty($multiSelectionIds);
    $multiSummary = $multiSelectionSummary ?? null;
    $multiItems = collect($multiSelectionItems ?? []);
    $missionData = $isMultiSelection ? null : (is_array($mission) ? $mission : null);
    $activeSubtaskContext = $missionData['active_subtask'] ?? null;
    $isSubtaskContext = !empty($activeSubtaskContext);
    $isDoneContext = $isSubtaskContext
        ? (bool) ($activeSubtaskContext['is_done'] ?? false)
        : ($missionData['status'] ?? null) === 'done';
    $priorityValue = isset($missionData['priority']) ? (int) $missionData['priority'] : 0;
    $priorityLabel = $missionData['priority_label'] ?? 'Nenhuma';
    if ($isMultiSelection) {
        $isDoneContext = false;
    }
@endphp

<aside @class([
    'ti-details',
    'is-done' => $isDoneContext,
    'is-subtask' => $isSubtaskContext,
    'is-multi' => $isMultiSelection,
]) data-selected-subtask="{{ $selectedSubtaskId ?? '' }}"
    data-mission-id="{{ $missionData['id'] ?? '' }}">
    @if ($isMultiSelection)
        @php
            $multiTotal = $multiSummary['total'] ?? count($multiSelectionIds);
            $multiCompleted = $multiSummary['completed'] ?? 0;
            $multiActive = $multiSummary['active'] ?? max($multiTotal - $multiCompleted, 0);
            $multiProgress = $multiSummary['completion_rate'] ?? ($multiTotal > 0 ? (int) round(($multiCompleted / $multiTotal) * 100) : 0);
            $multiStarred = $multiSummary['starred'] ?? 0;
            $allPinned = $multiTotal > 0 && $multiStarred === $multiTotal;
            $listBreakdown = collect($multiSummary['lists'] ?? [])->take(3);
            $priorityBreakdown = $multiSummary['priority'] ?? [];
            $priorityLabel = 'Nenhuma';

            if (($priorityBreakdown['high'] ?? 0) > 0) {
                $priorityLabel = 'Alta';
            } elseif (($priorityBreakdown['medium'] ?? 0) > 0) {
                $priorityLabel = 'Média';
            } elseif (($priorityBreakdown['low'] ?? 0) > 0) {
                $priorityLabel = 'Baixa';
            }

            $checkpointTotal = $multiSummary['checkpoints']['total'] ?? 0;
            $checkpointDone = $multiSummary['checkpoints']['done'] ?? 0;
            $checkpointProgress = $checkpointTotal > 0 ? (int) round(($checkpointDone / max($checkpointTotal, 1)) * 100) : 0;
            $lastUpdated = $multiSummary['last_updated'] ?? null;
        @endphp
        <div class="ti-details-wrapper ti-details-multi">
            <div class="ti-multi-selection">
                <div class="ti-multi-head">
                    <div class="ti-multi-count">
                        <span class="ti-multi-number">{{ $multiTotal }}</span>
                        <span class="ti-multi-label">tarefas selecionadas</span>
                    </div>
                    <div class="ti-multi-progress" style="--multi-progress: {{ $multiProgress }}%">
                        <span class="ti-multi-progress-label">Progresso</span>
                        <div class="ti-multi-progress-bar" aria-label="{{ $multiProgress }}% concluído"></div>
                        <span class="ti-multi-progress-value">{{ $multiProgress }}%</span>
                    </div>
                </div>

                <div class="ti-multi-actions">
                    <div class="ti-multi-action-group">
                        <span class="ti-multi-group-label">Estado</span>
                        <div class="ti-multi-action-row">
                            <button class="ti-multi-action primary" type="button" wire:click="markSelectionAsDone"
                                @if ($multiActive === 0) disabled @endif wire:loading.attr="disabled">
                                <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                                <span>Concluir abertas</span>
                            </button>
                            <button class="ti-multi-action" type="button" wire:click="markSelectionAsActive"
                                @if ($multiCompleted === 0) disabled @endif wire:loading.attr="disabled">
                                <i class="fa-solid fa-rotate-left" aria-hidden="true"></i>
                                <span>Reativar concluídas</span>
                            </button>
                        </div>
                    </div>

                    <div class="ti-multi-action-group">
                        <span class="ti-multi-group-label">Datas rápidas</span>
                        <div class="ti-multi-action-row">
                            <button class="ti-multi-chip" type="button"
                                wire:click="applyMultiSelectionShortcut('today')" wire:loading.attr="disabled">
                                Hoje
                            </button>
                            <button class="ti-multi-chip" type="button"
                                wire:click="applyMultiSelectionShortcut('tomorrow')" wire:loading.attr="disabled">
                                Amanhã
                            </button>
                            <button class="ti-multi-chip" type="button"
                                wire:click="applyMultiSelectionShortcut('next7')" wire:loading.attr="disabled">
                                Próx. 7 dias
                            </button>
                            <button class="ti-multi-chip ghost" type="button"
                                wire:click="applyMultiSelectionShortcut('clear')" wire:loading.attr="disabled">
                                Limpar datas
                            </button>
                            <label class="ti-multi-chip picker">
                                <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                                <span>Personalizar</span>
                                <input
                                    type="date"
                                    data-flatpickr
                                    data-flatpickr-date-format="Y-m-d"
                                    data-flatpickr-alt-format="d/m/Y"
                                    x-data="{}"
                                    x-init="$flatpickr()"
                                    wire:model.defer="multiSelectionDate"
                                    wire:change="applyMultiSelectionDate"
                                    aria-label="Definir data personalizada"
                                />
                            </label>
                        </div>
                    </div>

                    <div class="ti-multi-action-group">
                        <span class="ti-multi-group-label">Organização</span>
                        <div class="ti-multi-action-row">
                            <button class="ti-multi-action secondary" type="button" wire:click="togglePinSelection"
                                wire:loading.attr="disabled">
                                <i class="fa-solid fa-thumbtack" aria-hidden="true"></i>
                                <span>{{ $allPinned ? 'Desafixar' : 'Fixar' }} seleção</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="ti-multi-stats">
                    <div class="ti-multi-stat">
                        <span class="ti-multi-stat-label">Concluídas</span>
                        <strong class="ti-multi-stat-value">{{ $multiCompleted }}</strong>
                        <span class="ti-multi-stat-hint">{{ $multiActive }} abertas</span>
                    </div>
                    <div class="ti-multi-stat">
                        <span class="ti-multi-stat-label">Datas definidas</span>
                        <strong class="ti-multi-stat-value">{{ $multiSummary['with_due'] ?? 0 }}</strong>
                        <span class="ti-multi-stat-hint">{{ ($multiSummary['overdue'] ?? 0) }} atrasada(s)</span>
                    </div>
                    <div class="ti-multi-stat">
                        <span class="ti-multi-stat-label">Prioridade máxima</span>
                        <strong class="ti-multi-stat-value">{{ $priorityLabel }}</strong>
                        <span class="ti-multi-stat-hint">Alta: {{ $priorityBreakdown['high'] ?? 0 }} &bull; Média: {{ $priorityBreakdown['medium'] ?? 0 }}</span>
                    </div>
                    <div class="ti-multi-stat">
                        <span class="ti-multi-stat-label">Subtarefas</span>
                        <strong class="ti-multi-stat-value">{{ $checkpointDone }} / {{ $checkpointTotal }}</strong>
                        <span class="ti-multi-stat-hint">{{ $checkpointProgress }}% concluídas</span>
                    </div>
                </div>

                <div class="ti-multi-lists">
                    <h3 class="ti-multi-section-title">Listas envolvidas</h3>
                    <div class="ti-multi-list-grid">
                        @forelse ($listBreakdown as $listName => $count)
                            <div class="ti-multi-list-chip">
                                <span class="ti-multi-list-name">{{ $listName }}</span>
                                <span class="ti-multi-list-count">{{ $count }}</span>
                            </div>
                        @empty
                            <div class="ti-multi-list-chip is-empty">Sem listas atribuídas</div>
                        @endforelse
                    </div>
                </div>

                <div class="ti-multi-preview">
                    <h3 class="ti-multi-section-title">Visão rápida</h3>
                    <ul class="ti-multi-preview-list">
                        @forelse ($multiItems->take(6) as $item)
                            @php
                                $itemDue = $item['due_at'] ?? null;
                                $itemDueLabel = null;
                                $itemDueClass = 'is-empty';

                                if ($itemDue instanceof \Carbon\CarbonInterface) {
                                    $itemDueLabel = $itemDue->format('d/m');
                                    $todayLabel = now($itemDue->getTimezone())->startOfDay();
                                    $tomorrowLabel = $todayLabel->copy()->addDay();

                                    if ($itemDue->isSameDay($todayLabel)) {
                                        $itemDueLabel = 'Hoje';
                                        $itemDueClass = 'is-today';
                                    } elseif ($itemDue->isSameDay($tomorrowLabel)) {
                                        $itemDueLabel = 'Amanhã';
                                        $itemDueClass = 'is-tomorrow';
                                    } elseif ($itemDue->lessThan($todayLabel)) {
                                        $itemDueClass = 'is-overdue';
                                    } else {
                                        $itemDueClass = 'is-upcoming';
                                    }
                                }
                            @endphp
                            <li class="ti-multi-preview-item">
                                <div class="ti-multi-preview-main">
                                    <span class="ti-multi-preview-title">{{ ($item['title'] ?? '') !== '' ? $item['title'] : 'Sem título' }}</span>
                                    @if (!empty($item['list']))
                                        <span class="ti-multi-preview-tag">{{ $item['list'] }}</span>
                                    @endif
                                </div>
                                <div class="ti-multi-preview-meta">
                                    @if ($itemDueLabel)
                                        <span class="ti-multi-preview-date {{ $itemDueClass }}">{{ $itemDueLabel }}</span>
                                    @else
                                        <span class="ti-multi-preview-date is-empty">Sem data</span>
                                    @endif
                                    <span class="ti-multi-preview-status {{ ($item['status'] ?? '') === 'done' ? 'is-done' : '' }}">
                                        {{ ($item['status'] ?? '') === 'done' ? 'Concluída' : 'Ativa' }}
                                    </span>
                                    @if (($item['priority'] ?? 0) > 0)
                                        <span class="ti-multi-preview-priority priority-{{ $item['priority'] }}">
                                            <i class="fa-solid fa-flag" aria-hidden="true"></i>
                                        </span>
                                    @endif
                                </div>
                            </li>
                        @empty
                            <li class="ti-multi-preview-empty">Seleção vazia</li>
                        @endforelse
                    </ul>
                    @if ($lastUpdated instanceof \Carbon\CarbonInterface)
                        <p class="ti-multi-updated">Última atualização: {{ $lastUpdated->diffForHumans() }}</p>
                    @endif
                </div>
            </div>
        </div>
    @elseif ($missionData)
        @php
            $activeSubtask = $missionData['active_subtask'] ?? null;
            $isSubtask = !empty($activeSubtask);
        @endphp
        @if ($showDatePicker ?? false)
            <div class="ti-date-overlay" wire:click="closeAllDatePickers"></div>
        @endif

        <div class="ti-details-wrapper">

            <!-- Top bar -->
            <div class="ti-details-top">
        @if ($isSubtask)
            <div class="ti-topbar">
                <div class="ti-topbar-left">
                    <button class="ti-check {{ $activeSubtask['is_done'] ?? false ? 'is-active' : '' }}"
                        type="button" title="Concluir subtarefa"
                        aria-pressed="{{ $activeSubtask['is_done'] ?? false ? 'true' : 'false' }}"
                        wire:click="toggleCheckpoint({{ $activeSubtask['id'] }})">
                        <span class="ti-check-mark"></span>
                    </button>

                    <div class="ti-subtask-status-block">
                        <span class="ti-subtask-status">Subtarefa selecionada</span>
                        <div class="ti-subtask-meta-group">
                            <div class="ti-subtask-meta">
                                <span class="ti-subtask-meta-label">Tarefa mãe</span>
                                <span class="ti-subtask-meta-value">{{ $mission['title'] ?? 'Sem título' }}</span>
                            </div>
                            <div class="ti-subtask-meta">
                                <span class="ti-subtask-meta-label">Organização</span>
                                <span class="ti-subtask-meta-value">Arraste para reordenar</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="ti-topbar">
                <div class="ti-topbar-left">
                    <button class="ti-check {{ ($mission['status'] ?? null) === 'done' ? 'is-active' : '' }}"
                        type="button" title="Concluir missão"
                        aria-pressed="{{ ($mission['status'] ?? null) === 'done' ? 'true' : 'false' }}"
                        wire:click="toggleCompletion">
                        <span class="ti-check-mark"></span>
                    </button>

                    <div class="ti-date-picker-container" wire:keydown.escape.window="closeAllDatePickers">
                        @php
                            $detailsTimezone = auth()->user()?->timezone ?? config('app.timezone');
                            $missionDueLabel =
                                $mission['due_at'] instanceof \Carbon\CarbonInterface
                                    ? $mission['due_at']->copy()->setTimezone($detailsTimezone)->format('d/m/Y')
                                    : null;
                            $missionDueIso =
                                $mission['due_at'] instanceof \Carbon\CarbonInterface
                                    ? $mission['due_at']->copy()->setTimezone($detailsTimezone)->toIso8601String()
                                    : null;
                            $missionTimezone = $detailsTimezone;
                        @endphp
                        <button class="pill icon-only {{ $showDatePicker ?? false ? 'is-active' : '' }}" type="button"
                            title="{{ $missionDueLabel ?? 'Sem data definida' }}"
                            aria-label="{{ $missionDueLabel ? 'Data: ' . $missionDueLabel : 'Adicionar data' }}"
                            wire:click="toggleDatePicker">
                            <i class="fa-solid fa-calendar" aria-hidden="true"></i>
                            @if ($missionDueLabel)
                                <span
                                    class="ti-date-label"
                                    data-relative-datetime="{{ $missionDueIso }}"
                                    data-relative-tz="{{ $missionTimezone }}"
                                    data-relative-fallback="{{ $missionDueLabel }}"
                                >
                                    {{ $missionDueLabel }}
                                </span>
                            @endif
                        </button>

                        @if (($showDatePicker ?? false) && $pickerCalendar)
                            <div class="ti-date-popover" wire:click.stop>
                                <div class="ti-date-header">
                                    <button class="nav" type="button" title="Mês anterior"
                                        wire:click="movePicker(-1)">
                                        <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
                                    </button>
                                    <span class="label">{{ $pickerCalendar['label'] ?? '' }}</span>
                                    <button class="nav" type="button" title="Próximo mês"
                                        wire:click="movePicker(1)">
                                        <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                                    </button>
                                </div>

                                <div class="ti-date-grid">
                                    @foreach ($pickerCalendar['weekDays'] ?? [] as $weekDay)
                                        <span class="weekday">{{ $weekDay }}</span>
                                    @endforeach

                                    @foreach ($pickerCalendar['weeks'] ?? [] as $weekIndex => $week)
                                        @foreach ($week as $dayIndex => $day)
                                            @php
                                                $classes = [];
                                                if (!($day['isCurrentMonth'] ?? false)) {
                                                    $classes[] = 'is-muted';
                                                }
                                                if ($day['isToday'] ?? false) {
                                                    $classes[] = 'is-today';
                                                }
                                                if ($day['isSelected'] ?? false) {
                                                    $classes[] = 'is-selected';
                                                }
                                                $classAttr = implode(' ', $classes);
                                            @endphp
                                            <button class="day {{ $classAttr }}" type="button"
                                                wire:key="calendar-day-{{ $weekIndex }}-{{ $dayIndex }}-{{ $day['date'] }}"
                                                wire:click="selectDueDate('{{ $day['date'] }}')">
                                                {{ $day['label'] ?? '' }}
                                            </button>
                                        @endforeach
                                    @endforeach
                                </div>

                                <div class="ti-date-footer">
                                    @if ($pickerCalendar['hasSelected'] ?? false)
                                        <button class="link" type="button" wire:click="clearDueDate">Remover
                                            data</button>
                                    @else
                                        <button class="link disabled" type="button" disabled>Sem data
                                            definida</button>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="ti-priority-selector">
                        <button class="icon ghost" type="button" title="Prioridade">
                            <i class="fa-solid fa-flag" aria-hidden="true"></i>
                        </button>
                        <span class="ti-priority-current">{{ $priorityLabel }}</span>

                        <div class="ti-priority-menu" role="menu">
                            <button @class(['ti-priority-option', 'is-high', 'is-active' => $priorityValue === 3]) type="button" role="menuitem"
                                wire:click="setPriority(3)">
                                <span class="dot"></span> Alta
                            </button>
                            <button @class(['ti-priority-option', 'is-medium', 'is-active' => $priorityValue === 2]) type="button" role="menuitem"
                                wire:click="setPriority(2)">
                                <span class="dot"></span> Média
                            </button>
                            <button @class(['ti-priority-option', 'is-low', 'is-active' => $priorityValue === 1]) type="button" role="menuitem"
                                wire:click="setPriority(1)">
                                <span class="dot"></span> Baixa
                            </button>
                            <button @class(['ti-priority-option', 'is-none', 'is-active' => $priorityValue === 0]) type="button" role="menuitem"
                                wire:click="setPriority(0)">
                                <span class="dot"></span> Nenhuma
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
            </div>

            <div class="ti-details-scroll">

                <div class="ti-divider"></div>

                <!-- Title + actions -->
                <div class="ti-header">
                    <div class="ti-title-block">
                        @php
                            $activeSubtask = $mission['active_subtask'] ?? null;
                        @endphp
                        @if (!empty($activeSubtask))
                            <span class="ti-parent"
                                title="Tarefa pai">{{ $mission['parent_title'] ?? ($mission['title'] ?? '') }}</span>
                            <h1 class="ti-title" title="{{ $activeSubtask['title'] ?? '' }}">
                                {{ ($activeSubtask['title'] ?? '') !== '' ? $activeSubtask['title'] : 'Sem título' }}
                            </h1>
                        @elseif (!empty($mission['parent_title']))
                            <span class="ti-parent" title="Tarefa pai">{{ $mission['parent_title'] ?? '' }}</span>
                            <h1 class="ti-title" title="{{ $mission['title'] }}">
                                {{ $mission['title'] ?: 'Sem título' }}</h1>
                        @else
                            <h1 class="ti-title" title="{{ $mission['title'] }}">
                                {{ $mission['title'] ?: 'Sem título' }}</h1>
                        @endif
                    </div>
                </div>

                <!-- Subtasks -->
                <section class="ti-subtasks">
                    @php
                        $activeNode = $mission['active_subtask'] ?? null;
                        $subtasks = $activeNode ? $activeNode['children'] ?? [] : $mission['subtasks'] ?? [];
                        $maxSubtasks = $maxSubtasks ?? \App\Livewire\Tasks\MainPanel::MAX_SUBTASKS;
                        $rootSubtaskCount = count($subtasks);
                        $canAddRootSubtask = $rootSubtaskCount < $maxSubtasks;
                    @endphp

                    @if ($rootSubtaskCount)
                        <ul
                            class="ti-subtask-list"
                            role="list"
                            wire:sortable="reorderSubtasks"
                            data-subtask-container
                            data-mission-id="{{ $missionData['id'] ?? '' }}"
                            data-parent-id=""
                            x-data
                            x-auto-animate
                        >
                            @foreach ($subtasks as $st)
                                @include('livewire.tasks.partials.subtask-item', [
                                    'item' => $st,
                                    'depth' => 0,
                                    'selectedSubtaskId' => $selectedSubtaskId,
                                    'maxSubtasks' => $maxSubtasks,
                                    'missionId' => $missionData['id'] ?? null,
                                ])
                            @endforeach
                        </ul>
                    @else
                        <p class="muted" style="margin:8px 0 0;">Sem subtarefas</p>
                    @endif

                    @if ($canAddRootSubtask)
                        <button class="add-subtask" type="button" wire:click="openSubtaskForm">
                            <i class="fa-solid fa-plus" aria-hidden="true"></i> Adicionar subtarefa
                        </button>
                    @else
                        <div class="subtasks-limit">Limite de {{ $maxSubtasks }} subtarefas atingido.</div>
                    @endif
                </section>

                <div class="ti-divider"></div>

            </div>

            <footer class="ti-footer">
                <div class="ti-footer-start">
                    @if (!$isSubtask)
                        <div @class(['ti-list-selector', 'is-open' => $showMoveListMenu])
                            wire:click.away="closeMoveListMenu">
                            <button class="ti-list-selector-toggle" type="button" title="Mover para outra lista"
                                wire:click="toggleMoveListMenu">
                                <i class="fa-solid fa-list" aria-hidden="true"></i>
                                <span>{{ $mission['list'] ?? 'Sem lista' }}</span>
                                <i class="fa-solid fa-chevron-down chevron" aria-hidden="true"></i>
                            </button>

                            @if ($showMoveListMenu)
                                <div class="ti-list-dropdown" role="menu">
                                    <button type="button"
                                        class="ti-list-option {{ ($mission['list_id'] ?? null) === null ? 'is-active' : '' }}"
                                        wire:click.stop="moveToList(null)">
                                        <span>Sem lista</span>
                                    </button>
                                    @foreach ($availableLists as $listOption)
                                        <button type="button"
                                            class="ti-list-option {{ $listOption['is_current'] ? 'is-active' : '' }}"
                                            wire:click.stop="moveToList({{ $listOption['id'] }})">
                                            <span>{{ $listOption['name'] }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="ti-footer-actions">
                    <div
                        class="ti-menu"
                        x-data="tiInlineMenu({ placement: 'top-end' })"
                        x-id="['details-menu','details-trigger']"
                    >
                        <button
                            class="icon ghost"
                            title="Mais opções"
                            type="button"
                            x-ref="trigger"
                            :id="$id('details-trigger')"
                            :aria-controls="$id('details-menu')"
                            :aria-expanded="open.toString()"
                            aria-haspopup="true"
                            @click.prevent="toggle()"
                        >
                            <i class="fa-solid fa-ellipsis" aria-hidden="true"></i>
                            <span class="sr-only">Abrir menu contextual</span>
                        </button>
                        <div
                            class="ti-menu-dropdown"
                            x-ref="dropdown"
                            x-show="open"
                            x-transition.origin.bottom.right
                            role="menu"
                            :id="$id('details-menu')"
                            :aria-labelledby="$id('details-trigger')"
                            :aria-hidden="(!open).toString()"
                            @keydown.escape.stop.prevent="close(true)"
                            @click.outside="close()"
                            @click="if ($event.target.closest('[data-menu-item]')) close(true)"
                        >
                            @include('livewire.tasks.partials.menu-content', [
                                'context' => 'details',
                                'subtaskId' => $activeSubtask['id'] ?? null,
                                'priority' => $priorityValue,
                                'isStarred' => $mission['is_starred'] ?? false,
                            ])
                        </div>
                    </div>

                    @if (!$isSubtask)
                        <button class="ti-footer-icon" type="button" title="Iniciar Pomodoro"
                            wire:click="startPomodoro">
                            <i class="fa-solid fa-stopwatch" aria-hidden="true"></i>
                        </button>
                        <button class="ti-footer-icon {{ $mission['is_starred'] ?? false ? 'is-active' : '' }}"
                            type="button" title="Favoritar"
                            aria-pressed="{{ $mission['is_starred'] ?? false ? 'true' : 'false' }}"
                            wire:click="toggleStar">
                            <i class="fa-solid fa-star" aria-hidden="true"></i>
                        </button>
                    @endif
                </div>
            </footer>
        </div>
    @else
        <div class="ti-empty">
            <h3>Selecione uma tarefa</h3>
            <p>Escolha uma tarefa para ver detalhes e subtarefas.</p>
        </div>
    @endif
</aside>
