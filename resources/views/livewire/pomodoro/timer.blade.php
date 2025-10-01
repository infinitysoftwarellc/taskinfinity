{{-- Livewire Pomodoro timer surface. --}}
<section class="pomodoro-shell"
    wire:poll.1000ms="tick"
    wire:keydown.window.space.prevent="toggleTimer"
    wire:keydown.window.r.prevent="resetTimer">
    <div class="pomodoro-app" data-module="pomodoro">
        <!-- LEFT COLUMN — Timer area -->
        <section class="pomodoro-left" aria-labelledby="pomodoro-title">
            <header class="pomodoro-left__header">
                <h1 id="pomodoro-title" class="pomodoro-title">Pomodoro</h1>
                <div class="pomodoro-page-actions" aria-hidden="true">
                    <button type="button" class="pomodoro-icon-btn" title="Adicionar" aria-label="Adicionar">＋</button>
                    <button type="button" class="pomodoro-icon-btn" title="Mais opções" aria-label="Mais opções">⋯</button>
                </div>
            </header>

            <div class="pomodoro-crumb" aria-hidden="true">Focus ▸</div>

            <!-- Timer circle with conic-gradient progress -->
            <div class="pomodoro-ring" role="timer" aria-live="polite" aria-atomic="true" aria-label="Timer de foco"
                style="--progress: {{ $this->progressDegrees }}deg">
                <div class="pomodoro-ring__inner">
                    <div class="pomodoro-time">{{ $this->clockLabel }}</div>
                </div>
            </div>

            <!-- Timer controls (below the circle) -->
            <div class="pomodoro-controls">
                <button type="button" class="pomodoro-btn pomodoro-btn--primary" wire:click="toggleTimer"
                    wire:loading.attr="disabled" wire:target="toggleTimer,resetTimer"
                    aria-pressed="{{ $running ? 'true' : 'false' }}">
                    {{ $running ? 'Pause' : 'Start' }}
                </button>
                <button type="button" class="pomodoro-btn pomodoro-btn--stop" wire:click="resetTimer"
                    wire:loading.attr="disabled" wire:target="resetTimer">
                    Stop
                </button>
            </div>
        </section>

        <!-- RIGHT COLUMN — Overview & Focus record -->
        <aside class="pomodoro-right" aria-label="Estatísticas de foco">
            <header class="pomodoro-right__header">
                <h2 class="pomodoro-right__title">Overview</h2>
            </header>

            <section class="pomodoro-cards" aria-label="Métricas do dia e totais">
                <article class="pomodoro-card">
                    <p class="pomodoro-card__label">Today's Pomo</p>
                    <p class="pomodoro-card__value">{{ $todaysPomo }}</p>
                </article>
                <article class="pomodoro-card">
                    <p class="pomodoro-card__label">Today's Focus</p>
                    <p class="pomodoro-card__value">
                        <span>{{ $this->todayFocusHours }}</span><span class="pomodoro-card__unit">h</span>
                        <span>{{ $this->todayFocusMinutesRemainder }}</span><span class="pomodoro-card__unit">m</span>
                    </p>
                </article>
                <article class="pomodoro-card">
                    <p class="pomodoro-card__label">Total Pomo</p>
                    <p class="pomodoro-card__value">{{ $totalPomo }}</p>
                </article>
                <article class="pomodoro-card">
                    <p class="pomodoro-card__label">Total Focus Duration</p>
                    <p class="pomodoro-card__value">
                        <span>{{ $this->totalFocusHours }}</span><span class="pomodoro-card__unit">h</span>
                        <span>{{ $this->totalFocusMinutesRemainder }}</span><span class="pomodoro-card__unit">m</span>
                    </p>
                </article>
            </section>

            <section class="pomodoro-record" aria-label="Registros de foco">
                <header class="pomodoro-record__header">
                    <h3 class="pomodoro-section-title">Focus Record</h3>
                    <div class="pomodoro-record__actions" aria-hidden="true">
                        <button type="button" class="pomodoro-icon-btn" title="Adicionar registro" aria-label="Adicionar registro">＋</button>
                        <button type="button" class="pomodoro-icon-btn" title="Mais opções" aria-label="Mais opções">⋯</button>
                    </div>
                </header>

                <p class="pomodoro-record__date">{{ $this->dateLabel }}</p>

                <div class="pomodoro-timeline" role="list">
                    @forelse ($this->recordItems as $record)
                        <div class="pomodoro-timeline__item" role="listitem">
                            <div class="pomodoro-timeline__dotbox">
                                <div class="pomodoro-timeline__line" aria-hidden="true"></div>
                                <div class="pomodoro-timeline__dot" aria-hidden="true"></div>
                            </div>
                            <div class="pomodoro-timeline__time">{{ $record['time_label'] }}</div>
                            <div class="pomodoro-timeline__duration">{{ $record['duration_label'] }}</div>
                        </div>
                    @empty
                        <p class="pomodoro-empty">Nenhum ciclo registrado ainda.</p>
                    @endforelse
                </div>
            </section>
        </aside>
    </div>
</section>
