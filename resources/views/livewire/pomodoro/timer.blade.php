<div
    wire:poll.1000ms="tick"
    x-data="{
        progress: @js($progressRatio),
        update(value) {
            this.progress = value;
        },
        init() {
            const tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
            if (tz) {
                this.$wire.syncTimezone(tz);
            }
        }
    }"
    x-effect="update(@js($progressRatio))"
    class="flex w-full flex-col gap-6 lg:flex-row"
>
    <div class="flex-1 space-y-6">
        <div class="overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-white/10 via-black/40 to-black/80 p-6 sm:p-10">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                <div class="flex items-center gap-4">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-500 text-white shadow-lg shadow-indigo-500/30">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <circle cx="12" cy="12" r="8"></circle>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5V12l2.5 2.5"></path>
                        </svg>
                    </span>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.4em] text-white/50">Pomodoro</p>
                        <h2 class="mt-2 text-3xl font-semibold tracking-tight text-white sm:text-4xl">Deep focus timer</h2>
                        <p class="mt-2 text-sm text-white/60">Orchestrate your cycles without worrying about losing momentum.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 rounded-full border border-white/10 bg-black/40 p-1 text-xs font-medium text-white/50">
                    <span class="rounded-full bg-indigo-500 px-3 py-1 text-white shadow shadow-indigo-500/40">Pomodoro</span>
                    <span class="rounded-full px-3 py-1">Pomo</span>
                    <span class="rounded-full px-3 py-1">Stopwatch</span>
                </div>
            </div>

            <div class="mt-10 flex flex-col items-center gap-8">
                <p class="text-xs font-semibold uppercase tracking-[0.5em] text-white/50">{{ $currentLabel }} ></p>

                <div class="relative flex h-72 w-72 items-center justify-center sm:h-[22rem] sm:w-[22rem]">
                    <div class="absolute inset-0 rounded-full bg-gradient-to-br from-indigo-500/10 via-transparent to-transparent"></div>
                    <div
                        class="absolute inset-0 rounded-full border border-white/10"
                        :style="`background: conic-gradient(#4f46e5 ${(Math.max(0, Math.min(1, progress)) * 360).toFixed(2)}deg, rgba(79,70,229,0.08) ${(Math.max(0, Math.min(1, progress)) * 360).toFixed(2)}deg); transition: background 1s linear;`"
                    ></div>
                    <div class="absolute inset-3 rounded-full bg-black/60 shadow-inner shadow-black/80"></div>
                    <div class="absolute inset-12 rounded-full border border-white/10 bg-black/80"></div>
                    <div class="relative text-center">
                        <p class="text-[4.5rem] font-semibold tracking-tight text-white sm:text-7xl">{{ $displayTime }}</p>
                        <p class="mt-3 text-sm text-white/50">
                            @if ($currentSession)
                                {{ ucfirst($currentSession->status) }} · {{ ucfirst(str_replace('_', ' ', $currentSession->type)) }}
                            @else
                                Ready for your next focus block
                            @endif
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-center gap-3">
                    @if ($isRunning)
                        <button
                            type="button"
                            wire:click="pause"
                            class="inline-flex items-center gap-2 rounded-full bg-indigo-500 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-500/30 transition hover:bg-indigo-400"
                        >
                            <span>Pause</span>
                        </button>
                        <button
                            type="button"
                            wire:click="stop"
                            class="inline-flex items-center gap-2 rounded-full border border-white/20 px-6 py-3 text-sm font-semibold text-white/80 transition hover:border-white/40 hover:text-white"
                        >
                            Stop
                        </button>
                    @elseif ($isPaused)
                        <button
                            type="button"
                            wire:click="resume"
                            class="inline-flex items-center gap-2 rounded-full bg-emerald-500 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-500/30 transition hover:bg-emerald-400"
                        >
                            Resume
                        </button>
                        <button
                            type="button"
                            wire:click="stop"
                            class="inline-flex items-center gap-2 rounded-full border border-white/20 px-6 py-3 text-sm font-semibold text-white/80 transition hover:border-white/40 hover:text-white"
                        >
                            Stop
                        </button>
                    @else
                        <button
                            type="button"
                            wire:click="startFocus"
                            class="inline-flex items-center gap-2 rounded-full bg-indigo-500 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-500/30 transition hover:bg-indigo-400"
                        >
                            Start
                        </button>
                        <button
                            type="button"
                            wire:click="startShortBreak"
                            class="inline-flex items-center gap-2 rounded-full border border-white/20 px-6 py-3 text-sm font-semibold text-white/80 transition hover:border-white/40 hover:text-white"
                        >
                            Short break
                        </button>
                        <button
                            type="button"
                            wire:click="startLongBreak"
                            class="inline-flex items-center gap-2 rounded-full border border-white/20 px-6 py-3 text-sm font-semibold text-white/80 transition hover:border-white/40 hover:text-white"
                        >
                            Long break
                        </button>
                    @endif
                </div>

                <div class="grid w-full max-w-xl grid-cols-3 gap-4 text-center text-xs text-white/50">
                    <div class="rounded-2xl border border-white/10 bg-black/40 px-4 py-3">
                        <p class="text-sm font-semibold text-white">{{ $focusMinutes }}m</p>
                        <p class="mt-1 uppercase tracking-[0.3em]">Focus</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-black/40 px-4 py-3">
                        <p class="text-sm font-semibold text-white">{{ $shortBreakMinutes }}m</p>
                        <p class="mt-1 uppercase tracking-[0.3em]">Short</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-black/40 px-4 py-3">
                        <p class="text-sm font-semibold text-white">{{ $longBreakMinutes }}m</p>
                        <p class="mt-1 uppercase tracking-[0.3em]">Long</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-6">
            <div
                x-data="{ visible: false, open: false }"
                x-on:settings-saved.window="visible = true; setTimeout(() => visible = false, 2000)"
                class="rounded-3xl border border-white/10 bg-black/40 p-6 sm:p-8"
            >
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-sm font-semibold uppercase tracking-[0.3em] text-white/50">Timer preferences</h3>
                        <p class="mt-2 text-sm text-white/60">Fine-tune how long you focus and recover.</p>
                    </div>
                    <button
                        type="button"
                        class="flex h-10 w-10 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-white/70 transition hover:border-white/30 hover:text-white"
                        x-on:click="open = !open"
                        x-bind:aria-expanded="open"
                        aria-controls="timer-preferences-panel"
                    >
                        <svg
                            class="h-4 w-4 transition-transform"
                            :class="open ? 'rotate-45' : ''"
                            viewBox="0 0 20 20"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.5"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 4.167v11.666M4.167 10h11.666" />
                        </svg>
                    </button>
                </div>

                <div
                    x-cloak
                    x-show="visible"
                    x-transition
                    class="mt-4 rounded-2xl bg-emerald-500/20 px-4 py-3 text-sm text-emerald-200"
                >
                    Pomodoro preferences saved!
                </div>

                <div
                    x-cloak
                    x-show="open"
                    x-transition
                    id="timer-preferences-panel"
                    class="mt-6 grid gap-5 sm:grid-cols-2"
                >
                    <div>
                        <label class="text-xs uppercase tracking-[0.3em] text-white/50">Focus (minutes)</label>
                        <input
                            type="number"
                            min="1"
                            wire:model.lazy="focusMinutes"
                            class="mt-2 w-full rounded-2xl border border-white/10 bg-black/60 px-4 py-3 text-sm text-white focus:border-indigo-400 focus:outline-none"
                        >
                        @error('focusMinutes')
                            <p class="mt-2 text-xs text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-[0.3em] text-white/50">Short break (minutes)</label>
                        <input
                            type="number"
                            min="1"
                            wire:model.lazy="shortBreakMinutes"
                            class="mt-2 w-full rounded-2xl border border-white/10 bg-black/60 px-4 py-3 text-sm text-white focus:border-indigo-400 focus:outline-none"
                        >
                        @error('shortBreakMinutes')
                            <p class="mt-2 text-xs text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-[0.3em] text-white/50">Long break (minutes)</label>
                        <input
                            type="number"
                            min="1"
                            wire:model.lazy="longBreakMinutes"
                            class="mt-2 w-full rounded-2xl border border-white/10 bg-black/60 px-4 py-3 text-sm text-white focus:border-indigo-400 focus:outline-none"
                        >
                        @error('longBreakMinutes')
                            <p class="mt-2 text-xs text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-[0.3em] text-white/50">Long break every</label>
                        <input
                            type="number"
                            min="1"
                            wire:model.lazy="longBreakEvery"
                            class="mt-2 w-full rounded-2xl border border-white/10 bg-black/60 px-4 py-3 text-sm text-white focus:border-indigo-400 focus:outline-none"
                        >
                        <p class="mt-2 text-xs text-white/50">How many focus blocks before a long break.</p>
                        @error('longBreakEvery')
                            <p class="mt-2 text-xs text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="w-full space-y-6 lg:w-96 xl:w-[28rem]">
        <div class="rounded-3xl border border-white/10 bg-black/40 p-6 sm:p-8">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-[0.3em] text-white/50">Overview</h3>
                    <p class="mt-2 text-xs text-white/60">Today at a glance</p>
                </div>
                <button type="button" class="flex h-10 w-10 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-white/70">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 4.167v11.666M4.167 10h11.666" />
                    </svg>
                </button>
            </div>

            @php
                $todayFocusHours = $overview['today_focus_seconds'] / 3600;
                $totalFocusHours = $overview['total_focus_seconds'] / 3600;
            @endphp

            <dl class="mt-8 grid grid-cols-2 gap-4 text-sm">
                <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-4">
                    <dt class="text-xs uppercase tracking-[0.3em] text-white/50">Today's Pomo</dt>
                    <dd class="mt-2 text-2xl font-semibold text-white">{{ $overview['today_pomodoros'] }}</dd>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-4">
                    <dt class="text-xs uppercase tracking-[0.3em] text-white/50">Today's Focus</dt>
                    <dd class="mt-2 text-2xl font-semibold text-white">{{ number_format($todayFocusHours, 2) }}h</dd>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-4">
                    <dt class="text-xs uppercase tracking-[0.3em] text-white/50">Total Pomo</dt>
                    <dd class="mt-2 text-2xl font-semibold text-white">{{ $overview['total_pomodoros'] }}</dd>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-4">
                    <dt class="text-xs uppercase tracking-[0.3em] text-white/50">Total Focus Duration</dt>
                    <dd class="mt-2 text-2xl font-semibold text-white">{{ number_format($totalFocusHours, 2) }}h</dd>
                </div>
            </dl>
        </div>

        @php
            $activeTimezone = $timezone !== '' ? $timezone : config('app.timezone', 'UTC');
            $chartDateLabel = \Illuminate\Support\Carbon::now($activeTimezone)->format('M d');
            $chartStart = null;
            $chartEnd = null;
            foreach ($todayRecords as $record) {
                if ($record['start_minutes'] !== null) {
                    $chartStart = $chartStart === null ? $record['start_minutes'] : min($chartStart, $record['start_minutes']);
                }
                if ($record['end_minutes'] !== null) {
                    $chartEnd = $chartEnd === null ? $record['end_minutes'] : max($chartEnd, $record['end_minutes']);
                }
            }
            if ($chartStart === null || $chartEnd === null || $chartEnd <= $chartStart) {
                $chartStart = 8 * 60;
                $chartEnd = 18 * 60;
            }
            $chartSpan = max(60, $chartEnd - $chartStart);
            $chartMid = $chartStart + ($chartSpan / 2);
            $chartLabels = [
                str_pad((string) floor($chartStart / 60), 2, '0', STR_PAD_LEFT) . 'h',
                str_pad((string) floor($chartMid / 60), 2, '0', STR_PAD_LEFT) . 'h',
                str_pad((string) floor($chartEnd / 60), 2, '0', STR_PAD_LEFT) . 'h',
            ];
        @endphp

        <div class="rounded-3xl border border-white/10 bg-black/40 p-6 sm:p-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-500/20 text-indigo-300">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5h15M4.5 4.5h15M7.5 4.5v15M16.5 4.5v15" />
                        </svg>
                    </span>
                    <div>
                        <h3 class="text-base font-semibold text-white">Pomo</h3>
                        <p class="text-xs text-white/60">Focus timeline</p>
                    </div>
                </div>
                <span class="text-xs text-white/40">{{ $chartDateLabel }}</span>
            </div>

            <div class="relative mt-8 h-40 overflow-hidden rounded-2xl border border-white/10 bg-black/60">
                <div class="absolute inset-0 bg-gradient-to-b from-transparent via-white/10 to-transparent"></div>
                <div class="absolute left-6 right-6 top-10 h-px bg-white/10"></div>
                <div class="absolute left-6 right-6 bottom-12 h-px bg-white/10"></div>
                <div class="absolute inset-0">
                    @foreach ($todayRecords as $record)
                        @if ($record['start_minutes'] !== null && $record['end_minutes'] !== null)
                            @php
                                $segmentStart = max($chartStart, $record['start_minutes']);
                                $segmentEnd = max($segmentStart, min($chartEnd, $record['end_minutes']));
                                $widthPercent = ($segmentEnd - $segmentStart) / $chartSpan * 100;
                                $leftPercent = ($segmentStart - $chartStart) / $chartSpan * 100;
                            @endphp
                            <div
                                class="absolute bottom-12 h-3 rounded-full bg-indigo-500/80 shadow-lg shadow-indigo-500/30"
                                style="left: {{ max(0, min(100, $leftPercent)) }}%; width: {{ max(2, $widthPercent) }}%;"
                            ></div>
                        @endif
                    @endforeach
                    @if (count($todayRecords) === 0)
                        <div class="absolute inset-0 flex items-center justify-center text-sm text-white/50">
                            No focus activity yet.
                        </div>
                    @endif
                </div>
                <div class="absolute bottom-4 left-6 right-6 flex justify-between text-xs text-white/40">
                    <span>{{ $chartLabels[0] }}</span>
                    <span>{{ $chartLabels[1] }}</span>
                    <span>{{ $chartLabels[2] }}</span>
                </div>
            </div>
        </div>

        @php
            $focusRecords = array_values(array_filter($todayRecords, fn ($record) => $record['type'] === \App\Models\PomodoroSession::TYPE_FOCUS));
        @endphp

        <div class="rounded-3xl border border-white/10 bg-black/40 p-6 sm:p-8">
            <h3 class="text-sm font-semibold uppercase tracking-[0.3em] text-white/50">Focus record</h3>
            <ul class="mt-6 space-y-4">
                @forelse ($focusRecords as $record)
                    <li class="flex items-center justify-between gap-4 rounded-2xl border border-white/10 bg-black/60 px-4 py-3">
                        <div>
                            <p class="text-sm font-medium text-white">{{ $record['label'] }}</p>
                            <p class="text-xs text-white/50 capitalize">{{ str_replace('_', ' ', $record['status']) }}</p>
                        </div>
                        <span class="text-sm font-semibold text-indigo-300">{{ $record['duration_label'] }}</span>
                    </li>
                @empty
                    <li class="rounded-2xl border border-dashed border-white/10 px-4 py-6 text-center text-sm text-white/50">
                        No focus sessions recorded today.
                    </li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
