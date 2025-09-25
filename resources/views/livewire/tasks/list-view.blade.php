<div class="space-y-6">
    <div class="rounded-3xl border border-white/10 bg-black/30 p-6 text-sm text-white/70">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
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
                <label class="text-xs font-semibold uppercase tracking-wide text-white/50">Status</label>
                <select wire:model="status"
                    class="mt-2 w-full rounded-2xl border border-white/10 bg-black/40 px-4 py-2 text-sm text-white focus:border-indigo-400/60 focus:outline-none focus:ring-0">
                    @foreach ($statusOptions as $option)
                        <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2 xl:col-span-2">
                <label class="text-xs font-semibold uppercase tracking-wide text-white/50">Busca</label>
                <div class="relative mt-2">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/40">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 1 0 0-15 7.5 7.5 0 0 0 0 15z" />
                        </svg>
                    </span>
                    <input type="search" placeholder="Filtrar por nome ou descrição" wire:model.debounce.400ms="search"
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

    <div class="rounded-3xl border border-white/10 bg-black/30 p-0 text-sm text-white/70">
        @if ($tasks->isEmpty())
            <div class="p-8 text-center text-white/50">
                Nenhuma tarefa corresponde aos filtros selecionados. Ajuste os filtros ou crie novas tarefas.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/10">
                    <thead class="bg-white/5 text-xs uppercase tracking-wider text-white/50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left">Tarefa</th>
                            <th scope="col" class="px-4 py-3 text-left">Lista</th>
                            <th scope="col" class="px-4 py-3 text-left">Prazo</th>
                            <th scope="col" class="px-4 py-3 text-left">Status</th>
                            <th scope="col" class="px-4 py-3 text-left">Prioridade</th>
                            <th scope="col" class="px-4 py-3 text-left">Tags</th>
                            <th scope="col" class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach ($tasks as $task)
                            <tr class="bg-black/20">
                                <td class="px-4 py-3">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-sm font-medium text-white">{{ $task->title }}</span>
                                        @if ($task->description)
                                            <span class="text-xs text-white/50">{{ \Illuminate\Support\Str::limit(strip_tags($task->description), 120) }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-white/60">
                                    {{ $task->list->name ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-white/60">
                                    @if ($task->due_at)
                                        {{ $task->due_at->format('d/m/Y') }}
                                    @else
                                        <span class="text-white/40">Sem prazo</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full border border-white/10 px-3 py-1 text-[11px] uppercase tracking-wide text-white/70">
                                        {{ match ($task->status) {
                                            'todo' => 'A fazer',
                                            'doing' => 'Em progresso',
                                            'done' => 'Concluída',
                                            'archived' => 'Arquivada',
                                            default => ucfirst($task->status),
                                        } }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full border border-white/10 px-3 py-1 text-[11px] uppercase tracking-wide text-white/70">
                                        {{ match ($task->priority) {
                                            'none' => 'Sem prioridade',
                                            'low' => 'Baixa',
                                            'med' => 'Média',
                                            'high' => 'Alta',
                                            default => ucfirst($task->priority),
                                        } }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse ($task->tags as $tag)
                                            <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-black/30 px-3 py-1 text-[11px] uppercase tracking-wide text-white/70">
                                                <span class="h-2.5 w-2.5 rounded-full" style="background-color: {{ $tag->color }}"></span>
                                                {{ $tag->name }}
                                            </span>
                                        @empty
                                            <span class="text-xs text-white/40">—</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button type="button" wire:click="toggleTaskDone({{ $task->id }})"
                                        class="inline-flex items-center gap-2 rounded-2xl border border-white/10 px-3 py-1.5 text-xs font-semibold uppercase tracking-wide text-white/70 transition hover:border-indigo-400/60 hover:text-white">
                                        @if ($task->status === 'done')
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Reabrir
                                        @else
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                            </svg>
                                            Concluir
                                        @endif
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
