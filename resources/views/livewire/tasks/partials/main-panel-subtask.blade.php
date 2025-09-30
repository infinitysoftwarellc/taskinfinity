@props([
    'item' => [],
    'depth' => 0,
    'missionId',
    'selectedSubtaskId' => null,
    'editingSubtaskId' => null,
    'siblingsCount' => 0,
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
    $canAddSibling = $siblingsCount < $maxSubtasks;
@endphp

<div
    class="subtask"
    data-depth="{{ $depth }}"
    style="--subtask-depth: {{ $depth }};"
    wire:key="mp-subtask-{{ $item['id'] }}"
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
    @if ($hasChildren)
        <button class="expander" type="button" title="Recolher subtarefas" aria-label="Recolher subtarefas">
            <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
        </button>
    @endif

    <div class="title-line">
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
            <span class="title" wire:click.stop="startSubtaskEdit({{ $item['id'] }})">
                {{ $title !== '' ? $title : 'Sem título' }}
            </span>
        @endif
    </div>

    <div class="subtask-actions" wire:click.stop>
        @if ($canAddSibling)
            <button
                type="button"
                class="subtask-quick-btn"
                title="Adicionar subtarefa irmã"
                wire:click.stop="createSiblingSubtask({{ $item['id'] }})"
            >
                <i class="fa-solid fa-plus" aria-hidden="true"></i>
            </button>
        @endif
        @if ($canAddChild)
            <button
                type="button"
                class="subtask-quick-btn"
                title="Adicionar subtarefa filha"
                wire:click.stop="createChildSubtask({{ $item['id'] }})"
            >
                <i class="fa-solid fa-turn-down" aria-hidden="true"></i>
            </button>
        @endif
        @include('livewire.tasks.partials.inline-menu', [
            'context' => 'main',
            'missionId' => $missionId,
        ])
    </div>
</div>

@if ($children->isNotEmpty())
    <div class="subtask-group">
        @foreach ($children as $child)
            @include('livewire.tasks.partials.main-panel-subtask', [
                'item' => $child,
                'depth' => $depth + 1,
                'missionId' => $missionId,
                'selectedSubtaskId' => $selectedSubtaskId,
                'editingSubtaskId' => $editingSubtaskId,
                'siblingsCount' => $childrenCount,
                'maxSubtasks' => $maxSubtasks,
            ])
        @endforeach
        @if (! $canAddChild)
            <div class="subtasks-limit">Limite de {{ $maxSubtasks }} subtarefas atingido.</div>
        @endif
    </div>
@endif
