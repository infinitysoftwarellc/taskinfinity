{{-- This Blade view renders the livewire tasks partials main panel subtask interface. --}}
@props([
    'item' => [],
    'depth' => 0,
    'missionId',
    'selectedSubtaskId' => null,
    'editingSubtaskId' => null,
    'maxSubtasks' => 7,
    'userTimezone' => null,
    'monthAbbr' => [],
    'currentDay' => null,
    'nextDay' => null,
    'collapsedSubtaskIds' => [],
])

@php
    $children = collect($item['children'] ?? []);
    $isDone = (bool) ($item['is_done'] ?? false);
    $isActive = ($item['id'] ?? null) === $selectedSubtaskId;
    $isEditing = ($item['id'] ?? null) === $editingSubtaskId;
    $title = trim((string) ($item['title'] ?? ''));
    $childrenCount = $children->count();
    $hasChildren = $childrenCount > 0;
    $canAddChild = $childrenCount < $maxSubtasks;
    $timezone = $userTimezone ?: config('app.timezone');
    $monthLabels = $monthAbbr ?: [];
    $today = $currentDay instanceof \Illuminate\Support\Carbon ? $currentDay->copy()->startOfDay() : \Illuminate\Support\Carbon::now($timezone)->startOfDay();
    $tomorrow = $nextDay instanceof \Illuminate\Support\Carbon ? $nextDay->copy()->startOfDay() : $today->copy()->addDay();
    $rawDueAt = $item['due_at'] ?? null;
    $subtaskDueDate = null;
    $subtaskDueLabel = null;
    $subtaskDueClass = 'subtask-due';
    $subtaskDueIso = null;
    $isCollapsed = in_array($item['id'] ?? null, $collapsedSubtaskIds, true);

    if ($rawDueAt) {
        try {
            $dueDate = \Illuminate\Support\Carbon::parse($rawDueAt)->setTimezone($timezone);
            $subtaskDueDate = $dueDate->format('Y-m-d');
            $subtaskDueIso = $dueDate->toIso8601String();
            $dueDay = $dueDate->copy()->startOfDay();

            if ($dueDay->equalTo($today)) {
                $subtaskDueLabel = 'Hoje';
                $subtaskDueClass .= ' is-today';
            } elseif ($dueDay->equalTo($tomorrow)) {
                $subtaskDueLabel = 'Amanhã';
                $subtaskDueClass .= ' is-tomorrow';
            } else {
                $monthKey = (int) $dueDay->format('n');
                $monthLabel = $monthLabels[$monthKey] ?? $dueDay->format('M');
                $subtaskDueLabel = $dueDay->format('d') . ' ' . $monthLabel;

                if ($dueDay->lessThan($today)) {
                    $subtaskDueClass .= ' is-overdue';
                }
            }
        } catch (\Throwable $e) {
            $subtaskDueDate = null;
            $subtaskDueLabel = null;
            $subtaskDueClass = 'subtask-due is-empty';
        }
    }
@endphp

<div
    class="subtask-node"
    role="listitem"
    wire:key="mp-subtask-{{ $item['id'] }}"
    wire:sortable.item="{{ $item['id'] }}"
    data-subtask-node
    data-subtask-id="{{ $item['id'] }}"
    data-mission-id="{{ $missionId }}"
    data-parent-id="{{ $item['parent_id'] ?? '' }}"
>
    <div
        class="subtask"
        data-depth="{{ $depth }}"
        style="--subtask-depth: {{ $depth }};"
        wire:click="selectSubtask({{ $missionId }}, {{ $item['id'] }})"
        @class([
            'done' => $isDone,
            'is-active' => $isActive,
            'has-children' => $hasChildren,
        ])
        @if($hasChildren)
            aria-expanded="{{ $isCollapsed ? 'false' : 'true' }}"
        @endif
    >
        <button
            class="checkbox {{ $isDone ? 'checked' : '' }}"
            type="button"
            aria-label="Marcar subtarefa"
            wire:click.stop="toggleSubtaskCompletion({{ $missionId }}, {{ $item['id'] }})"
        ></button>
        <div class="subtask-label">
            @if ($hasChildren)
                <button
                    class="expander"
                    type="button"
                    title="Recolher subtarefas"
                    aria-label="Recolher subtarefas"
                    wire:click.stop="toggleSubtaskCollapse({{ $missionId }}, {{ $item['id'] }})"
                >
                    <i class="fa-solid {{ $isCollapsed ? 'fa-chevron-right' : 'fa-chevron-down' }}" aria-hidden="true"></i>
                </button>
            @endif

            <div
                class="title-line"
                @if (! $isEditing)
                    wire:click.stop="selectSubtask({{ $missionId }}, {{ $item['id'] }})"
                @endif
            >
                @if ($isEditing)
                    <input
                        type="text"
                        class="inline-input"
                        data-subtask-input="{{ $item['id'] }}"
                        wire:model.defer="editingSubtaskTitle"
                        wire:keydown.enter.prevent="saveSubtaskEdit({{ $item['id'] }}, true)"
                        wire:keydown.shift.enter.prevent="saveSubtaskEdit({{ $item['id'] }}, false, true)"
                        wire:keydown.escape="cancelSubtaskEdit"
                        wire:blur="saveSubtaskEdit({{ $item['id'] }})"
                    />
                @else
                    <span class="title">
                        {{ $title !== '' ? $title : 'Sem título' }}
                    </span>
                @endif
            </div>
        </div>
        <div class="subtask-date">
            <label class="subtask-date-button" title="Alterar data da subtarefa">
                <span @class([
                    'subtask-date-chip',
                    $subtaskDueLabel ? $subtaskDueClass : 'subtask-due is-empty',
                ])>
                    <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                    @if ($subtaskDueLabel)
                        <span
                            class="subtask-date-label"
                            data-relative-datetime="{{ $subtaskDueIso }}"
                            data-relative-tz="{{ $timezone }}"
                            data-relative-fallback="{{ $subtaskDueLabel }}"
                        >
                            {{ $subtaskDueLabel }}
                        </span>
                    @else
                        <span class="sr-only">Definir data</span>
                    @endif
                </span>
                <input
                    type="date"
                    value="{{ $subtaskDueDate }}"
                    data-flatpickr
                    data-flatpickr-date-format="Y-m-d"
                    data-flatpickr-alt-format="d/m/Y"
                    x-data="{}"
                    x-init="$flatpickr()"
                    wire:change="runInlineAction({{ $missionId }}, 'set-date', $event.target.value, {{ $item['id'] }})"
                >
            </label>
        </div>
        <div class="subtask-menu" wire:click.stop>
            @include('livewire.tasks.partials.inline-menu', [
                'context' => 'subtask',
                'missionId' => $missionId,
                'subtaskId' => $item['id'] ?? null,
                'dueDate' => $subtaskDueDate,
            ])
        </div>
    </div>
    @if ($children->isNotEmpty())
            <div
                class="subtask-group"
                role="list"
                wire:sortable="reorderSubtasks"
                data-subtask-container
                data-mission-id="{{ $missionId }}"
                data-parent-id="{{ $item['id'] ?? '' }}"
                x-data
                x-auto-animate
                @if ($isCollapsed)
                    style="display:none;"
                @endif
            >
                @foreach ($children as $child)
                    @include('livewire.tasks.partials.main-panel-subtask', [
                        'item' => $child,
                        'depth' => $depth + 1,
                        'missionId' => $missionId,
                        'selectedSubtaskId' => $selectedSubtaskId,
                        'editingSubtaskId' => $editingSubtaskId,
                        'maxSubtasks' => $maxSubtasks,
                        'collapsedSubtaskIds' => $collapsedSubtaskIds,
                    ])
                @endforeach
                @if (! $canAddChild)
                    <div class="subtasks-limit">Limite de {{ $maxSubtasks }} subtarefas atingido.</div>
                @endif
            </div>
        @endif
</div>
