{{-- resources/views/livewire/habits/habit-tracker.blade.php --}}
@php
    use Illuminate\Support\Carbon;

    $locale = app()->getLocale();
    $checkedDates = $monthlyCheckins->map(fn ($checkin) => $checkin->checked_on_local->toDateString())->all();
    $daysInMonth = $monthStart->daysInMonth;
    $daysDone = $monthStat?->days_done_count ?? $monthlyCheckins->count();
    $monthRate = $daysInMonth > 0 ? (int) round(($daysDone / $daysInMonth) * 100) : 0;
    $currentStreak = optional($selectedHabit?->streakCache)->current_streak ?? 0;
    $longestStreak = optional($selectedHabit?->streakCache)->longest_streak ?? 0;
    $lastCheckin = optional($selectedHabit?->streakCache)->last_checkin_local;
    $calendarStart = $monthStart->copy()->startOfWeek(Carbon::SUNDAY);
    $calendarEnd = $monthEnd->copy()->endOfWeek(Carbon::SATURDAY);
@endphp

<div class="mx-auto w-full max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
    <div class="space-y-1">
        <h1 class="text-2xl font-semibold text-zinc-900 dark:text-white">{{ __('Hábitos') }}</h1>
        <p class="text-sm text-zinc-600 dark:text-zinc-300">
            {{ __('Veja todos os hábitos, check-ins e streaks organizados em um único hub.') }}
        </p>
    </div>

    <div class="grid gap-6 lg:grid-cols-[340px_1fr] xl:grid-cols-[360px_1fr]">
        <section class="rounded-2xl border border-zinc-200 bg-white/80 p-6 shadow-sm backdrop-blur dark:border-zinc-700 dark:bg-zinc-900/80">
            <header class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ __('Seus hábitos') }}</h2>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Escolha um hábito para ver os detalhes.') }}</p>
                </div>
                <span class="rounded-full border border-zinc-200 px-3 py-1 text-xs font-medium text-zinc-500 dark:border-zinc-600 dark:text-zinc-400">
                    {{ trans_choice(':count hábito|:count hábitos', $habits->count(), ['count' => $habits->count()]) }}
                </span>
            </header>

            <div class="mt-6 space-y-3">
                @forelse ($habits as $habit)
                    @php
                        $isSelected = $selectedHabit && $selectedHabit->id === $habit->id;
                        $color = $habit->color ?: '#22c55e';
                        $icon = $habit->icon ?: '🌱';
                        $monthlyCount = $habit->checkins_this_month ?? 0;
                        $streak = optional($habit->streakCache)->current_streak ?? 0;
                    @endphp

                    <button
                        type="button"
                        wire:click="selectHabit({{ $habit->id }})"
                        wire:key="habit-item-{{ $habit->id }}"
                        class="group flex w-full items-center gap-4 rounded-xl border p-3 text-left transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-900 {{ $isSelected ? 'border-emerald-400/70 bg-emerald-50/70 dark:border-emerald-500/60 dark:bg-emerald-500/10' : 'border-transparent bg-white/70 hover:border-zinc-300 hover:bg-zinc-100/80 dark:bg-zinc-900/60 dark:hover:border-zinc-600 dark:hover:bg-zinc-800/70' }}"
                    >
                        <span
                            class="flex h-12 w-12 items-center justify-center rounded-xl text-2xl"
                            style="background: linear-gradient(135deg, {{ $color }}, {{ $color }}33);"
                        >
                            {{ $icon }}
                        </span>

                        <div class="flex flex-1 flex-col">
                            <span class="font-medium text-zinc-900 dark:text-white">{{ $habit->name }}</span>
                            <span class="text-xs text-zinc-500 dark:text-zinc-400">
                                {{ __('Streak: :streak dias • Mês: :count check-ins', ['streak' => $streak, 'count' => $monthlyCount]) }}
                            </span>
                        </div>

                        <div class="flex flex-col items-end">
                            <span class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $monthlyCount }}</span>
                            <span class="text-[11px] uppercase tracking-wide text-zinc-400 dark:text-zinc-500">{{ __('no mês') }}</span>
                        </div>
                    </button>
                @empty
                    <div class="rounded-xl border border-dashed border-zinc-300 bg-white/60 p-6 text-center text-sm text-zinc-500 dark:border-zinc-600 dark:bg-zinc-900/60 dark:text-zinc-400">
                        {{ __('Cadastre um hábito para começar a acompanhar seus check-ins.') }}
                    </div>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl border border-zinc-200 bg-white/80 p-6 shadow-sm backdrop-blur dark:border-zinc-700 dark:bg-zinc-900/80">
            @if ($selectedHabit)
                <header class="flex flex-col gap-4 border-b border-zinc-200 pb-4 dark:border-zinc-700">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-500/10 text-2xl">
                                {{ $selectedHabit->icon ?: '🌱' }}
                            </span>

                            <div>
                                <h2 class="text-xl font-semibold text-zinc-900 dark:text-white">{{ $selectedHabit->name }}</h2>
                                <div class="mt-1 flex items-center gap-2 text-xs text-zinc-500 dark:text-zinc-400">
                                    <span class="inline-flex items-center gap-1 rounded-full border border-emerald-500/20 px-2 py-0.5 text-[11px] font-medium uppercase tracking-wide text-emerald-600 dark:border-emerald-500/30 dark:text-emerald-400">
                                        {{ $selectedHabit->status === 'archived' ? __('Arquivado') : __('Ativo') }}
                                    </span>
                                    @if ($lastCheckin)
                                        <span>
                                            {{ __('Último check-in em :date', ['date' => $lastCheckin->copy()->locale($locale)->translatedFormat('d \d\e M')]) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($selectedHabit->description)
                        <p class="text-sm text-zinc-600 dark:text-zinc-300">
                            {{ $selectedHabit->description }}
                        </p>
                    @endif
                </header>

                <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-xl border border-zinc-200 bg-white/70 p-4 dark:border-zinc-700 dark:bg-zinc-900/70">
                        <p class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{{ __('Dias concluídos') }}</p>
                        <p class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-white">{{ $daysDone }}</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ trans_choice('de :count dia|de :count dias', $daysInMonth, ['count' => $daysInMonth]) }}</p>
                    </div>
                    <div class="rounded-xl border border-zinc-200 bg-white/70 p-4 dark:border-zinc-700 dark:bg-zinc-900/70">
                        <p class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{{ __('Taxa no mês') }}</p>
                        <p class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-white">{{ $monthRate }}%</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Baseado nos check-ins deste mês') }}</p>
                    </div>
                    <div class="rounded-xl border border-zinc-200 bg-white/70 p-4 dark:border-zinc-700 dark:bg-zinc-900/70">
                        <p class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{{ __('Streak atual') }}</p>
                        <p class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-white">{{ $currentStreak }}</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('dias seguidos') }}</p>
                    </div>
                    <div class="rounded-xl border border-zinc-200 bg-white/70 p-4 dark:border-zinc-700 dark:bg-zinc-900/70">
                        <p class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{{ __('Recorde') }}</p>
                        <p class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-white">{{ $longestStreak }}</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('melhor sequência') }}</p>
                    </div>
                </div>

                <div class="mt-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{{ __('Progresso do mês') }}</p>
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">
                                {{ $monthStart->copy()->locale($locale)->isoFormat('MMMM [de] YYYY') }}
                            </h3>
                        </div>
                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                wire:click="goToPreviousMonth"
                                class="flex h-9 w-9 items-center justify-center rounded-full border border-zinc-200 bg-white text-zinc-600 transition hover:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800"
                                aria-label="{{ __('Mês anterior') }}"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                                </svg>
                            </button>
                            <button
                                type="button"
                                wire:click="goToNextMonth"
                                class="flex h-9 w-9 items-center justify-center rounded-full border border-zinc-200 bg-white text-zinc-600 transition hover:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800"
                                aria-label="{{ __('Próximo mês') }}"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5 15.75 12 8.25 19.5" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="h-2 w-full overflow-hidden rounded-full bg-zinc-200 dark:bg-zinc-700">
                        <div class="h-full rounded-full bg-emerald-500 transition-all" style="width: {{ $monthRate }}%"></div>
                    </div>
                    <div class="flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400">
                        <span>{{ __('Meta: :count dias no mês', ['count' => $daysInMonth]) }}</span>
                        <span>{{ __('Concluídos: :count', ['count' => $daysDone]) }}</span>
                    </div>
                </div>

                <div class="mt-8 rounded-2xl border border-zinc-200 bg-white/70 p-5 dark:border-zinc-700 dark:bg-zinc-900/70">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{{ __('Calendário de check-ins') }}</p>
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ $monthStart->copy()->locale($locale)->isoFormat('MMMM [de] YYYY') }}</h3>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-7 gap-2 text-center text-[11px] font-medium uppercase tracking-wide text-zinc-400 dark:text-zinc-500">
                        @foreach (['D', 'S', 'T', 'Q', 'Q', 'S', 'S'] as $dayInitial)
                            <span>{{ $dayInitial }}</span>
                        @endforeach
                    </div>

                    <div class="mt-3 grid grid-cols-7 gap-2 text-center text-sm">
                        @for ($date = $calendarStart->copy(); $date->lte($calendarEnd); $date->addDay())
                            @php
                                $isCurrentMonth = $date->month === $monthStart->month;
                                $isChecked = in_array($date->toDateString(), $checkedDates, true);
                            @endphp

                            <div class="relative flex aspect-square items-center justify-center">
                                <div class="flex h-9 w-9 items-center justify-center rounded-full text-sm transition
                                    {{ $isCurrentMonth ? 'text-zinc-900 dark:text-zinc-100' : 'text-zinc-400 dark:text-zinc-500' }}
                                    {{ $isChecked ? 'bg-emerald-500 text-white shadow-sm dark:bg-emerald-500' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800/70' }}
                                ">
                                    {{ $date->day }}
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>

                <div class="mt-8 rounded-2xl border border-zinc-200 bg-white/70 p-5 dark:border-zinc-700 dark:bg-zinc-900/70">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{{ __('Linha do tempo de check-ins') }}</p>
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ __('Registros recentes') }}</h3>
                        </div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ trans_choice(':count check-in total|:count check-ins no total', $totalCheckins, ['count' => $totalCheckins]) }}</span>
                    </div>

                    <div class="mt-4 space-y-4">
                        @forelse ($recentCheckins as $checkin)
                            <div class="flex items-center gap-4 rounded-xl border border-transparent p-3 hover:border-zinc-200 hover:bg-zinc-100/70 dark:hover:border-zinc-600 dark:hover:bg-zinc-800/70">
                                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-500/10 text-base text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-400">✓</span>

                                <div class="flex flex-1 flex-col">
                                    <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                        {{ $checkin->checked_on_local->copy()->locale($locale)->translatedFormat('d \d\e F, l') }}
                                    </span>
                                    <span class="text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ __('Registrado às :time', ['time' => optional($checkin->created_at)->setTimezone(config('app.timezone'))->format('H:i')]) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Nenhum check-in registrado ainda para este hábito.') }}</p>
                        @endforelse
                    </div>
                </div>
            @else
                <div class="flex h-full min-h-[320px] flex-col items-center justify-center gap-3 text-center">
                    <span class="flex h-14 w-14 items-center justify-center rounded-full bg-zinc-200/60 text-3xl text-zinc-500 dark:bg-zinc-800/60 dark:text-zinc-300">🌱</span>
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ __('Selecione um hábito') }}</h2>
                    <p class="max-w-sm text-sm text-zinc-600 dark:text-zinc-400">
                        {{ __('Quando você tiver hábitos cadastrados, os detalhes aparecerão aqui para análise e acompanhamento diário.') }}
                    </p>
                </div>
            @endif
        </section>
    </div>
</div>
