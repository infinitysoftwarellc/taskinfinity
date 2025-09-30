{{-- This Blade view renders the livewire tasks partials main panel subtask interface. --}}
@props([
    'item' => [],
    'depth' => 0,
    'missionId',
    'selectedSubtaskId' => null,
    'editingSubtaskId' => null,
    'siblingsCount' => 0,
    'maxSubtasks' => 7,
    'userTimezone' => null,
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
    $canAddSibling = $siblingsCount < $maxSubtasks;
    $timezone = $userTimezone ?? (auth()->user()?->timezone ?? config('app.timezone'));
    $dueDate = null;
    $dueLabel = null;
    $dueClass = 'subtask-due';
    $rawDueAt = $item['due_at'] ?? null;
    $priority = (int) ($item['priority'] ?? 0);

    if ($rawDueAt instanceof \Carbon\CarbonInterface) {
        $dueAt = $rawDueAt->copy()->setTimezone($timezone);
        $dueDate = $dueAt->format('Y-m-d');

        $dueDay = $dueAt->copy()->startOfDay();
        $today = \Illuminate\Support\Carbon::now($timezone)->startOfDay();
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

        if ($dueDay->equalTo($today)) {
            $dueLabel = 'Hoje';
            $dueClass .= ' is-today';
        } elseif ($dueDay->equalTo($tomorrow)) {
            $dueLabel = 'Amanhã';
            $dueClass .= ' is-tomorrow';
        } elseif ($dueDay->lessThan($today)) {
            $monthKey = (int) $dueDay->format('n');
            $monthLabel = $monthAbbr[$monthKey] ?? $dueDay->format('M');
            $dueLabel = $dueDay->format('d') . ' ' . $monthLabel;
            $dueClass .= ' is-overdue';
        } else {
            $monthKey = (int) $dueDay->format('n');
            $monthLabel = $monthAbbr[$monthKey] ?? $dueDay->format('M');
            $dueLabel = $dueDay->format('d') . ' ' . $monthLabel;
        }
    }
@endphp

<div
    class="subtask-node"
    wire:key="mp-subtask-{{ $item['id'] }}"
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
            'priority-high' => $priority === 3,
            'priority-medium' => $priority === 2,
            'priority-low' => $priority === 1,
        ])
        data-priority="{{ $priority }}"
        @if($hasChildren)
            aria-expanded="true"
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
                <button class="expander" type="button" title="Recolher subtarefas" aria-label="Recolher subtarefas">
                    <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
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
                        wire:keydown.enter.prevent="saveSubtaskEdit({{ $item['id'] }})"
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
                <span class="{{ $rawDueAt ? $dueClass : 'subtask-due is-empty' }}">
                    {{ $dueLabel ?? 'Definir data' }}
                </span>
                <input
                    type="date"
                    value="{{ $dueDate }}"
                    wire:change="runInlineAction({{ $missionId }}, 'set-date', $event.target.value, {{ $item['id'] }})"
                >
            </label>
        </div>

        <div class="subtask-menu" wire:click.stop>
            @include('livewire.tasks.partials.inline-menu', [
                'context' => 'main',
                'missionId' => $missionId,
                'subtaskId' => $item['id'] ?? null,
                'dueDate' => $dueDate,
                'priority' => $item['priority'] ?? null,
            ])
        </div>
    </div>

    @if ($children->isNotEmpty())
        <div
            class="subtask-group"
            data-subtask-container
            data-mission-id="{{ $missionId }}"
            data-parent-id="{{ $item['id'] }}"
        >
            @foreach ($children as $child)
                @include('livewire.tasks.partials.main-panel-subtask', [
                    'item' => $child,
                    'depth' => $depth + 1,
                    'missionId' => $missionId,
                    'selectedSubtaskId' => $selectedSubtaskId,
                    'editingSubtaskId' => $editingSubtaskId,
                    'siblingsCount' => $childrenCount,
                    'maxSubtasks' => $maxSubtasks,
                    'userTimezone' => $userTimezone,
                ])
            @endforeach
            @if (! $canAddChild)
                <div class="subtasks-limit">Limite de {{ $maxSubtasks }} subtarefas atingido.</div>
            @endif
        </div>
    @endif
</div>
