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
            'label' => __('Hábitos'),
            'href' => route('app.habits'),
            'icon' => 'fa-solid fa-leaf',
            'active' => request()->routeIs('app.habits'),
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

<aside class="hidden w-64 shrink-0 border-r border-zinc-200 bg-white px-4 py-6 dark:border-zinc-700 dark:bg-zinc-900 lg:block">
    <nav class="flex flex-col gap-2">
        @foreach ($items as $item)
            <a
                wire:navigate
                href="{{ $item['href'] }}"
                @class([
                    'flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition',
                    'bg-zinc-200 text-zinc-900 dark:bg-zinc-700 dark:text-white' => $item['active'],
                    'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white' => ! $item['active'],
                ])
                aria-current="{{ $item['active'] ? 'page' : 'false' }}"
            >
                <i class="{{ $item['icon'] }} text-base" aria-hidden="true"></i>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>
</aside>
