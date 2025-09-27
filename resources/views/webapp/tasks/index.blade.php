<x-app-layout>
    @php
        $menuLinks = [
            [
                'label' => 'Tasks',
                'route' => route('tasks.index'),
                'active' => request()->routeIs('tasks.*'),
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 5.25h16.5M3.75 9.75h16.5M3.75 14.25h16.5M3.75 18.75h16.5" />',
                'stroke' => true,
            ],
            [
                'label' => 'Pomodoro',
                'route' => route('pomodoro'),
                'active' => request()->routeIs('pomodoro'),
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5c-4.142 0-7.5 3.134-7.5 7s3.358 7 7.5 7 7.5-3.134 7.5-7" />',
                'stroke' => true,
            ],
            [
                'label' => 'Habits',
                'route' => route('habits'),
                'active' => request()->routeIs('habits'),
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3.75v2.25m10.5-2.25v2.25M4.5 9.75h15M6 7.5h12a1.5 1.5 0 0 1 1.5 1.5v9a1.5 1.5 0 0 1-1.5 1.5H6A1.5 1.5 0 0 1 4.5 18V9A1.5 1.5 0 0 1 6 7.5z" />',
                'stroke' => true,
            ],
            [
                'label' => 'Profile',
                'route' => route('profile'),
                'active' => request()->routeIs('profile'),
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0zM4.5 19.125a8.25 8.25 0 0 1 15 0" />',
                'stroke' => true,
            ],
        ];

        $taskViews = [
            [
                'label' => 'Lista',
                'route' => route('tasks.index', $view ? ['view' => $view] : []),
                'active' => request()->routeIs('tasks.index') || request()->routeIs('tasks.lists.show'),
            ],
            [
                'label' => 'Kanban',
                'route' => route('tasks.board'),
                'active' => request()->routeIs('tasks.board'),
                'visible' => false,
            ],
            [
                'label' => 'Linha do tempo',
                'route' => route('tasks.timeline'),
                'active' => request()->routeIs('tasks.timeline'),
                'visible' => false,
            ],
        ];
    @endphp

    <div class="tasks-page">
        <header class="tasks-topbar">
            <div class="tasks-topbar-brand">Task Infinity</div>
            <nav class="tasks-topnav">
                @foreach ($menuLinks as $link)
                    <a
                        href="{{ $link['route'] }}"
                        class="tasks-topnav-btn {{ ($link['active'] ?? false) ? 'is-active' : '' }}"
                    >
                        <span class="tasks-topnav-icon">
                            <svg viewBox="0 0 24 24" fill="{{ ($link['stroke'] ?? false) ? 'none' : 'currentColor' }}" stroke="currentColor" stroke-width="1.5">
                                {!! $link['icon'] !!}
                            </svg>
                        </span>
                        <span>{{ $link['label'] }}</span>
                    </a>
                @endforeach
            </nav>
            <form method="POST" action="{{ route('logout') }}" class="tasks-logout">
                @csrf
                <button type="submit" title="{{ __('Log out') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6A2.25 2.25 0 0 0 5.25 5.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9l3 3m0 0-3 3m3-3H3" />
                    </svg>
                </button>
            </form>
        </header>

        <div class="tasks-shell">
            <livewire:task.sidebar :active-list-id="$list?->id" :active-view="$view" :key="'task-sidebar-' . ($list?->id ?? $view ?? 'empty')" />

            <div class="tasks-main">
                <nav class="tasks-view-nav">
                    @foreach ($taskViews as $taskView)
                        @if (!($taskView['visible'] ?? true))
                            @continue
                        @endif
                        <a
                            href="{{ $taskView['route'] }}"
                            class="tasks-view-btn {{ ($taskView['active'] ?? false) ? 'is-active' : '' }}"
                        >
                            {{ $taskView['label'] }}
                        </a>
                    @endforeach
                </nav>

                <livewire:task.workspace :list-id="$list?->id" :view="$view" :key="'task-workspace-' . ($list?->id ?? $view ?? 'empty')" />
            </div>
        </div>
    </div>
</x-app-layout>
