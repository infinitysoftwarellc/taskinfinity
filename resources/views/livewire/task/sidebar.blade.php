<aside class="space-y-8 rounded-3xl border border-white/5 bg-white/5 p-6 backdrop-blur">
    <div class="space-y-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Overview</p>
            <div class="mt-4 space-y-2">
                @foreach ($filters as $filter)
                    <button type="button"
                        class="flex w-full items-center justify-between rounded-xl bg-white/10 px-4 py-3 text-left text-sm font-medium text-gray-100 shadow-sm transition hover:bg-white/15">
                        <span class="flex items-center gap-3">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/10">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="{{ $filter['icon'] }}" />
                                </svg>
                            </span>
                            {{ $filter['label'] }}
                        </span>
                        <span class="rounded-full bg-white/10 px-2 py-0.5 text-xs font-semibold text-white/80">
                            {{ $filter['count'] }}
                        </span>
                    </button>
                @endforeach
            </div>
        </div>

        <div class="h-px w-full bg-white/10"></div>

        <div class="space-y-3">
            <div class="flex items-center justify-between text-xs font-semibold uppercase tracking-wider text-gray-400">
                <span>Folders</span>
                <button type="button" class="rounded-full bg-white/10 px-3 py-1 text-xs text-gray-200">+ New</button>
            </div>
            <ul class="space-y-2 text-sm text-gray-200">
                @foreach ($folders as $folder)
                    <li class="flex items-center justify-between rounded-xl px-3 py-2 hover:bg-white/10">
                        <span class="font-medium">{{ $folder['label'] }}</span>
                        <span class="text-xs text-gray-400">{{ $folder['items'] }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="h-px w-full bg-white/10"></div>

        <div class="space-y-3">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Labels</p>
            <ul class="space-y-2 text-sm">
                @foreach ($labels as $label)
                    <li class="flex items-center gap-3 rounded-xl px-3 py-2 hover:bg-white/10">
                        <span class="h-2 w-2 rounded-full {{ $label['color'] }}"></span>
                        <span class="text-gray-200">{{ $label['label'] }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="h-px w-full bg-white/10"></div>

        <div class="space-y-2">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">More</p>
            <div class="space-y-2 text-sm text-gray-200">
                @foreach ($filtersSecondary as $filter)
                    <button type="button"
                        class="flex w-full items-center justify-between rounded-xl px-3 py-2 hover:bg-white/10">
                        <span>{{ $filter['label'] }}</span>
                        <span class="text-xs text-gray-400">{{ $filter['count'] }}</span>
                    </button>
                @endforeach
            </div>
        </div>
    </div>
</aside>
