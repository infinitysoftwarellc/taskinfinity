<section class="space-y-6">
    <header class="flex flex-col justify-between gap-4 rounded-3xl border border-white/5 bg-white/5 p-6 text-sm backdrop-blur md:flex-row md:items-center">
        <div>
            <p class="text-xs uppercase tracking-wider text-white/60">All Tasks</p>
            <h1 class="text-2xl font-semibold text-white">All</h1>
        </div>
        <div class="flex flex-1 flex-col gap-3 md:flex-row md:items-center md:justify-end">
            <div class="relative w-full md:max-w-xs">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/40">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z" />
                    </svg>
                </span>
                <input type="search" placeholder="Search tasks" class="w-full rounded-2xl border border-white/10 bg-black/40 py-2 pl-9 pr-4 text-sm text-white placeholder-white/40 focus:border-white/30 focus:outline-none focus:ring-0" />
            </div>
            <div class="flex items-center gap-2">
                <button type="button" class="flex items-center gap-2 rounded-2xl border border-white/10 bg-black/30 px-4 py-2 text-xs font-medium uppercase tracking-wide text-white/70 hover:border-white/30">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h18M3 12h18M3 20h18" />
                    </svg>
                    Filter
                </button>
                <button type="button" class="flex items-center gap-2 rounded-2xl bg-indigo-500 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white shadow-lg shadow-indigo-500/30 hover:bg-indigo-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Add Task
                </button>
            </div>
        </div>
    </header>

    <div class="space-y-6">
        @foreach ($sections as $section)
            <article class="rounded-3xl border border-white/5 bg-white/5 p-6 backdrop-blur">
                <div class="flex flex-wrap items-center gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-white">{{ $section['title'] }}</h2>
                        <p class="text-xs uppercase tracking-wider text-white/50">{{ $section['tag'] }}</p>
                    </div>
                    <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-white/70">{{ count($section['tasks']) }} tasks</span>
                </div>

                <ul class="mt-6 space-y-3">
                    @foreach ($section['tasks'] as $task)
                        <li class="group flex items-center justify-between rounded-2xl border border-white/5 bg-black/30 px-4 py-3 transition hover:border-indigo-400/60 hover:bg-black/40">
                            <div class="flex items-center gap-3">
                                <span class="flex h-10 w-10 items-center justify-center rounded-2xl border border-white/10 bg-gradient-to-br from-indigo-500/20 to-purple-500/20 text-white">
                                    <svg class="h-5 w-5 text-indigo-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-white">{{ $task['title'] }}</p>
                                    <p class="text-xs text-white/50">{{ $task['subtitle'] }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-medium uppercase tracking-wide text-white/60">{{ $task['status'] }}</span>
                                @if (($task['completed'] ?? false) === true)
                                    <span class="rounded-full border border-emerald-400/40 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-300">Completed</span>
                                @else
                                    <button type="button" class="rounded-full border border-white/10 px-3 py-1 text-xs font-medium uppercase tracking-wide text-white/60 transition hover:border-indigo-400 hover:text-indigo-300">Details</button>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </article>
        @endforeach
    </div>
</section>
