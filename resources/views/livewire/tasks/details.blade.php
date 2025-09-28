<aside class="details panel">
    <div class="header">
        <div style="font-weight:700; color:var(--muted)">{{ data_get($details, 'owner', '') }} </div>
        <div class="right">
            @foreach (data_get($details, 'actions', []) as $index => $action)
                <button class="icon-btn" title="{{ $action['title'] ?? '' }}" wire:key="detail-action-{{ $index }}">
                    <i data-lucide="{{ $action['icon'] ?? '' }}"></i>
                </button>
            @endforeach
        </div>
    </div>
    <div class="empty">
        <h3 style="margin:6px 0 6px; font-size:18px; color:#d7def0">{{ data_get($details, 'emptyTitle', '') }}</h3>
        <p>{{ data_get($details, 'emptyDescription', '') }}</p>
    </div>
</aside>
