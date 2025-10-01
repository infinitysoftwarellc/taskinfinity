{{-- Pomodoro focus page rendered without external frameworks. --}}
{{-- resources/views/app/pomodoro/index.blade.php --}}
<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('pomodoro/styles.css') }}">
    @endpush

    @push('scripts')
        <script src="{{ asset('pomodoro/script.js') }}" defer></script>
    @endpush

    <main class="pomodoro-app" data-module="pomodoro">
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
            <div class="pomodoro-ring" role="timer" aria-live="polite" aria-atomic="true" aria-label="Timer de foco">
                <div class="pomodoro-ring__inner">
                    <div class="pomodoro-time" data-role="time">20:00</div>
                </div>
            </div>

            <!-- Timer controls (below the circle) -->
            <div class="pomodoro-controls">
                <button type="button" class="pomodoro-btn pomodoro-btn--primary" data-action="start" aria-pressed="false" data-role="start">
                    Start
                </button>
                <button type="button" class="pomodoro-btn pomodoro-btn--stop" data-action="stop" data-role="stop">
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
                    <p class="pomodoro-card__value" data-role="stat-today-pomo">36</p>
                </article>
                <article class="pomodoro-card">
                    <p class="pomodoro-card__label">Today's Focus</p>
                    <p class="pomodoro-card__value" data-role="stat-today-focus">
                        <span data-role="stat-today-hours">11</span><span class="pomodoro-card__unit">h</span>
                        <span data-role="stat-today-minutes">34</span><span class="pomodoro-card__unit">m</span>
                    </p>
                </article>
                <article class="pomodoro-card">
                    <p class="pomodoro-card__label">Total Pomo</p>
                    <p class="pomodoro-card__value" data-role="stat-total-pomo">1606</p>
                </article>
                <article class="pomodoro-card">
                    <p class="pomodoro-card__label">Total Focus Duration</p>
                    <p class="pomodoro-card__value" data-role="stat-total-focus">
                        <span data-role="stat-total-hours">528</span><span class="pomodoro-card__unit">h</span>
                        <span data-role="stat-total-minutes">29</span><span class="pomodoro-card__unit">m</span>
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

                <p class="pomodoro-record__date" data-role="date-label">Sep 30</p>

                <div class="pomodoro-timeline" data-role="record-list" role="list">
                    <!-- Records populated via script.js -->
                </div>
            </section>
        </aside>
    </main>
</x-app-layout>
