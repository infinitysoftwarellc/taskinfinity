{{-- This Blade view renders the livewire tasks main panel interface. --}}
@php
    $userTimezone = auth()->user()?->timezone ?? config('app.timezone');
@endphp

<main class="main panel">
    <div class="toolbar">
        <div class="title">
            {{ $primaryGroupTitle }}
            <span class="bubble">{{ $totalCount }}</span>
        </div>
        <div class="spacer"></div>
        @if ($listView)
            <a wire:navigate href="{{ route('tasks.index') }}" class="toolbar-link">Ver todas as tarefas</a>
        @endif
        <button class="icon-btn" title="Ordenar"><i class="fa-solid fa-arrow-down-wide-short" aria-hidden="true"></i></button>
        <button class="icon-btn" title="Opções"><i class="fa-solid fa-ellipsis" aria-hidden="true"></i></button>
    </div>

    <form class="add-row" wire:submit.prevent="createTask">
        <input
            wire:model.defer="newTaskTitle"
            class="add-input"
            type="text"
            placeholder="{{ $inputPlaceholder }}"
            data-behavior="livewire"
            aria-label="Adicionar nova tarefa"
        />

        @if ($showListSelector)
            <select wire:model="newTaskListId" class="add-select" aria-label="Selecionar lista">
                <option value="">Sem lista</option>
                @foreach ($availableLists as $listOption)
                    <option value="{{ $listOption->id }}">{{ $listOption->name }}</option>
                @endforeach
            </select>
        @endif

        <button class="icon-btn" type="submit" title="Adicionar tarefa" wire:loading.attr="disabled">
            <i class="fa-solid fa-plus" aria-hidden="true"></i>
        </button>
    </form>
    @error('newTaskTitle')
        <p class="form-error">{{ $message }}</p>
    @enderror
    @error('newTaskListId')
        <p class="form-error">{{ $message }}</p>
    @enderror

    <section class="group" aria-expanded="true">

        <div class="group-body">
            @php
                // Junta todas as tarefas (sem e com lista) numa coleção única preservando a ordem atual
                $allMissions = collect($unlistedMissions ?? [])
                    ->concat(($lists ?? collect())->flatMap->missions)
                    ->values();
                $maxSubtasks = $maxSubtasks ?? 7;
            @endphp

            @php
                $today = \Illuminate\Support\Carbon::now($userTimezone)->startOfDay();
                $tomorrow = $today->copy()->addDay();
                $monthAbbr = [
                    1 => 'Jan',
                    2 => 'Fev',
                    3 => 'Mar',
                    4 => 'Abr',
                    5 => 'Mai',
                    6 => 'Jun',
                    7 => 'Jul',
                    8 => 'Ago',
                    9 => 'Set',
                    10 => 'Out',
                    11 => 'Nov',
                    12 => 'Dez',
                ];
            @endphp

            <div class="task-list" data-sortable-tasks>
                @forelse ($allMissions as $mission)
                    @php
                        $isActive = $mission->id === $selectedMissionId;
                        $relationLoaded = method_exists($mission, 'relationLoaded') && $mission->relationLoaded('checkpointTree');
                        $subtasksRelation = $relationLoaded ? $mission->getRelation('checkpointTree') : collect();
                        $subtasks = collect($subtasksRelation);
                        $hasSubtasks = $subtasks->isNotEmpty();
                        $missionContainsSelectedSubtask = false;

                        if ($selectedSubtaskId && $hasSubtasks) {
                            $searchTree = function ($nodes) use (&$searchTree, $selectedSubtaskId) {
                                foreach ($nodes as $node) {
                                    if (($node['id'] ?? null) === $selectedSubtaskId) {
                                        return true;
                                    }

                                    $children = $node['children'] ?? [];

                                    if ($children instanceof \Illuminate\Support\Collection) {
                                        $children = $children->all();
                                    }

                                    if (! empty($children) && $searchTree($children)) {
                                        return true;
                                    }
                                }

                                return false;
                            };

                            $missionContainsSelectedSubtask = $searchTree($subtasks->all());
                        }
                        $rootSubtaskCount = $subtasks->count();
                        $canAddMissionSubtask = $rootSubtaskCount < $maxSubtasks;
                        $missionDueDate = optional(optional($mission->due_at)->setTimezone($userTimezone))->format('Y-m-d');
                        $dueLabel = null;
                        $dueClass = 'task-due';
                        $priority = (int) ($mission->priority ?? 0);

                        if ($mission->due_at) {
                            $missionDue = $mission->due_at->copy()->setTimezone($userTimezone);
                            $missionDueDay = $missionDue->copy()->startOfDay();

                            if ($missionDueDay->equalTo($today)) {
                                $dueLabel = 'Hoje';
                                $dueClass .= ' is-today';
                            } elseif ($missionDueDay->equalTo($tomorrow)) {
                                $dueLabel = 'Amanhã';
                                $dueClass .= ' is-tomorrow';
                            } elseif ($missionDueDay->lessThan($today)) {
                                $monthKey = (int) $missionDueDay->format('n');
                                $monthLabel = $monthAbbr[$monthKey] ?? $missionDueDay->format('M');
                                $dueLabel = $missionDueDay->format('d') . ' ' . $monthLabel;
                                $dueClass .= ' is-overdue';
                            } else {
                                $monthKey = (int) $missionDueDay->format('n');
                                $monthLabel = $monthAbbr[$monthKey] ?? $missionDueDay->format('M');
                                $dueLabel = $missionDueDay->format('d') . ' ' . $monthLabel;
                            }
                        }
                    @endphp

                    <div
                        class="task-block"
                        wire:key="mission-flat-{{ $mission->id }}"
                        data-mission-id="{{ $mission->id }}"
                        data-list-id="{{ $mission->list_id ?? '' }}"
                    >
                        <div
                            wire:click="selectMission({{ $mission->id }})"
                            @class([
                                'task',
                                'done' => $mission->status === 'done',
                                'is-active' => $isActive && ! $missionContainsSelectedSubtask,
                                'has-active-subtask' => $missionContainsSelectedSubtask,
                                'has-subtasks' => $hasSubtasks,
                                'priority-high' => $priority === 3,
                                'priority-medium' => $priority === 2,
                                'priority-low' => $priority === 1,
                            ])
                            data-priority="{{ $priority }}"
                            @if($hasSubtasks)
                                aria-expanded="true"
                            @endif
                        >
                            <button class="checkbox" aria-label="Marcar tarefa" type="button"></button>
                            <div class="task-label">
                                @if ($hasSubtasks)
                                    <button class="expander" type="button" title="Recolher subtarefas" aria-label="Recolher subtarefas">
                                        <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                                    </button>
                                @endif
                                <div
                                    class="title-line"
                                    @if ($editingMissionId !== $mission->id)
                                        wire:click.stop="selectMission({{ $mission->id }})"
                                    @endif
                                >
                                    @if ($editingMissionId === $mission->id)
                                        <input
                                            type="text"
                                            class="inline-input"
                                            data-mission-input="{{ $mission->id }}"
                                            wire:model.defer="editingMissionTitle"
                                            wire:keydown.enter.prevent="saveMissionEdit({{ $mission->id }}, true)"
                                            wire:keydown.shift.enter.prevent="missionShiftEnter({{ $mission->id }})"
                                            wire:keydown.escape="cancelMissionEdit"
                                            wire:blur="saveMissionEdit({{ $mission->id }})"
                                        />
                                    @else
                                        <span class="title">
                                            {{ $mission->title ?: 'Sem título' }}
                                        </span>
                                    @endif

                                    @if ($mission->is_starred)
                                        <span class="task-pin" aria-label="Tarefa fixada" title="Tarefa fixada">
                                            <i class="fa-solid fa-thumbtack" aria-hidden="true"></i>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="task-date">
                                <label class="task-date-button" title="Alterar data da tarefa">
                                    <span @class([
                                        'task-date-chip',
                                        $dueLabel ? $dueClass : 'task-due is-empty',
                                    ])>
                                        <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                                        @if ($dueLabel)
                                            <span class="task-date-label">{{ $dueLabel }}</span>
                                        @else
                                            <span class="sr-only">Definir data</span>
                                        @endif
                                    </span>
                                    <input
                                        type="date"
                                        value="{{ $missionDueDate }}"
                                        wire:change="runInlineAction({{ $mission->id }}, 'set-date', $event.target.value)"
                                    >
                                </label>
                            </div>
                            <div class="task-menu" wire:click.stop>
                                @include('livewire.tasks.partials.inline-menu', [
                                    'context' => 'main',
                                    'missionId' => $mission->id,
                                    'dueDate' => $missionDueDate,
                                    'priority' => $mission->priority,
                                ])
                            </div>
                            {{-- Não exibimos rótulo de lista para manter apenas tarefas puras --}}
                        </div>

                        <div
                            class="subtasks"
                            data-subtask-container
                            data-mission-id="{{ $mission->id }}"
                            data-parent-id=""
                        >
                            @if ($hasSubtasks)
                                @foreach ($subtasks as $node)
                                    @include('livewire.tasks.partials.main-panel-subtask', [
                                        'item' => $node,
                                        'depth' => 0,
                                        'missionId' => $mission->id,
                                        'selectedSubtaskId' => $selectedSubtaskId,
                                        'editingSubtaskId' => $editingSubtaskId,
                                        'maxSubtasks' => $maxSubtasks,
                                        'userTimezone' => $userTimezone,
                                        'monthAbbr' => $monthAbbr,
                                        'currentDay' => $today,
                                        'nextDay' => $tomorrow,
                                    ])
                                @endforeach
                                @if (! $canAddMissionSubtask)
                                    <div class="subtasks-limit">Limite de {{ $maxSubtasks }} subtarefas atingido.</div>
                                @endif
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="task ghost">
                        <div class="checkbox" aria-hidden="true"></div>
                        <div class="title-line">
                            <span class="title" style="opacity:.6">Sem tarefas</span>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    @if ($totalCount === 0)
        <div class="empty-state">
            <p>Nenhuma tarefa cadastrada ainda. Que tal criar a primeira?</p>
        </div>
    @endif
</main>
