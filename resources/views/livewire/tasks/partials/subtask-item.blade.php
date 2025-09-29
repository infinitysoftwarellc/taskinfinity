@props([
    'item' => [],
    'depth' => 0,
    'selectedSubtaskId' => null,
])

@php
    $children = $item['children'] ?? [];
    $isDone = (bool) ($item['is_done'] ?? false);
    $title = trim((string) ($item['title'] ?? ''));
    $isSelected = ($item['id'] ?? null) === $selectedSubtaskId;
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
            @include('livewire.tasks.partials.inline-menu')
        </div>
    </div>

    @if (!empty($children))
        <ul class="ti-subtask-children" role="list">
            @foreach ($children as $child)
                @include('livewire.tasks.partials.subtask-item', [
                    'item' => $child,
                    'depth' => $depth + 1,
                    'selectedSubtaskId' => $selectedSubtaskId,
                ])
            @endforeach
        </ul>
    @endif
</li>
