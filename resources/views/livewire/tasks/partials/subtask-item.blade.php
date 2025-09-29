@props([
    'item' => [],
    'depth' => 0,
])

@php
    $children = $item['children'] ?? [];
    $isDone = (bool) ($item['is_done'] ?? false);
    $title = trim((string) ($item['title'] ?? ''));
@endphp

<li class="ti-subtask-item" data-depth="{{ $depth }}">
    <div class="ti-subtask-row subtask {{ $isDone ? 'is-done' : '' }}">
        <button class="checkbox" type="button" title="Concluir subtarefa"></button>
        <div class="ti-subtask-main">
            <span class="ti-subtask-title">{{ $title !== '' ? $title : 'Sem título' }}</span>
            @if (!empty($item['due_at']))
                <span class="ti-subtask-meta">{{ $item['due_at'] }}</span>
            @endif
        </div>
        @include('livewire.tasks.partials.inline-menu')
    </div>

    @if (!empty($children))
        <ul class="ti-subtask-children" role="list">
            @foreach ($children as $child)
                @include('livewire.tasks.partials.subtask-item', ['item' => $child, 'depth' => $depth + 1])
            @endforeach
        </ul>
    @endif
</li>
