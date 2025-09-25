<div class="min-h-screen bg-[#06070f] py-12 text-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-10">
        @php
            $activeRecentEntries = $activeHabit ? ($recentEntriesByHabit[$activeHabit->id] ?? collect()) : collect();
        @endphp

        <div class="grid gap-10 lg:grid-cols-[minmax(0,1fr),360px]">
            <div class="space-y-8">
                <div
                    class="rounded-3xl border border-white/5 bg-gradient-to-br from-[#151a33]/90 via-[#0e111f]/95 to-[#070812] p-8 shadow-[0_40px_80px_-24px_rgba(0,0,0,0.7)]"
                >
                    <div class="flex flex-wrap items-start justify-between gap-6">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.6em] text-white/40">Habit</p>
                            <h1 class="mt-4 text-3xl font-semibold tracking-tight text-white/95 sm:text-4xl">
                                Construa hábitos consistentes
                            </h1>
                            <p class="mt-3 max-w-xl text-sm text-white/60">
                                Complete seus check-ins diários, mantenha o streak vivo e visualize cada vitória com clareza.
                            </p>
                        </div>

                        <div class="flex items-center gap-3">
                            <button
                                type="button"
                                wire:click="toggleCreateForm"
                                class="flex h-11 w-11 items-center justify-center rounded-full bg-indigo-500 text-white transition hover:bg-indigo-400"
                                title="Novo hábito"
                            >
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" />
                                </svg>
                            </button>
                            <button
                                type="button"
                                class="flex h-11 w-11 items-center justify-center rounded-full border border-white/10 bg-white/5 text-white/60 transition hover:text-white"
                                title="Opções"
                            >
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Zm0 6a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Zm0 6a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    @if ($showCreateForm)
                        <div class="mt-8 rounded-3xl border border-indigo-500/40 bg-black/40 p-6 backdrop-blur">
                            <form wire:submit="createHabit" class="grid gap-5 md:grid-cols-2">
                                <div class="space-y-2 md:col-span-2">
                                    <label class="text-[10px] font-semibold uppercase tracking-[0.3em] text-white/40">Nome do hábito</label>
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
                                    <label class="text-[10px] font-semibold uppercase tracking-[0.3em] text-white/40">Recorrência</label>
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
                                    <label class="text-[10px] font-semibold uppercase tracking-[0.3em] text-white/40">Frequência</label>
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
                                    <label class="text-[10px] font-semibold uppercase tracking-[0.3em] text-white/40">Objetivo</label>
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
                                    <label class="text-[10px] font-semibold uppercase tracking-[0.3em] text-white/40">Data de início</label>
                                    <input
                                        type="date"
                                        wire:model.defer="form.start_date"
                                        class="w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-sm text-white focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/60"
                                    />
                                    @error('form.start_date')
                                        <p class="text-xs text-rose-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="text-[10px] font-semibold uppercase tracking-[0.3em] text-white/40">Período alvo</label>
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
                                        <label class="text-[10px] font-semibold uppercase tracking-[0.3em] text-white/40">Meta semanal</label>
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
                                    <div class="space-y-3 md:col-span-2">
                                        <span class="text-[10px] font-semibold uppercase tracking-[0.3em] text-white/40">Dias da semana</span>
                                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                                            @foreach ($weekdays as $index => $label)
                                                <label class="flex items-center gap-2 rounded-2xl border border-white/10 bg-white/10 px-3 py-2 text-sm text-white/70 transition hover:text-white">
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
                                    <label class="text-[10px] font-semibold uppercase tracking-[0.3em] text-white/40">Lembrete</label>
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
                                    <label class="text-[10px] font-semibold uppercase tracking-[0.3em] text-white/40">Cor</label>
                                    <div class="flex items-center gap-3">
                                        <input
                                            type="color"
                                            wire:model="form.color"
                                            class="h-12 w-16 cursor-pointer rounded-2xl border border-white/10 bg-white/10"
                                        />
                                        <span class="text-xs text-white/50">Escolha uma cor para identificar o hábito.</span>
                                    </div>
                                    @error('form.color')
                                        <p class="text-xs text-rose-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <label class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-xs uppercase tracking-[0.3em] text-white/60 transition hover:text-white md:col-span-2">
                                    <input
                                        type="checkbox"
                                        wire:model="form.auto_popup"
                                        class="h-4 w-4 rounded border-white/20 bg-black/40 text-indigo-500 focus:ring-indigo-400"
                                    />
                                    <span>Abrir automaticamente o registro do hábito</span>
                                </label>

                                <div class="flex items-center justify-end gap-3 md:col-span-2">
                                    <button
                                        type="button"
                                        wire:click="toggleCreateForm"
                                        class="rounded-2xl border border-white/20 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.3em] text-white/70 transition hover:text-white"
                                    >
                                        Cancelar
                                    </button>
                                    <button
                                        type="submit"
                                        class="rounded-2xl bg-indigo-500 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.3em] text-white transition hover:bg-indigo-400"
                                    >
                                        Criar hábito
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif

                    @if ($activeHabit)
                        <div class="mt-10 grid grid-cols-2 gap-3 sm:grid-cols-4">
                            @foreach ($recentDays as $day)
                                @php
                                    $entry = $activeRecentEntries[$day->toDateString()] ?? null;
                                    $isCompleted = $entry?->completed ?? false;
                                    $isDue = $activeHabit->isDueOn($day);
                                    $isToday = $day->isSameDay($today);
                                    $disabled = $day->greaterThan($today) || ! $isDue;
                                @endphp
                                <button
                                    type="button"
                                    wire:click="toggleEntry({{ $activeHabit->id }}, '{{ $day->toDateString() }}')"
                                    @disabled($disabled)
                                    class="group relative flex flex-col items-center justify-between rounded-2xl border border-white/10 bg-white/[0.04] px-3 py-4 text-center text-xs transition hover:border-indigo-400/60 hover:text-white {{ $disabled ? 'cursor-not-allowed opacity-30 hover:border-white/10' : '' }}"
                                >
                                    <span class="text-[10px] uppercase tracking-[0.4em] text-white/50">{{ $day->isoFormat('dd') }}</span>
                                    <span class="mt-4 text-sm font-medium text-white/80">{{ $day->day }}</span>
                                    <span
                                        class="mt-4 flex h-9 w-9 items-center justify-center rounded-full border text-white transition {{ $isCompleted ? 'border-blue-400/80 bg-blue-500 text-white shadow-lg shadow-blue-500/30' : 'border-white/10 bg-white/5 text-white/40 group-hover:text-white' }} {{ $isToday ? 'ring-2 ring-blue-400/60' : '' }}"
                                    >
                                        @if ($isCompleted)
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75" />
                                            </svg>
                                        @else
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5" />
                                            </svg>
                                        @endif
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="rounded-3xl border border-white/10 bg-[#0a0d18]/90 p-7 shadow-[0_24px_60px_-20px_rgba(0,0,0,0.6)]">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs uppercase tracking-[0.4em] text-white/40">Calendário mensal</p>
                            <h3 class="mt-2 text-2xl font-semibold text-white/90">{{ $monthLabel }}</h3>
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
                                class="rounded-2xl border border-white/10 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.3em] text-white/70 transition hover:text-white"
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

                    <div class="mt-8 space-y-4">
                        <div class="grid grid-cols-7 gap-3 text-center text-[10px] uppercase tracking-[0.4em] text-white/40">
                            @foreach ($weekdays as $weekday)
                                <span>{{ $weekday }}</span>
                            @endforeach
                        </div>

                        <div class="grid grid-cols-7 gap-3">
                            @foreach ($calendarRows as $row)
                                @foreach ($row as $day)
                                    @php
                                        $entry = $calendarEntries[$day->toDateString()] ?? null;
                                        $isCompleted = $entry?->completed ?? false;
                                        $isCurrentMonth = $day->isSameMonth($activeDate);
                                        $isToday = $day->isSameDay($today);
                                        $isDue = $activeHabit?->isDueOn($day) ?? false;
                                        $disabled = $day->greaterThan($today) || ! $isDue;
                                    @endphp
                                    <button
                                        type="button"
                                        @if ($activeHabit)
                                            wire:click="toggleEntry({{ $activeHabit->id }}, '{{ $day->toDateString() }}')"
                                        @endif
                                        @disabled($disabled || ! $activeHabit)
                                        class="flex h-16 flex-col items-center justify-center rounded-2xl border text-xs transition {{ $isCompleted ? 'border-blue-400/70 bg-blue-500 text-white shadow-lg shadow-blue-500/30' : 'border-white/10 bg-white/5 text-white/50 hover:border-indigo-400/60 hover:text-white' }} {{ $disabled || ! $activeHabit ? 'cursor-not-allowed opacity-30 hover:border-white/10 hover:text-white/50' : '' }} {{ $isToday ? 'ring-2 ring-blue-400/70' : '' }} {{ $isCurrentMonth ? '' : 'opacity-40' }}"
                                    >
                                        <span class="text-lg font-semibold">{{ $day->day }}</span>
                                        <span class="text-[10px] uppercase tracking-[0.3em]">{{ $day->isoFormat('dd') }}</span>
                                    </button>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    @forelse ($habits as $habit)
                        @php
                            $isActive = $activeHabit && $activeHabit->id === $habit->id;
                            $recentEntries = $recentEntriesByHabit[$habit->id] ?? collect();
                            $summary = $summaries[$habit->id] ?? ['current_streak' => 0, 'monthly_completed' => 0];
                        @endphp
                        <div
                            wire:key="habit-card-{{ $habit->id }}"
                            class="group overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-[#101426]/90 via-[#0b0f1c]/95 to-[#06070f] p-6 transition hover:border-blue-400/60 {{ $isActive ? 'border-blue-400/70 shadow-[0_24px_60px_-20px_rgba(37,99,235,0.45)]' : 'shadow-[0_18px_40px_-20px_rgba(0,0,0,0.6)]' }}"
                        >
                            <div class="flex items-start justify-between gap-4">
                                <button
                                    type="button"
                                    wire:click="selectHabit({{ $habit->id }})"
                                    class="flex flex-1 items-center gap-4 text-left"
                                >
                                    <span
                                        class="flex h-12 w-12 items-center justify-center rounded-2xl border border-white/10 text-white"
                                        style="background: {{ ($habit->color ?? '#2563eb') }}1a"
                                    >
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12A9 9 0 1 1 3 12a9 9 0 0 1 18 0Z" />
                                        </svg>
                                    </span>
                                    <div class="flex-1">
                                        <p class="text-[11px] uppercase tracking-[0.4em] text-white/40">{{ ucfirst($habit->schedule) }}</p>
                                        <h3 class="mt-2 text-lg font-semibold text-white/90">{{ $habit->name }}</h3>
                                        <div class="mt-3 flex flex-wrap items-center gap-3 text-[11px] uppercase tracking-[0.3em] text-white/40">
                                            <span>Streak {{ $summary['current_streak'] }}d</span>
                                            <span class="hidden h-1 w-1 rounded-full bg-white/30 sm:block"></span>
                                            <span>{{ __('Mês: :count check-ins', ['count' => $summary['monthly_completed']]) }}</span>
                                        </div>
                                    </div>
                                </button>
                                <button
                                    type="button"
                                    wire:click="deleteHabit({{ $habit->id }})"
                                    onclick="confirm('Tem certeza que deseja excluir este hábito?') || event.stopImmediatePropagation()"
                                    class="rounded-2xl border border-white/10 p-2 text-white/40 transition hover:border-rose-400/60 hover:text-rose-300"
                                    title="Remover hábito"
                                >
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 5.25H19.5m0 0V9.5m0-4.25-5.25 5.25M4.5 19.5l3.95-11.41a1 1 0 0 1 .95-.68h5.2" />
                                    </svg>
                                </button>
                            </div>

                            <div class="mt-6 grid grid-cols-7 gap-2">
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
                                        class="flex h-11 flex-col items-center justify-center rounded-2xl border text-[10px] uppercase tracking-[0.3em] transition {{ $isCompleted ? 'border-blue-400/70 bg-blue-500 text-white shadow-lg shadow-blue-500/30' : 'border-white/10 bg-white/5 text-white/50 hover:border-indigo-400/60 hover:text-white' }} {{ $disabled ? 'cursor-not-allowed opacity-30 hover:border-white/10 hover:text-white/50' : '' }} {{ $isToday ? 'ring-2 ring-blue-400/60' : '' }}"
                                    >
                                        <span>{{ $day->isoFormat('dd') }}</span>
                                        <span class="mt-1 text-xs font-semibold">{{ $day->day }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="rounded-3xl border border-dashed border-white/20 bg-white/5 p-8 text-center text-sm text-white/60">
                            Nenhum hábito ainda. Clique em “+” para criar o primeiro.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="space-y-8">
                @if ($activeHabit)
                    <div class="rounded-3xl border border-white/10 bg-gradient-to-br from-[#11162c] via-[#0b101f] to-[#06070f] p-7 shadow-[0_30px_70px_-24px_rgba(0,0,0,0.75)]">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-[11px] uppercase tracking-[0.4em] text-white/40">Hábito selecionado</p>
                                <h2 class="mt-3 text-2xl font-semibold text-white/90">{{ $activeHabit->name }}</h2>
                                <p class="mt-2 text-sm text-white/60">
                                    {{ __('Acompanhe cada check-in para manter a consistência.') }}
                                </p>
                            </div>
                            <span
                                class="flex h-12 w-12 items-center justify-center rounded-2xl border border-white/10 text-white"
                                style="background: {{ ($activeHabit->color ?? '#2563eb') }}1a"
                            >
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12A9 9 0 1 1 3 12a9 9 0 0 1 18 0Z" />
                                </svg>
                            </span>
                        </div>

                        @if ($stats)
                            <div class="mt-8 grid gap-4 sm:grid-cols-2">
                                <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                                    <p class="text-[10px] uppercase tracking-[0.3em] text-white/40">Monthly check-ins</p>
                                    <p class="mt-3 text-2xl font-semibold text-white/90">{{ $stats['monthly_completed'] }} Dias</p>
                                </div>
                                <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                                    <p class="text-[10px] uppercase tracking-[0.3em] text-white/40">Total check-ins</p>
                                    <p class="mt-3 text-2xl font-semibold text-white/90">{{ $stats['total_completed'] }}</p>
                                </div>
                                <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                                    <p class="text-[10px] uppercase tracking-[0.3em] text-white/40">Monthly check-in rate</p>
                                    <p class="mt-3 text-2xl font-semibold text-white/90">{{ $stats['monthly_rate'] ? $stats['monthly_rate'] . '%' : '—' }}</p>
                                </div>
                                <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                                    <p class="text-[10px] uppercase tracking-[0.3em] text-white/40">Current streak</p>
                                    <p class="mt-3 text-2xl font-semibold text-white/90">{{ $stats['current_streak'] }} Dias</p>
                                </div>
                            </div>
                            <p class="mt-6 text-[11px] uppercase tracking-[0.3em] text-white/40">
                                {{ __('Maior streak: :long dias', ['long' => $stats['longest_streak']]) }} · {{ __('Histórico total: :total check-ins', ['total' => $stats['total_completed']]) }}
                            </p>
                        @endif
                    </div>

                    <div class="rounded-3xl border border-white/10 bg-[#090c17]/95 p-7 shadow-[0_24px_60px_-24px_rgba(0,0,0,0.7)]">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs uppercase tracking-[0.4em] text-white/40">{{ $monthLabel }}</p>
                                <h3 class="mt-2 text-lg font-semibold text-white/80">{{ __('Resumo do mês') }}</h3>
                            </div>
                            <span class="rounded-2xl border border-white/10 px-3 py-1 text-[10px] uppercase tracking-[0.3em] text-white/50">
                                {{ $activeHabit->schedule === 'daily' ? 'Daily' : ucfirst($activeHabit->schedule) }}
                            </span>
                        </div>

                        <div class="mt-6 grid grid-cols-7 gap-2 text-center text-[10px] uppercase tracking-[0.3em] text-white/30">
                            @foreach ($weekdays as $weekday)
                                <span>{{ $weekday }}</span>
                            @endforeach
                        </div>

                        <div class="mt-4 grid grid-cols-7 gap-2 text-sm">
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
                                        class="flex h-14 flex-col items-center justify-center rounded-2xl border text-xs transition {{ $isCompleted ? 'border-blue-400/70 bg-blue-500 text-white shadow-lg shadow-blue-500/30' : 'border-white/10 bg-white/5 text-white/40 hover:border-indigo-400/60 hover:text-white' }} {{ $disabled ? 'cursor-not-allowed opacity-30 hover:border-white/10 hover:text-white/40' : '' }} {{ $isToday ? 'ring-2 ring-blue-400/70' : '' }} {{ $isCurrentMonth ? '' : 'opacity-40' }}"
                                    >
                                        <span class="text-base font-semibold">{{ $day->day }}</span>
                                        <span class="text-[10px] uppercase tracking-[0.3em]">{{ $day->isoFormat('dd') }}</span>
                                    </button>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="rounded-3xl border border-dashed border-white/20 bg-white/5 p-10 text-center text-sm text-white/60">
                        Cadastre um hábito para visualizar estatísticas detalhadas.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
