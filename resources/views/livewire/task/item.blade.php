<div style="display: contents;">
    @if ($task->exists)
        <div class="space-y-3" style="margin-left: {{ max(0, $depth) * 1.5 }}rem;">
            <article
                class="rounded-2xl border border-[#212832] bg-[#151a21]/80 px-4 py-3 text-sm text-[#8b96a5] transition hover:border-[#3b82f6]/60 hover:text-[#e8eef5]">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex w-full flex-1 items-start gap-3">
                        <button type="button" wire:click="toggleCompletion"
                            class="flex h-6 w-6 items-center justify-center rounded-md border border-[#212832] bg-[#0d1116]/80 transition focus:outline-none focus:ring-2 focus:ring-[#3b82f6]/40"
                            aria-pressed="{{ $status === 'done' ? 'true' : 'false' }}">
                            <span
                                class="flex h-4 w-4 items-center justify-center rounded border-2 text-[10px] font-semibold transition"
                                @class([
                                    'border-[#3b82f6] bg-[#3b82f6] text-[#0d1116]' => $status === 'done',
                                    'border-[#212832] text-transparent hover:border-[#3b82f6]/70' => $status !== 'done',
                                ])>
                                ✓
                            </span>
                        </button>

                        <div class="w-full">
                            <input type="text" wire:model.debounce.400ms="title" wire:keydown.enter.prevent="saveTitle"
                                wire:keydown.shift.enter.prevent="quickSubtask"
                                class="w-full bg-transparent text-base text-[#e8eef5] placeholder-white/40 focus:outline-none focus:ring-0"
                                @class(['line-through text-[#6c7684]' => $status === 'done'])
                                placeholder="Nome da tarefa" />
                            @error('title')
                                <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" wire:click="toggleSubtaskForm"
                            class="flex items-center gap-1 rounded-xl border border-[#212832] px-3 py-2 text-xs font-medium text-[#8b96a5] transition hover:border-[#3b82f6] hover:text-[#e8eef5]">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Subtarefa
                        </button>
                        <button type="button" wire:click="deleteTask"
                            class="flex h-9 w-9 items-center justify-center rounded-xl border border-[#212832] text-[#6c7684] transition hover:border-rose-500/60 hover:text-rose-200"
                            title="Excluir tarefa">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                @if ($showSubtaskForm)
                    <div class="mt-3 rounded-xl border border-dashed border-[#212832] bg-[#151a21]/80 p-3">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                            <input type="text" wire:model.defer="subtaskTitle" wire:keydown.enter.prevent="createSubtask"
                                class="w-full rounded-lg border border-[#212832] bg-[#0d1116]/80 px-3 py-2 text-sm text-[#e8eef5] placeholder-white/40 focus:border-[#3b82f6]/60 focus:outline-none focus:ring-0"
                                placeholder="Digite o nome da subtarefa" />
                            <button type="button" wire:click="createSubtask"
                                class="inline-flex items-center justify-center rounded-lg bg-[#3b82f6] px-3 py-2 text-xs font-semibold uppercase tracking-wide text-[#e8eef5] transition hover:bg-[#2563eb]">
                                Adicionar
                            </button>
                        </div>
                        @error('subtaskTitle')
                            <p class="mt-2 text-xs text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>
                @endif
            </article>

            @if ($task->childrenRecursive->isNotEmpty())
                <div class="space-y-3 pl-6">
                    @foreach ($task->childrenRecursive as $child)
                        <livewire:task.item :task="$child" :depth="$depth + 1" :key="'task-item-' . $child->id" />
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>
