{{-- This Blade view renders the app shared navigation interface. --}}
@php
    $items = [
        [
            'label' => __('Tasks'),
            'href' => route('tasks.index'),
            'icon' => 'fa-solid fa-list-check',
            'active' => request()->routeIs('tasks.index'),
        ],
        [
            'label' => __('Pomodoro'),
            'href' => route('app.pomodoro'),
            'icon' => 'fa-solid fa-clock',
            'active' => request()->routeIs('app.pomodoro'),
        ],
        [
            'label' => __('Profile'),
            'href' => route('profile'),
            'icon' => 'fa-solid fa-user',
            'active' => request()->routeIs('profile'),
        ],
        [
            'label' => __('Configurações'),
            'href' => route('app.settings'),
            'icon' => 'fa-solid fa-gear',
            'active' => request()->routeIs('app.settings'),
        ],
    ];
@endphp

<aside class="hidden w-64 shrink-0 border-r border-zinc-200 bg-white px-4 py-6 lg:block">
    <nav class="flex flex-col gap-2">
        @foreach ($items as $item)
            <a
                wire:navigate
                href="{{ $item['href'] }}"
                @class([
                    'flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition',
                    'bg-zinc-200 text-zinc-900' => $item['active'],
                    'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900' => ! $item['active'],
                ])
                aria-current="{{ $item['active'] ? 'page' : 'false' }}"
            >
                <i class="{{ $item['icon'] }} text-base" aria-hidden="true"></i>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>
</aside>
