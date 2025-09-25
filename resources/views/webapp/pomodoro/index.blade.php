<x-app-layout>
    @php
        $menuLinks = [
            [
                'label' => 'Dashboard',
                'route' => route('dashboard'),
                'active' => request()->routeIs('dashboard'),
                'icon' => '<path d="M3 9.75 12 3l9 6.75V21a.75.75 0 0 1-.75.75H3.75A.75.75 0 0 1 3 21z" />',
            ],
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
                'label' => 'Profile',
                'route' => route('profile'),
                'active' => request()->routeIs('profile'),
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0zM4.5 19.125a8.25 8.25 0 0 1 15 0" />',
                'stroke' => true,
            ],
        ];
    @endphp

    <div class="flex min-h-screen bg-gray-950 text-gray-100">
        <aside class="hidden w-20 shrink-0 flex-col items-center border-r border-white/10 bg-black/30 py-6 md:flex">
            <a href="{{ route('dashboard') }}" class="mb-8 flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-500 text-white transition hover:bg-indigo-400">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75" />
                </svg>
            </a>
            <nav class="flex flex-1 flex-col items-center gap-4">
                @foreach ($menuLinks as $link)
                    <a
                        href="{{ $link['route'] }}"
                        class="flex h-12 w-12 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-white/70 transition hover:text-white {{ ($link['active'] ?? false) ? 'border-indigo-400 text-white' : '' }}"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="{{ ($link['stroke'] ?? false) ? 'none' : 'currentColor' }}" stroke="currentColor" stroke-width="1.5">
                            {!! $link['icon'] !!}
                        </svg>
                    </a>
                @endforeach
            </nav>
            <form method="POST" action="{{ route('logout') }}" class="mt-8">
                @csrf
                <button type="submit" class="flex h-12 w-12 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-white/70 transition hover:text-white">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6A2.25 2.25 0 0 0 5.25 5.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9l3 3m0 0-3 3m3-3H3" />
                    </svg>
                </button>
            </form>
        </aside>

        <div class="flex flex-1 flex-col gap-6 p-4 sm:p-6">
            <div class="md:hidden">
                <nav class="mb-4 flex items-center gap-3 overflow-x-auto rounded-2xl border border-white/10 bg-black/30 p-3 text-sm text-white/70">
                    @foreach ($menuLinks as $link)
                        <a
                            href="{{ $link['route'] }}"
                            class="flex items-center gap-2 rounded-xl px-3 py-2 transition hover:text-white {{ ($link['active'] ?? false) ? 'bg-indigo-500 text-white' : 'bg-white/5' }}"
                        >
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="{{ ($link['stroke'] ?? false) ? 'none' : 'currentColor' }}" stroke="currentColor" stroke-width="1.5">
                                {!! $link['icon'] !!}
                            </svg>
                            <span class="whitespace-nowrap">{{ $link['label'] }}</span>
                        </a>
                    @endforeach
                </nav>
            </div>

            <livewire:pomodoro.timer />
        </div>
    </div>
</x-app-layout>
