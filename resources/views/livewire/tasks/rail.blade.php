@php
    $iconMap = [
        'list-checks' => 'fa-solid fa-list-check',
        'sun' => 'fa-solid fa-sun',
        'calendar-days' => 'fa-solid fa-calendar-days',
        'inbox' => 'fa-solid fa-inbox',
        'pie-chart' => 'fa-solid fa-chart-pie',
        'settings' => 'fa-solid fa-gear',
    ];
@endphp

<aside class="rail">
    <div class="avatar" title="{{ $avatarLabel }}"></div>

    @foreach ($primaryButtons as $index => $button)
        @php
            $iconClass = $iconMap[$button['icon'] ?? ''] ?? 'fa-solid fa-circle';
        @endphp
        <button class="btn" title="{{ $button['title'] ?? '' }}" wire:key="rail-primary-{{ $index }}">
            <i class="{{ $iconClass }}" aria-hidden="true"></i>
        </button>
    @endforeach

    <div class="spacer"></div>

    @foreach ($secondaryButtons as $index => $button)
        @php
            $iconClass = $iconMap[$button['icon'] ?? ''] ?? 'fa-solid fa-circle';
        @endphp
        <button class="btn" title="{{ $button['title'] ?? '' }}" wire:key="rail-secondary-{{ $index }}">
            <i class="{{ $iconClass }}" aria-hidden="true"></i>
        </button>
    @endforeach
</aside>
