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

    <div
        class="min-h-screen bg-[#0f1216] text-[#e8eef5]"
        style="background: radial-gradient(1200px 800px at 20% 0%, #0d1116 15%, #0a0c10 60%) fixed, #0f1216;"
    >
        <div class="flex min-h-screen flex-col gap-6 p-4 sm:p-6">
            <div class="md:hidden">
                <nav class="flex items-center gap-3 overflow-x-auto rounded-[18px] border border-[#212832] bg-[#151a21]/80 p-3 text-sm text-[#8b96a5] shadow-lg shadow-black/30">
                    @foreach ($menuLinks as $link)
                        <a
                            href="{{ $link['route'] }}"
                            class="flex items-center gap-2 rounded-[14px] border border-transparent px-3 py-2 transition {{ ($link['active'] ?? false) ? 'bg-[#3b82f6] text-white shadow-lg shadow-[#3b82f6]/40' : 'bg-[#13171c]/80 text-[#8b96a5] hover:border-[#212832] hover:text-white' }}"
                        >
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="{{ ($link['stroke'] ?? false) ? 'none' : 'currentColor' }}" stroke="currentColor" stroke-width="1.5">
                                {!! $link['icon'] !!}
                            </svg>
                            <span class="whitespace-nowrap">{{ $link['label'] }}</span>
                        </a>
                    @endforeach
                </nav>
            </div>

            <div class="grid flex-1 gap-4 md:grid-cols-[64px_minmax(0,1fr)] xl:grid-cols-[64px_300px_minmax(0,1fr)]">
                <aside class="hidden h-full flex-col items-center rounded-[18px] border border-[#212832] bg-[#0c0f13]/90 p-4 text-[#8b96a5] shadow-2xl shadow-black/40 md:flex">
                    <a href="{{ route('tasks.index') }}" class="mb-6 flex h-12 w-12 items-center justify-center rounded-[14px] bg-gradient-to-br from-[#3b82f6] to-[#8b5cf6] text-sm font-semibold text-white shadow-lg shadow-[#3b82f6]/40">
                        TI
                    </a>
                    <nav class="flex flex-1 flex-col items-center gap-3">
                        @foreach ($menuLinks as $link)
                            <a
                                href="{{ $link['route'] }}"
                                class="flex h-11 w-11 items-center justify-center rounded-[12px] border border-transparent text-lg transition {{ ($link['active'] ?? false) ? 'border-[#212832] bg-[#151a21] text-white shadow-lg shadow-black/40' : 'bg-transparent text-[#8b96a5] hover:border-[#212832] hover:bg-[#151a21] hover:text-white' }}"
                                title="{{ $link['label'] }}"
                            >
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="{{ ($link['stroke'] ?? false) ? 'none' : 'currentColor' }}" stroke="currentColor" stroke-width="1.5">
                                    {!! $link['icon'] !!}
                                </svg>
                            </a>
                        @endforeach
                    </nav>
                    <div class="mt-4 w-full border-t border-[#212832]/70"></div>
                    <form method="POST" action="{{ route('logout') }}" class="mt-4 w-full">
                        @csrf
                        <button type="submit" class="flex h-11 w-full items-center justify-center rounded-[12px] border border-transparent text-sm text-[#8b96a5] transition hover:border-[#212832] hover:bg-[#151a21] hover:text-white">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6A2.25 2.25 0 0 0 5.25 5.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9l3 3m0 0-3 3m3-3H3" />
                            </svg>
                        </button>
                    </form>
                </aside>

                <div class="hidden xl:block">
                    <livewire:task.sidebar :active-list-id="$list?->id" :active-view="$view" :key="'task-sidebar-desktop-' . ($list?->id ?? $view ?? 'empty')" />
                </div>

                <div class="flex flex-col gap-4">
                    <div class="xl:hidden">
                        <livewire:task.sidebar :active-list-id="$list?->id" :active-view="$view" :key="'task-sidebar-mobile-' . ($list?->id ?? $view ?? 'empty')" />
                    </div>

                    <nav class="flex items-center gap-2 overflow-x-auto rounded-[18px] border border-[#212832] bg-[#151a21]/80 p-2 text-xs font-semibold uppercase tracking-wide text-[#8b96a5] shadow-lg shadow-black/30">
                        @foreach ($taskViews as $taskView)
                            @if (!($taskView['visible'] ?? true))
                                @continue
                            @endif
                            <a
                                href="{{ $taskView['route'] }}"
                                class="rounded-[14px] px-4 py-2 transition {{ ($taskView['active'] ?? false) ? 'bg-[#3b82f6] text-white shadow-lg shadow-[#3b82f6]/40' : 'bg-transparent text-[#8b96a5] hover:border hover:border-[#212832] hover:bg-[#151a21] hover:text-white' }}"
                            >
                                {{ $taskView['label'] }}
                            </a>
                        @endforeach
                    </nav>

                    <livewire:task.workspace :list-id="$list?->id" :view="$view" :key="'task-workspace-' . ($list?->id ?? $view ?? 'empty')" />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
