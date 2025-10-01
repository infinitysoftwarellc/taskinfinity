{{-- This Blade view renders the livewire tasks partials main panel subtask interface. --}}
@props([
    'item' => [],
    'depth' => 0,
    'missionId',
    'selectedSubtaskId' => null,
    'editingSubtaskId' => null,
    'maxSubtasks' => 7,
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
        ])
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
        <div class="subtask-menu" wire:click.stop>
            @include('livewire.tasks.partials.inline-menu', [
                'context' => 'subtask',
                'missionId' => $missionId,
                'subtaskId' => $item['id'] ?? null,
            ])
        </div>
    </div>

    <div
        @class(['subtask-group', 'is-empty' => $children->isEmpty()])
        data-subtask-container
        data-mission-id="{{ $missionId }}"
        data-parent-id="{{ $item['id'] }}"
    >
        @if ($children->isNotEmpty())
            @foreach ($children as $child)
                @include('livewire.tasks.partials.main-panel-subtask', [
                    'item' => $child,
                    'depth' => $depth + 1,
                    'missionId' => $missionId,
                    'selectedSubtaskId' => $selectedSubtaskId,
                    'editingSubtaskId' => $editingSubtaskId,
                    'maxSubtasks' => $maxSubtasks,
                ])
            @endforeach
            @if (! $canAddChild)
                <div class="subtasks-limit">Limite de {{ $maxSubtasks }} subtarefas atingido.</div>
            @endif
        @else
            <div class="subtask-drop-placeholder" aria-hidden="true">
                Solte aqui para transformar em subtarefa
            </div>
        @endif
    </div>
</div>
