<div class="space-y-6">
    <div class="rounded-3xl border border-white/10 bg-black/30 p-6 text-sm text-white/70">
        <div class="grid gap-4 md:grid-cols-[minmax(0,1fr)_minmax(0,2fr)] xl:grid-cols-[280px_minmax(0,2fr)]">
            <div>
                <label class="text-xs font-semibold uppercase tracking-wide text-white/50">Lista</label>
                <select wire:model="listId"
                    class="mt-2 w-full rounded-2xl border border-white/10 bg-black/40 px-4 py-2 text-sm text-white focus:border-indigo-400/60 focus:outline-none focus:ring-0">
                    <option value="">Todas as listas</option>
                    @foreach ($lists as $list)
                        <option value="{{ $list->id }}">{{ $list->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold uppercase tracking-wide text-white/50">Busca</label>
                <div class="relative mt-2">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/40">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 1 0 0-15 7.5 7.5 0 0 0 0 15z" />
                        </svg>
                    </span>
                    <input type="search" placeholder="Filtrar tarefas pelo título ou descrição" wire:model.debounce.400ms="search"
                        class="w-full rounded-2xl border border-white/10 bg-black/40 py-2 pl-9 pr-4 text-sm text-white placeholder-white/40 focus:border-indigo-400/60 focus:outline-none focus:ring-0" />
                </div>
            </div>
        </div>
        <div class="mt-4 flex justify-end">
            <button type="button" wire:click="clearFilters"
                class="inline-flex items-center gap-2 rounded-2xl border border-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white/70 transition hover:border-white/30 hover:text-white">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
                Limpar filtros
            </button>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach ($columns as $column)
            <section class="flex h-full flex-col gap-4 rounded-3xl border border-white/10 bg-black/30 p-5 text-sm text-white/70">
                <header class="space-y-1">
                    <div class="flex items-center justify-between gap-2">
                        <h2 class="text-lg font-semibold text-white">{{ $column['label'] }}</h2>
                        <span class="text-xs uppercase tracking-wide text-white/40">{{ $column['tasks']->count() }}</span>
                    </div>
                    <p class="text-xs text-white/50">{{ $column['description'] }}</p>
                </header>

                <div class="flex-1 space-y-3 overflow-y-auto pr-1">
                    @forelse ($column['tasks'] as $task)
                        <article class="space-y-3 rounded-2xl border border-white/10 bg-black/40 p-4 text-sm text-white/70 shadow-inner shadow-black/40">
                            <div class="flex items-start justify-between gap-3">
                                <div class="space-y-1">
                                    <h3 class="text-base font-semibold text-white">{{ $task->title }}</h3>
                                    @if ($task->list && (! $listId || $task->list_id !== (int) $listId))
                                        <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-black/30 px-3 py-1 text-[11px] uppercase tracking-wide text-white/60">
                                            {{ $task->list->name }}
                                        </span>
                                    @endif
                                </div>
                                <button type="button" wire:click="toggleTaskDone({{ $task->id }})"
                                    class="rounded-full border border-white/10 bg-black/20 p-2 text-white/60 transition hover:border-indigo-400/60 hover:text-white"
                                    title="Alternar conclusão">
                                    @if ($task->status === 'done')
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    @else
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                        </svg>
                                    @endif
                                </button>
                            </div>

                            @if ($task->description)
                                <p class="text-xs text-white/50">{{ \Illuminate\Support\Str::limit(strip_tags($task->description), 160) }}</p>
                            @endif

                            <div class="flex flex-wrap items-center gap-3 text-xs text-white/50">
                                <div class="flex items-center gap-2">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h7.5M8.25 12h7.5m-4.5 5.25H12M3.75 5.25A2.25 2.25 0 0 1 6 3h12a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 18 21H6a2.25 2.25 0 0 1-2.25-2.25z" />
                                    </svg>
                                    <span>
                                        {{ match ($task->priority) {
                                            'none' => 'Sem prioridade',
                                            'low' => 'Baixa prioridade',
                                            'med' => 'Prioridade média',
                                            'high' => 'Alta prioridade',
                                            default => ucfirst($task->priority),
                                        } }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m3.75 0a8.25 8.25 0 1 1-16.5 0 8.25 8.25 0 0 1 16.5 0Z" />
                                    </svg>
                                    <span>
                                        @if ($task->due_at)
                                            {{ $task->due_at->format('d/m/Y') }}
                                        @else
                                            Sem prazo
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                @foreach ($task->tags as $tag)
                                    <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-black/30 px-3 py-1 text-[11px] uppercase tracking-wide text-white/70">
                                        <span class="h-2.5 w-2.5 rounded-full" style="background-color: {{ $tag->color }}"></span>
                                        {{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>

                            <div>
                                <label class="text-[11px] font-semibold uppercase tracking-wide text-white/40">Mover para</label>
                                <select wire:change="moveTask({{ $task->id }}, $event.target.value)"
                                    class="mt-1 w-full rounded-2xl border border-white/10 bg-black/40 px-3 py-2 text-xs text-white focus:border-indigo-400/60 focus:outline-none focus:ring-0">
                                    @foreach ($columns as $option)
                                        <option value="{{ $option['status'] }}" @selected($option['status'] === $task->status)>
                                            {{ $option['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-2xl border border-dashed border-white/10 bg-black/20 p-6 text-center text-xs text-white/50">
                            Nenhuma tarefa neste estágio ainda.
                        </div>
                    @endforelse
                </div>
            </section>
        @endforeach
    </div>
</div>
