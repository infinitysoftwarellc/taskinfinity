@props([
    'item' => [],
    'depth' => 0,
    'selectedSubtaskId' => null,
    'maxSubtasks' => 7,
])

@php
    $children = $item['children'] ?? [];
    $isDone = (bool) ($item['is_done'] ?? false);
    $title = trim((string) ($item['title'] ?? ''));
    $isSelected = ($item['id'] ?? null) === $selectedSubtaskId;
    $childrenCount = count($children);
    $canAddChild = $childrenCount < $maxSubtasks;
@endphp

<li class="ti-subtask-item" data-depth="{{ $depth }}" wire:key="detail-subtask-{{ $item['id'] ?? 'temp' }}">
    <div
        class="ti-subtask-row subtask {{ $isDone ? 'is-done' : '' }} {{ $isSelected ? 'is-active' : '' }}"
        wire:click="selectCheckpoint({{ $item['id'] }})"
    >
        <button
            class="checkbox {{ $isDone ? 'checked' : '' }}"
            type="button"
            title="Concluir subtarefa"
            wire:click.stop="toggleCheckpoint({{ $item['id'] }})"
        ></button>
        <div class="ti-subtask-main">
            <span class="ti-subtask-title">{{ $title !== '' ? $title : 'Sem título' }}</span>
            @if (!empty($item['due_at']))
                <span class="ti-subtask-meta">{{ $item['due_at'] }}</span>
            @endif
        </div>
        <div class="ti-subtask-actions" wire:click.stop>
            @if ($canAddChild)
                <button
                    class="icon ghost"
                    type="button"
                    title="Adicionar subtarefa"
                    wire:click="openSubtaskForm({{ $item['id'] }})"
                >
                    <i class="fa-solid fa-plus" aria-hidden="true"></i>
                </button>
            @endif
            @include('livewire.tasks.partials.inline-menu', [
                'context' => 'details',
            ])
        </div>
    </div>

    @if (!empty($children))
        <ul class="ti-subtask-children" role="list">
            @foreach ($children as $child)
                @include('livewire.tasks.partials.subtask-item', [
                    'item' => $child,
                    'depth' => $depth + 1,
                    'selectedSubtaskId' => $selectedSubtaskId,
                    'maxSubtasks' => $maxSubtasks,
                ])
            @endforeach
        </ul>
        @if (! $canAddChild)
            <div class="subtasks-limit">Limite de {{ $maxSubtasks }} subtarefas atingido.</div>
        @endif
    @endif
</li>
