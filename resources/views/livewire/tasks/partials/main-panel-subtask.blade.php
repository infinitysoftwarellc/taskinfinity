@props([
    'item' => [],
    'depth' => 0,
    'missionId',
    'selectedSubtaskId' => null,
    'editingSubtaskId' => null,
])

@php
    $children = collect($item['children'] ?? []);
    $isDone = (bool) ($item['is_done'] ?? false);
    $isActive = ($item['id'] ?? null) === $selectedSubtaskId;
    $isEditing = ($item['id'] ?? null) === $editingSubtaskId;
    $title = trim((string) ($item['title'] ?? ''));
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
    ])
>
    <button
        class="checkbox {{ $isDone ? 'checked' : '' }}"
        type="button"
        aria-label="Marcar subtarefa"
        wire:click.stop="toggleSubtaskCompletion({{ $missionId }}, {{ $item['id'] }})"
    ></button>

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
        <button
            type="button"
            class="subtask-quick-btn"
            title="Adicionar subtarefa irmã"
            wire:click.stop="createSiblingSubtask({{ $item['id'] }})"
        >
            <i data-lucide="plus"></i>
        </button>
        <button
            type="button"
            class="subtask-quick-btn"
            title="Adicionar subtarefa filha"
            wire:click.stop="createChildSubtask({{ $item['id'] }})"
        >
            <i data-lucide="corner-down-right"></i>
        </button>
        @include('livewire.tasks.partials.inline-menu')
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
            ])
        @endforeach
    </div>
@endif
