<aside class="flex flex-col gap-8 rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur">
    <div>
        <p class="text-xs font-semibold uppercase tracking-widest text-white/50">Views</p>
        <ul class="mt-4 space-y-2">
            @foreach ($views as $view)
                <li>
                    <button type="button"
                        class="flex w-full items-center justify-between rounded-2xl bg-white/10 px-4 py-3 text-left text-sm font-medium text-white/80 transition hover:bg-white/15">
                        <span class="flex items-center gap-3">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-black/40 text-white/70">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="{{ $view['icon'] }}" />
                                </svg>
                            </span>
                            {{ $view['label'] }}
                        </span>
                        <span class="rounded-full bg-white/10 px-2 py-0.5 text-xs font-semibold text-white/70">
                            {{ $view['count'] }}
                        </span>
                    </button>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="h-px bg-white/10"></div>

    <div>
        <div class="mb-3 flex items-center justify-between text-xs font-semibold uppercase tracking-widest text-white/50">
            <span>Lists</span>
            <button type="button" class="rounded-full bg-white/10 px-3 py-1 text-[10px] font-semibold text-white/70 transition hover:bg-white/20">+ New</button>
        </div>
        <ul class="space-y-2 text-sm">
            @foreach ($lists as $list)
                <li class="flex items-center justify-between rounded-2xl px-3 py-2 text-white/80 transition hover:bg-white/10">
                    <span class="font-medium uppercase tracking-wide">{{ $list['label'] }}</span>
                    <span class="text-xs text-white/50">{{ $list['items'] }}</span>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="h-px bg-white/10"></div>

    <div>
        <p class="text-xs font-semibold uppercase tracking-widest text-white/50">Filters</p>
        <p class="mt-3 rounded-2xl bg-black/30 p-4 text-xs text-white/60">
            {{ $filtersDescription }}
        </p>
    </div>

    <div class="h-px bg-white/10"></div>

    <div>
        <p class="text-xs font-semibold uppercase tracking-widest text-white/50">Tags</p>
        <ul class="mt-3 space-y-2 text-sm text-white/80">
            @foreach ($tags as $tag)
                <li class="flex items-center gap-3 rounded-2xl px-3 py-2 transition hover:bg-white/10">
                    <span class="h-2.5 w-2.5 rounded-full {{ $tag['color'] }}"></span>
                    <span class="uppercase tracking-wide">{{ $tag['label'] }}</span>
                </li>
            @endforeach
        </ul>
    </div>
</aside>
