<div class="space-y-6">
    <div class="rounded-3xl border border-white/10 bg-black/30 p-6 text-sm text-white/70">
        <div class="grid gap-4 md:grid-cols-3">
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
                <label class="text-xs font-semibold uppercase tracking-wide text-white/50">Janela</label>
                <select wire:model="timeframe"
                    class="mt-2 w-full rounded-2xl border border-white/10 bg-black/40 px-4 py-2 text-sm text-white focus:border-indigo-400/60 focus:outline-none focus:ring-0">
                    @foreach ($timeframes as $option)
                        <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
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
                    <input type="search" placeholder="Filtrar pelo título ou descrição" wire:model.debounce.400ms="search"
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

    <div class="space-y-6">
        @forelse ($groups as $group)
            <section class="relative flex gap-6 rounded-3xl border border-white/10 bg-black/30 p-6 text-sm text-white/70">
                <div class="flex w-36 shrink-0 flex-col items-start gap-2">
                    <span class="rounded-full bg-indigo-500/20 px-3 py-1 text-[11px] font-semibold uppercase tracking-wide text-indigo-200">
                        {{ $group['tasks']->count() }} tarefas
                    </span>
                    <h2 class="text-lg font-semibold text-white">{{ $group['label'] }}</h2>
                </div>
                <div class="relative flex-1 space-y-4">
                    <span class="absolute left-2 top-1 bottom-1 w-px bg-gradient-to-b from-white/40 to-white/10"></span>
                    @foreach ($group['tasks'] as $task)
                        <article class="relative ml-4 rounded-2xl border border-white/10 bg-black/40 p-4 text-sm text-white/70 shadow-inner shadow-black/40">
                            <span class="absolute -left-4 top-5 h-3 w-3 rounded-full border-4 border-black/40 bg-indigo-400"></span>
                            <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                <div class="space-y-2">
                                    <div class="flex items-center gap-3">
                                        <button type="button" wire:click="toggleTaskDone({{ $task->id }})"
                                            class="flex h-8 w-8 items-center justify-center rounded-full border border-white/10 text-white/60 transition hover:border-indigo-400/60 hover:text-white"
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
                                        <div>
                                            <h3 class="text-base font-semibold text-white">{{ $task->title }}</h3>
                                            @if ($task->list && (! $listId || $task->list_id !== (int) $listId))
                                                <p class="text-xs text-white/50">{{ $task->list->name }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    @if ($task->description)
                                        <p class="text-xs text-white/50">{{ \Illuminate\Support\Str::limit(strip_tags($task->description), 160) }}</p>
                                    @endif
                                    <div class="flex flex-wrap items-center gap-3 text-xs text-white/50">
                                        <span class="inline-flex items-center gap-2">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h7.5M8.25 12h7.5m-4.5 5.25H12M3.75 5.25A2.25 2.25 0 0 1 6 3h12a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 18 21H6a2.25 2.25 0 0 1-2.25-2.25z" />
                                            </svg>
                                            {{ match ($task->priority) {
                                                'none' => 'Sem prioridade',
                                                'low' => 'Baixa prioridade',
                                                'med' => 'Prioridade média',
                                                'high' => 'Alta prioridade',
                                                default => ucfirst($task->priority),
                                            } }}
                                        </span>
                                        <span class="inline-flex items-center gap-2">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l2.5 2.5m5-2.5a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z" />
                                            </svg>
                                            @if ($task->due_at)
                                                {{ $task->due_at->format('d/m/Y H:i') }}
                                            @else
                                                Sem prazo
                                            @endif
                                        </span>
                                    </div>
                                    @if ($task->tags->isNotEmpty())
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($task->tags as $tag)
                                                <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-black/30 px-3 py-1 text-[11px] uppercase tracking-wide text-white/70">
                                                    <span class="h-2.5 w-2.5 rounded-full" style="background-color: {{ $tag->color }}"></span>
                                                    {{ $tag->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div class="flex shrink-0 flex-col items-end gap-2 text-xs text-white/50">
                                    <span class="rounded-full border border-white/10 px-3 py-1 uppercase tracking-wide">
                                        {{ match ($task->status) {
                                            'todo' => 'A fazer',
                                            'doing' => 'Em progresso',
                                            'done' => 'Concluída',
                                            'archived' => 'Arquivada',
                                            default => ucfirst($task->status),
                                        } }}
                                    </span>
                                    @if ($task->estimate_pomodoros)
                                        <span>Estimativa: {{ $task->estimate_pomodoros }} pomodoros</span>
                                    @endif
                                    @if ($task->pomodoros_done)
                                        <span>Concluídos: {{ $task->pomodoros_done }}</span>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @empty
            <div class="rounded-3xl border border-dashed border-white/10 bg-black/30 p-10 text-center text-white/50">
                Nenhuma tarefa encontrada para os filtros selecionados.
            </div>
        @endforelse
    </div>
</div>
