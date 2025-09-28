@php
    $workspaceExpanded = (bool) data_get($workspace, 'expanded', true);
    $workspaceStyle = 'padding-left:8px;';
    if (! $workspaceExpanded) {
        $workspaceStyle = 'padding-left:8px; display:none;';
    }
@endphp

<aside class="sidebar panel">
    <h6>Atalhos</h6>
    <nav>
        <ul class="nav-list">
            @foreach ($shortcuts as $index => $shortcut)
                <li wire:key="shortcut-{{ $index }}">
                    <a class="nav-item" href="{{ $shortcut['href'] ?? '#' }}">
                        <i class="icon" data-lucide="{{ $shortcut['icon'] ?? '' }}"></i>
                        <span class="label">{{ $shortcut['label'] ?? '' }}</span>
                        @if (!empty($shortcut['count']))
                            <span class="count">{{ $shortcut['count'] }}</span>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>

    <div class="sep"></div>

    <button class="workspace" aria-expanded="{{ $workspaceExpanded ? 'true' : 'false' }}" data-toggle="workspace">
        <i class="chev" data-lucide="chevron-down"></i>
        <span class="title">{{ data_get($workspace, 'title', '') }}</span>
        @if ($badge = data_get($workspace, 'badge'))
            <span class="badge">{{ $badge }}</span>
        @endif

        <button class="btn-new" title="Nova lista">
            <i data-lucide="plus"></i>
        </button>
    </button>

    <div class="workspace-content" style="{{ $workspaceStyle }}">
        <ul class="nav-list">
            @foreach (data_get($workspace, 'items', []) as $index => $item)
                <li wire:key="workspace-item-{{ $index }}">
                    <a class="nav-item" href="{{ $item['href'] ?? '#' }}">
                        <i class="icon" data-lucide="{{ $item['icon'] ?? '' }}"></i>
                        <span class="label">{{ $item['label'] ?? '' }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    <h6>Filters</h6>
    <div class="filters-tip">{{ $filtersTip }}</div>

    <h6>Tags</h6>
    <div class="tags">
        @foreach ($tags as $index => $tag)
            <a class="tag" href="{{ $tag['href'] ?? '#' }}" wire:key="tag-{{ $index }}">
                <span class="dot" style="background:{{ $tag['color'] ?? '#ccc' }}"></span>
                <span>{{ $tag['label'] ?? '' }}</span>
                @if (!empty($tag['count']))
                    <span class="count" style="margin-left:auto">{{ $tag['count'] }}</span>
                @endif
            </a>
        @endforeach
    </div>

    <h6 style="margin-top:14px"> </h6>
    <div class="completed">
        <i class="icon" data-lucide="check-square"></i>
        {{ $completedLabel }}
    </div>
</aside>
