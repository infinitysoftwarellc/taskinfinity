<div class="min-h-screen bg-gray-950 py-12 text-white">
    <div class="mx-auto flex max-w-6xl flex-col gap-10 px-4 sm:px-6 lg:px-8">
        <header class="max-w-3xl">
            <p class="text-xs uppercase tracking-[0.4em] text-white/50">Rotina</p>
            <h1 class="mt-3 text-4xl font-semibold tracking-tight">Construa hábitos consistentes</h1>
            <p class="mt-3 text-sm text-white/60">
                Planeje seus hábitos, acompanhe streaks e visualize o progresso em um calendário bonito. Faça check-in todos os
                dias para manter o momentum vivo.
            </p>
        </header>

        <div class="grid gap-8 lg:grid-cols-[360px,1fr]">
            <div class="space-y-6">
                <div class="rounded-3xl border border-white/5 bg-white/5/80 p-6 shadow-2xl shadow-black/20 backdrop-blur">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-xl font-semibold">Meus hábitos</h2>
                            <p class="mt-1 text-sm text-white/60">Adicione, organize e celebre pequenas vitórias diárias.</p>
                        </div>

                        <button
                            type="button"
                            wire:click="toggleCreateForm"
                            class="flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-500/90 text-white transition hover:bg-indigo-400"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" />
                            </svg>
                        </button>
                    </div>

                    @if ($showCreateForm)
                        <form wire:submit="createHabit" class="mt-6 space-y-5 rounded-2xl border border-white/10 bg-black/30 p-5">
                            <div class="space-y-2">
                                <label class="text-xs font-semibold uppercase tracking-[0.2em] text-white/50">Nome</label>
                                <input
                                    type="text"
                                    wire:model.defer="form.name"
                                    class="w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-sm text-white placeholder:text-white/40 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/60"
                                    placeholder="Ex.: Ler 10 páginas"
                                />
                                @error('form.name')
                                    <p class="text-xs text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-semibold uppercase tracking-[0.2em] text-white/50">Recorrência</label>
                                <select
                                    wire:model="form.schedule"
                                    class="w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-sm text-white focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/60"
                                >
                                    <option value="daily">Diário</option>
                                    <option value="weekly">Semanal</option>
                                    <option value="custom">Personalizado</option>
                                </select>
                                @error('form.schedule')
                                    <p class="text-xs text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-semibold uppercase tracking-[0.2em] text-white/50">Frequência</label>
                                <select
                                    wire:model="form.frequency"
                                    class="w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-sm text-white focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/60"
                                >
                                    @foreach ($frequencyOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('form.frequency')
                                    <p class="text-xs text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-semibold uppercase tracking-[0.2em] text-white/50">Objetivo</label>
                                <select
                                    wire:model="form.goal"
                                    class="w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-sm text-white focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/60"
                                >
                                    @foreach ($goalOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('form.goal')
                                    <p class="text-xs text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-semibold uppercase tracking-[0.2em] text-white/50">Data de início</label>
                                <input
                                    type="date"
                                    wire:model.defer="form.start_date"
                                    class="w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-sm text-white placeholder:text-white/40 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/60"
                                />
                                @error('form.start_date')
                                    <p class="text-xs text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-semibold uppercase tracking-[0.2em] text-white/50">Período alvo</label>
                                <select
                                    wire:model="form.goal_days"
                                    class="w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-sm text-white focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/60"
                                >
                                    @foreach ($goalDaysOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('form.goal_days')
                                    <p class="text-xs text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            @if ($form['schedule'] === 'weekly')
                                <div class="space-y-2">
                                    <label class="text-xs font-semibold uppercase tracking-[0.2em] text-white/50">Meta semanal</label>
                                    <input
                                        type="number"
                                        min="1"
                                        max="31"
                                        wire:model.defer="form.goal_per_period"
                                        class="w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-sm text-white placeholder:text-white/40 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/60"
                                        placeholder="Ex.: 5 check-ins"
                                    />
                                    @error('form.goal_per_period')
                                        <p class="text-xs text-rose-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif

                            @if ($form['schedule'] === 'custom')
                                <div class="space-y-3">
                                    <span class="text-xs font-semibold uppercase tracking-[0.2em] text-white/50">Dias da semana</span>
                                    <div class="grid grid-cols-4 gap-2 text-sm">
                                        @foreach ($weekdays as $index => $label)
                                            <label class="flex items-center gap-2 rounded-2xl border border-white/10 bg-white/10 px-3 py-2 text-white/70 transition hover:text-white">
                                                <input
                                                    type="checkbox"
                                                    wire:model="form.custom_days"
                                                    value="{{ $index }}"
                                                    class="h-4 w-4 rounded border-white/20 bg-black/40 text-indigo-500 focus:ring-indigo-400"
                                                />
                                                <span>{{ $label }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('form.custom_days')
                                        <p class="text-xs text-rose-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif

                            <div class="space-y-2">
                                <label class="text-xs font-semibold uppercase tracking-[0.2em] text-white/50">Lembrete</label>
                                <select
                                    wire:model="form.reminder"
                                    class="w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-sm text-white focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/60"
                                >
                                    @foreach ($reminderOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('form.reminder')
                                    <p class="text-xs text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-semibold uppercase tracking-[0.2em] text-white/50">Cor</label>
                                <div class="flex items-center gap-3">
                                    <input
                                        type="color"
                                        wire:model="form.color"
                                        class="h-12 w-16 cursor-pointer rounded-2xl border border-white/10 bg-white/10"
                                    />
                                    <span class="text-xs text-white/50">Use cores para identificar seus hábitos rapidamente.</span>
                                </div>
                                @error('form.color')
                                    <p class="text-xs text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <label class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white/70 transition hover:text-white">
                                <input
                                    type="checkbox"
                                    wire:model="form.auto_popup"
                                    class="h-4 w-4 rounded border-white/20 bg-black/40 text-indigo-500 focus:ring-indigo-400"
                                />
                                <span class="text-xs uppercase tracking-[0.2em]">Abrir automaticamente o registro do hábito</span>
                            </label>

                            <div class="flex items-center justify-end gap-3">
                                <button
                                    type="button"
                                    wire:click="toggleCreateForm"
                                    class="rounded-2xl border border-white/20 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white/70 transition hover:text-white"
                                >
                                    Cancelar
                                </button>
                                <button
                                    type="submit"
                                    class="rounded-2xl bg-indigo-500 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white transition hover:bg-indigo-400"
                                >
                                    Criar hábito
                                </button>
                            </div>
                        </form>
                    @endif

                    <div class="mt-6 space-y-4">
                        @forelse ($habits as $habit)
                            @php
                                $isActive = $activeHabit && $activeHabit->id === $habit->id;
                                $recentEntries = $recentEntriesByHabit[$habit->id] ?? collect();
                                $summary = $summaries[$habit->id] ?? ['current_streak' => 0, 'monthly_completed' => 0];
                            @endphp
                            <div
                                wire:key="habit-card-{{ $habit->id }}"
                                class="group rounded-3xl border border-white/10 bg-black/40 p-5 transition hover:border-indigo-400/60 {{ $isActive ? 'border-indigo-400/80 bg-indigo-500/10' : '' }}"
                            >
                                <div class="flex items-start justify-between gap-3">
                                    <button
                                        type="button"
                                        wire:click="selectHabit({{ $habit->id }})"
                                        class="flex flex-1 items-center gap-3 text-left"
                                    >
                                        <span
                                            class="mt-1 h-3 w-3 rounded-full"
                                            style="background: {{ $habit->color ?? '#22c55e' }}"
                                        ></span>
                                        <div class="flex-1">
                                            <h3 class="text-base font-semibold">{{ $habit->name }}</h3>
                                            <p class="text-xs uppercase tracking-[0.3em] text-white/40">{{ ucfirst($habit->schedule) }}</p>
                                        </div>
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="deleteHabit({{ $habit->id }})"
                                        class="rounded-2xl border border-white/10 p-2 text-white/40 transition hover:border-rose-400/60 hover:text-rose-300"
                                        title="Remover hábito"
                                    >
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 5.25H19.5m0 0V9.5m0-4.25-5.25 5.25M4.5 19.5l3.95-11.41a1 1 0 0 1 .95-.68h5.2" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="mt-4 flex items-center justify-between text-xs text-white/60">
                                    <span>Streak atual: <strong class="text-white">{{ $summary['current_streak'] }}d</strong></span>
                                    <span>Este mês: <strong class="text-white">{{ $summary['monthly_completed'] }}</strong></span>
                                </div>

                                <div class="mt-4 flex items-center gap-2">
                                    @foreach ($recentDays as $day)
                                        @php
                                            $entry = $recentEntries[$day->toDateString()] ?? null;
                                            $isCompleted = $entry?->completed ?? false;
                                            $isToday = $day->isSameDay($today);
                                            $disabled = $day->greaterThan($today) || ! $habit->isDueOn($day);
                                        @endphp
                                        <button
                                            type="button"
                                            wire:click="toggleEntry({{ $habit->id }}, '{{ $day->toDateString() }}')"
                                            @disabled($disabled)
                                            class="flex h-10 w-10 items-center justify-center rounded-2xl border border-white/10 text-sm transition {{ $isCompleted ? 'bg-emerald-500/80 text-white border-emerald-400/60' : 'bg-white/5 text-white/60 hover:text-white' }} {{ $disabled ? 'opacity-40 cursor-not-allowed' : '' }} {{ $isToday ? 'ring-2 ring-indigo-400/60' : '' }}"
                                        >
                                            <span class="text-[10px] uppercase tracking-[0.2em]">{{ $day->isoFormat('dd') }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="rounded-3xl border border-dashed border-white/20 bg-black/30 p-6 text-center text-sm text-white/60">
                                Nenhum hábito ainda. Clique em “+” para criar o primeiro.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div>
                @if ($activeHabit)
                    <div class="space-y-6">
                        <div class="rounded-3xl border border-white/10 bg-black/40 p-6">
                            <div class="flex items-center gap-4">
                                <div
                                    class="flex h-16 w-16 items-center justify-center rounded-3xl border border-white/10 text-white"
                                    style="background: {{ ($activeHabit->color ?? '#22c55e') }}1A"
                                >
                                    <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12A9 9 0 1 1 3 12a9 9 0 0 1 18 0Z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs uppercase tracking-[0.4em] text-white/50">Hábito selecionado</p>
                                    <h2 class="mt-2 text-2xl font-semibold">{{ $activeHabit->name }}</h2>
                                    <p class="mt-1 text-sm text-white/60">{{ __('Acompanhe cada check-in para manter a consistência.') }}</p>
                                </div>
                            </div>

                            @if ($stats)
                                <dl class="mt-6 grid gap-4 sm:grid-cols-2">
                                    <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                                        <dt class="text-xs uppercase tracking-[0.3em] text-white/50">Check-ins no mês</dt>
                                        <dd class="mt-2 text-2xl font-semibold">{{ $stats['monthly_completed'] }}</dd>
                                    </div>
                                    <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                                        <dt class="text-xs uppercase tracking-[0.3em] text-white/50">Taxa mensal</dt>
                                        <dd class="mt-2 text-2xl font-semibold">{{ $stats['monthly_rate'] ? $stats['monthly_rate'] . '%' : '—' }}</dd>
                                    </div>
                                    <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                                        <dt class="text-xs uppercase tracking-[0.3em] text-white/50">Streak atual</dt>
                                        <dd class="mt-2 text-2xl font-semibold">{{ $stats['current_streak'] }} dias</dd>
                                    </div>
                                    <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                                        <dt class="text-xs uppercase tracking-[0.3em] text-white/50">Maior streak</dt>
                                        <dd class="mt-2 text-2xl font-semibold">{{ $stats['longest_streak'] }} dias</dd>
                                    </div>
                                </dl>
                                <p class="mt-4 text-xs text-white/50">Total histórico: {{ $stats['total_completed'] }} check-ins completos.</p>
                            @endif
                        </div>

                        <div class="rounded-3xl border border-white/10 bg-black/40 p-6">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.3em] text-white/40">Calendário</p>
                                    <h3 class="text-xl font-semibold">{{ $monthLabel }}</h3>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button
                                        type="button"
                                        wire:click="goToPreviousMonth"
                                        class="flex h-10 w-10 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-white/70 transition hover:text-white"
                                    >
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 19.5-7.5-7.5 7.5-7.5" />
                                        </svg>
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="goToCurrentMonth"
                                        class="rounded-2xl border border-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white/70 transition hover:text-white"
                                    >
                                        Hoje
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="goToNextMonth"
                                        class="flex h-10 w-10 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-white/70 transition hover:text-white"
                                    >
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="mt-6 grid gap-3">
                                <div class="grid grid-cols-7 gap-2 text-center text-xs uppercase tracking-[0.3em] text-white/40">
                                    @foreach ($weekdays as $weekday)
                                        <span>{{ $weekday }}</span>
                                    @endforeach
                                </div>

                                <div class="grid grid-cols-7 gap-2 text-sm">
                                    @foreach ($calendarRows as $row)
                                        @foreach ($row as $day)
                                            @php
                                                $entry = $calendarEntries[$day->toDateString()] ?? null;
                                                $isCompleted = $entry?->completed ?? false;
                                                $isCurrentMonth = $day->isSameMonth($activeDate);
                                                $isToday = $day->isSameDay($today);
                                                $isDue = $activeHabit->isDueOn($day);
                                                $disabled = $day->greaterThan($today) || ! $isDue;
                                            @endphp
                                            <button
                                                type="button"
                                                wire:click="toggleEntry({{ $activeHabit->id }}, '{{ $day->toDateString() }}')"
                                                @disabled($disabled)
                                                class="flex h-14 flex-col items-center justify-center rounded-2xl border text-xs transition {{ $isCompleted ? 'border-emerald-400/60 bg-emerald-500/80 text-white' : 'border-white/10 bg-white/5 text-white/60 hover:text-white' }} {{ $disabled ? 'cursor-not-allowed opacity-40' : '' }} {{ $isToday ? 'ring-2 ring-indigo-400/60' : '' }} {{ $isCurrentMonth ? '' : 'opacity-40' }}"
                                            >
                                                <span class="text-base font-semibold">{{ $day->day }}</span>
                                                <span class="text-[10px] uppercase tracking-[0.2em]">{{ $day->isoFormat('dd') }}</span>
                                            </button>
                                        @endforeach
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="rounded-3xl border border-dashed border-white/20 bg-black/30 p-10 text-center text-sm text-white/60">
                        Cadastre um hábito para começar a acompanhar sua rotina diária.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
