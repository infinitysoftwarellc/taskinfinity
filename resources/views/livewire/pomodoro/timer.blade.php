<div
    wire:poll.1000ms="tick"
    x-data="{
        init() {
            const tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
            if (tz) {
                this.$wire.syncTimezone(tz);
            }
        }
    }"
    class="space-y-10"
>
    <div class="rounded-3xl border border-white/10 bg-black/50 p-10 text-center text-white shadow-2xl shadow-indigo-500/10">
        <p class="text-xs uppercase tracking-[0.6em] text-white/50">{{ $currentLabel }}</p>
        <p class="mt-6 text-7xl font-semibold tracking-tight">{{ $displayTime }}</p>
        <p class="mt-4 text-sm text-white/60">
            @if ($currentSession)
                {{ ucfirst($currentSession->status) }} · {{ ucfirst(str_replace('_', ' ', $currentSession->type)) }}
            @else
                Ready for your next focus block
            @endif
        </p>

        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            @if ($currentSession && $currentSession->status === \App\Models\PomodoroSession::STATUS_RUNNING)
                <button
                    type="button"
                    wire:click="pause"
                    class="rounded-full bg-indigo-500 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-500/30 transition hover:bg-indigo-400"
                >
                    Pause
                </button>
                <button
                    type="button"
                    wire:click="stop"
                    class="rounded-full border border-white/20 px-6 py-3 text-sm font-semibold text-white/80 transition hover:border-white/40 hover:text-white"
                >
                    Stop
                </button>
            @elseif ($currentSession && $currentSession->status === \App\Models\PomodoroSession::STATUS_PAUSED)
                <button
                    type="button"
                    wire:click="resume"
                    class="rounded-full bg-emerald-500 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-500/30 transition hover:bg-emerald-400"
                >
                    Resume
                </button>
                <button
                    type="button"
                    wire:click="stop"
                    class="rounded-full border border-white/20 px-6 py-3 text-sm font-semibold text-white/80 transition hover:border-white/40 hover:text-white"
                >
                    Stop
                </button>
            @else
                <button
                    type="button"
                    wire:click="startFocus"
                    class="rounded-full bg-indigo-500 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-500/30 transition hover:bg-indigo-400"
                >
                    Start focus
                </button>
                <button
                    type="button"
                    wire:click="startShortBreak"
                    class="rounded-full border border-white/20 px-6 py-3 text-sm font-semibold text-white/80 transition hover:border-white/40 hover:text-white"
                >
                    Short break
                </button>
                <button
                    type="button"
                    wire:click="startLongBreak"
                    class="rounded-full border border-white/20 px-6 py-3 text-sm font-semibold text-white/80 transition hover:border-white/40 hover:text-white"
                >
                    Long break
                </button>
            @endif
        </div>
    </div>

    <div
        x-data="{ visible: false }"
        x-on:settings-saved.window="visible = true; setTimeout(() => visible = false, 2000)"
        class="rounded-3xl border border-white/10 bg-white/5 p-8 text-white"
    >
        <div x-show="visible" x-transition class="mb-4 rounded-2xl bg-emerald-500/20 px-4 py-3 text-sm text-emerald-200">
            Pomodoro preferences saved!
        </div>

        <form wire:submit.prevent="saveSettings" class="grid gap-6 md:grid-cols-2">
            <div>
                <label class="text-xs uppercase tracking-[0.3em] text-white/50">Focus (minutes)</label>
                <input
                    type="number"
                    min="1"
                    wire:model.live="focusMinutes"
                    class="mt-2 w-full rounded-2xl border border-white/10 bg-black/40 px-4 py-3 text-white focus:border-indigo-400 focus:outline-none"
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
                    wire:model.live="shortBreakMinutes"
                    class="mt-2 w-full rounded-2xl border border-white/10 bg-black/40 px-4 py-3 text-white focus:border-indigo-400 focus:outline-none"
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
                    wire:model.live="longBreakMinutes"
                    class="mt-2 w-full rounded-2xl border border-white/10 bg-black/40 px-4 py-3 text-white focus:border-indigo-400 focus:outline-none"
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
                    wire:model.live="longBreakEvery"
                    class="mt-2 w-full rounded-2xl border border-white/10 bg-black/40 px-4 py-3 text-white focus:border-indigo-400 focus:outline-none"
                >
                <p class="mt-2 text-xs text-white/50">How many focus blocks before a long break.</p>
                @error('longBreakEvery')
                    <p class="mt-2 text-xs text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2 flex justify-end">
                <button
                    type="submit"
                    class="rounded-full bg-indigo-500 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-500/30 transition hover:bg-indigo-400"
                >
                    Save settings
                </button>
            </div>
        </form>
    </div>

    <div class="rounded-3xl border border-white/10 bg-black/40 p-8 text-white">
        <h3 class="text-sm uppercase tracking-[0.4em] text-white/50">Recent sessions</h3>
        <div class="mt-6 overflow-hidden rounded-2xl border border-white/10">
            <table class="min-w-full divide-y divide-white/5 text-sm">
                <thead class="bg-white/5 text-left text-xs uppercase tracking-[0.2em] text-white/50">
                    <tr>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Started</th>
                        <th class="px-4 py-3">Ended</th>
                        <th class="px-4 py-3">Duration</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse ($recentSessions as $session)
                        @php
                            $typeLabel = ucfirst(str_replace('_', ' ', $session['type']));
                            $statusLabel = ucfirst(str_replace('_', ' ', $session['status']));
                            $startedLabel = $session['meta']['initial_started_at'] ?? optional($session['started_at'])->format('Y-m-d H:i');
                            $endedLabel = $session['meta']['local_finished_at']
                                ?? $session['meta']['local_canceled_at']
                                ?? optional($session['ended_at'])->format('Y-m-d H:i');
                            $durationMinutes = (int) floor(($session['duration_seconds'] ?? 0) / 60);
                        @endphp
                        <tr class="bg-white/5/10">
                            <td class="px-4 py-3 font-medium text-white/80">{{ $typeLabel }}</td>
                            <td class="px-4 py-3 text-white/60">{{ $statusLabel }}</td>
                            <td class="px-4 py-3 text-white/60">{{ $startedLabel ?? '—' }}</td>
                            <td class="px-4 py-3 text-white/60">{{ $endedLabel ?? '—' }}</td>
                            <td class="px-4 py-3 text-white/60">{{ $durationMinutes }} min</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-white/50">No sessions recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
