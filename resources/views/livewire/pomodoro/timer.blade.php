{{-- Livewire Pomodoro timer surface adapted to the requested layout. --}}
<section class="pomodoro-shell"
    wire:poll.1000ms="tick"
    wire:keydown.window.space.prevent="toggleTimer"
    wire:keydown.window.r.prevent="resetTimer">
    <div class="pomodoro-container" data-module="pomodoro">
        <div class="pomodoro-left-panel" aria-labelledby="pomodoro-title">
            <div class="pomodoro-header">
                <h1 id="pomodoro-title" class="pomodoro-header__title">Pomodoro</h1>
            </div>

            <div class="pomodoro-action-buttons" aria-hidden="true">
                <button type="button" class="pomodoro-action-btn" title="Adicionar" aria-label="Adicionar">＋</button>
                <button type="button" class="pomodoro-action-btn" title="Mais opções" aria-label="Mais opções">⋯</button>
            </div>

            <div class="pomodoro-focus-label">Focus</div>

            <div class="pomodoro-timer" role="timer" aria-live="polite" aria-atomic="true"
                aria-label="Timer de foco" style="--progress: {{ $this->progressDegrees }}deg">
                <div class="pomodoro-timer__inner">
                    <div class="pomodoro-timer__display">{{ $this->clockLabel }}</div>
                </div>
            </div>

            <div class="pomodoro-controls">
                <button type="button"
                    class="pomodoro-btn {{ $running ? 'pomodoro-btn--secondary' : 'pomodoro-btn--primary' }}"
                    wire:click="toggleTimer" wire:loading.attr="disabled" wire:target="toggleTimer,resetTimer"
                    aria-pressed="{{ $running ? 'true' : 'false' }}">
                    {{ $running ? 'Pause' : 'Start' }}
                </button>
                <button type="button" class="pomodoro-btn pomodoro-btn--stop" wire:click="resetTimer"
                    wire:loading.attr="disabled" wire:target="resetTimer">
                    Stop
                </button>
            </div>
        </div>

        <aside class="pomodoro-right-panel" aria-label="Estatísticas de foco">
            <section class="pomodoro-overview">
                <div class="pomodoro-section-header">
                    <h2 class="pomodoro-section-title">Overview</h2>
                </div>

                <div class="pomodoro-stats-grid" aria-label="Métricas do Pomodoro">
                    <article class="pomodoro-stat-card">
                        <span class="pomodoro-stat-label">Today's Pomo</span>
                        <span class="pomodoro-stat-value">{{ $todaysPomo }}</span>
                    </article>
                    <article class="pomodoro-stat-card">
                        <span class="pomodoro-stat-label">Today's Focus</span>
                        <span class="pomodoro-stat-value">
                            {{ $this->todayFocusHours }}<small>h</small>{{ $this->todayFocusMinutesRemainder }}<small>m</small>
                        </span>
                    </article>
                    <article class="pomodoro-stat-card">
                        <span class="pomodoro-stat-label">Total Pomo</span>
                        <span class="pomodoro-stat-value">{{ $totalPomo }}</span>
                    </article>
                    <article class="pomodoro-stat-card">
                        <span class="pomodoro-stat-label">Total Focus Duration</span>
                        <span class="pomodoro-stat-value">
                            {{ $this->totalFocusHours }}<small>h</small>{{ $this->totalFocusMinutesRemainder }}<small>m</small>
                        </span>
                    </article>
                </div>
            </section>

            <section class="pomodoro-record" aria-label="Registros de foco">
                <div class="pomodoro-section-header">
                    <h3 class="pomodoro-section-title">Focus Record</h3>
                    <div class="pomodoro-record__actions" aria-hidden="true">
                        <button type="button" class="pomodoro-action-btn" title="Adicionar registro"
                            aria-label="Adicionar registro">＋</button>
                        <button type="button" class="pomodoro-more-btn" title="Mais opções" aria-label="Mais opções">⋯</button>
                    </div>
                </div>

                <div class="pomodoro-date-label">{{ $this->dateLabel }}</div>

                <div class="pomodoro-record-list" role="list">
                    @forelse ($this->recordItems as $record)
                        <div class="pomodoro-record-item" role="listitem">
                            <div class="pomodoro-record-left">
                                <span class="pomodoro-record-icon" aria-hidden="true"></span>
                                <span class="pomodoro-record-time">{{ $record['time_label'] }}</span>
                            </div>
                            <span class="pomodoro-record-duration">{{ $record['duration_label'] }}</span>
                        </div>
                    @empty
                        <p class="pomodoro-empty">Nenhum ciclo registrado ainda.</p>
                    @endforelse
                </div>
            </section>
        </aside>
    </div>
</section>
