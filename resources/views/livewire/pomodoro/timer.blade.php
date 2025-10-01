{{-- Livewire Pomodoro timer surface adapted to the requested layout. --}}
<section class="pomodoro-shell"
    wire:poll.1000ms="tick"
    wire:keydown.window.space.prevent="toggleTimer"
    wire:keydown.window.r.prevent="confirmStop">
    <div class="pomodoro-container" data-module="pomodoro">
        <div class="pomodoro-left-panel" aria-labelledby="pomodoro-title">
            <div class="pomodoro-header">
                <h1 id="pomodoro-title" class="pomodoro-header__title">Pomodoro</h1>
            </div>

            <div class="pomodoro-header__actions" x-data="{ open: false }"
                @click.outside="open = false" @keydown.escape.window="open = false">
                <button type="button" class="pomodoro-header__button" @click="open = !open"
                    x-bind:aria-expanded="open"
                    aria-haspopup="true"
                    aria-controls="pomodoro-settings-menu">
                    {{ __('Configurações') }}
                    <span class="pomodoro-header__chevron" aria-hidden="true">▾</span>
                </button>

                <div class="pomodoro-dropdown" id="pomodoro-settings-menu" role="menu" x-cloak
                    x-show="open" x-transition>
                    <a class="pomodoro-dropdown__item" href="{{ route('app.pomodoro.stats') }}" role="menuitem">
                        {{ __('Estatísticas') }}
                    </a>
                    <button type="button" class="pomodoro-dropdown__item" role="menuitem"
                        wire:click="openFocusConfig" @click="open = false">
                        {{ __('Configuração do foco') }}
                    </button>
                </div>
            </div>

            <div class="pomodoro-phase-wrapper">
                <span class="pomodoro-focus-label">{{ $this->phaseLabel }}</span>
                <nav class="pomodoro-phase-switch" aria-label="{{ __('Selecionar ciclo') }}">
                    <button type="button"
                        class="pomodoro-phase-btn {{ $phase === 'focus' ? 'pomodoro-phase-btn--active' : '' }}"
                        wire:click="selectPhase('focus')" wire:loading.attr="disabled"
                        @if ($sessionId) disabled @endif aria-pressed="{{ $phase === 'focus' ? 'true' : 'false' }}">
                        {{ __('Pomodoro') }}
                    </button>
                    <button type="button"
                        class="pomodoro-phase-btn {{ $phase === 'short_break' ? 'pomodoro-phase-btn--active' : '' }}"
                        wire:click="selectPhase('short_break')" wire:loading.attr="disabled"
                        @if ($sessionId) disabled @endif aria-pressed="{{ $phase === 'short_break' ? 'true' : 'false' }}">
                        {{ __('Pausa') }}
                    </button>
                    <button type="button"
                        class="pomodoro-phase-btn {{ $phase === 'long_break' ? 'pomodoro-phase-btn--active' : '' }}"
                        wire:click="selectPhase('long_break')" wire:loading.attr="disabled"
                        @if ($sessionId) disabled @endif aria-pressed="{{ $phase === 'long_break' ? 'true' : 'false' }}">
                        {{ __('Pausa longa') }}
                    </button>
                </nav>
            </div>

            <div class="pomodoro-timer" role="timer" aria-live="polite" aria-atomic="true"
                aria-label="Timer de foco" style="--progress: {{ $this->progressDegrees }}deg">
                <div class="pomodoro-timer__inner">
                    <div class="pomodoro-timer__display">{{ $this->clockLabel }}</div>
                </div>
            </div>

            <div class="pomodoro-controls">
                <button type="button"
                    class="pomodoro-btn {{ $running ? 'pomodoro-btn--secondary' : 'pomodoro-btn--primary' }}"
                    wire:click="toggleTimer" wire:loading.attr="disabled" wire:target="toggleTimer"
                    aria-pressed="{{ $running ? 'true' : 'false' }}">
                    {{ $running ? 'Pause' : 'Start' }}
                </button>
                <button type="button" class="pomodoro-btn pomodoro-btn--stop" wire:click="confirmStop"
                    wire:loading.attr="disabled" wire:target="confirmStop">
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
                        <button type="button" class="pomodoro-record-item" role="listitem"
                            wire:key="record-{{ $record['id'] }}"
                            wire:click="openRecord({{ $record['id'] }})"
                            aria-label="{{ __('Visualizar registro de :type', ['type' => $record['type_label']]) }}">
                            <div class="pomodoro-record-left">
                                <span class="pomodoro-record-icon pomodoro-record-icon--{{ $record['phase'] }}"
                                    aria-hidden="true"></span>
                                <div class="pomodoro-record-meta">
                                    <span class="pomodoro-record-time">{{ $record['time_label'] }}</span>
                                    <span class="pomodoro-record-type">{{ $record['type_label'] }}</span>
                                </div>
                            </div>
                            <span class="pomodoro-record-duration">{{ $record['duration_label'] }}</span>
                        </button>
                    @empty
                        <p class="pomodoro-empty">Nenhum ciclo registrado ainda.</p>
                    @endforelse
                </div>
            </section>
        </aside>
    </div>

    @if ($showStopConfirmation)
        <div class="pomodoro-modal" role="dialog" aria-modal="true" aria-labelledby="stop-modal-title">
            <div class="pomodoro-modal__backdrop" wire:click="cancelStop"></div>
            <div class="pomodoro-modal__panel">
                <h2 id="stop-modal-title">{{ __('Encerrar ciclo?') }}</h2>
                <p>{{ __('Deseja realmente encerrar este ciclo de Pomodoro?') }}</p>
                @if ($allowSaveOnStop)
                    <p class="pomodoro-modal__hint">{{ __('Você pode sair salvando o progresso dos últimos minutos.') }}</p>
                @else
                    <p class="pomodoro-modal__hint">{{ __('Menos de 5 minutos decorreram — nada será salvo se você confirmar.') }}</p>
                @endif

                <div class="pomodoro-modal__actions">
                    <button type="button" class="pomodoro-btn pomodoro-btn--secondary" wire:click="cancelStop"
                        wire:loading.attr="disabled" wire:target="stopWithoutSaving,stopAndSave">
                        {{ __('Continuar') }}
                    </button>
                    <button type="button" class="pomodoro-btn pomodoro-btn--stop" wire:click="stopWithoutSaving"
                        wire:loading.attr="disabled" wire:target="stopWithoutSaving">
                        {{ $allowSaveOnStop ? __('Sair sem salvar') : __('Encerrar') }}
                    </button>
                    @if ($allowSaveOnStop)
                        <button type="button" class="pomodoro-btn pomodoro-btn--primary" wire:click="stopAndSave"
                            wire:loading.attr="disabled" wire:target="stopAndSave">
                            {{ __('Sair e salvar') }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    @if ($showRecordModal && ! empty($recordModal))
        <div class="pomodoro-modal" role="dialog" aria-modal="true" aria-labelledby="record-modal-title">
            <div class="pomodoro-modal__backdrop" wire:click="closeRecordModal"></div>
            <div class="pomodoro-modal__panel pomodoro-record-modal">
                <button type="button" class="pomodoro-modal__close" wire:click="closeRecordModal"
                    aria-label="{{ __('Fechar') }}">×</button>
                <h2 id="record-modal-title">{{ $recordModal['type_label'] ?? __('Pomodoro') }}</h2>
                <p class="pomodoro-record-modal__date">{{ $recordModal['date_full_label'] ?? '' }}</p>
                <dl class="pomodoro-record-details">
                    <div class="pomodoro-record-details__item">
                        <dt>{{ __('Horário') }}</dt>
                        <dd>{{ $recordModal['time_label'] ?? '' }}</dd>
                    </div>
                    <div class="pomodoro-record-details__item">
                        <dt>{{ __('Duração') }}</dt>
                        <dd>{{ $recordModal['duration_full_label'] ?? '' }}</dd>
                    </div>
                    <div class="pomodoro-record-details__item">
                        <dt>{{ __('Fase') }}</dt>
                        <dd>{{ $recordModal['type_label'] ?? '' }}</dd>
                    </div>
                </dl>
                <div class="pomodoro-record-modal__footer">
                    <button type="button" class="pomodoro-record-delete-btn"
                        wire:click="deleteRecord({{ $recordModal['id'] ?? 0 }})" wire:loading.attr="disabled"
                        title="{{ __('Excluir registro') }}">
                        <svg class="pomodoro-record-delete-icon" viewBox="0 0 24 24" aria-hidden="true"
                            focusable="false">
                            <path d="M9 9h1v9H9zm5 0h1v9h-1z"></path>
                            <path d="M19 5h-3.5l-1-1h-5l-1 1H5v2h14zm-2 3H7l1 12h8z"></path>
                        </svg>
                        <span>{{ __('Excluir') }}</span>
                    </button>
                    <button type="button" class="pomodoro-btn pomodoro-btn--secondary"
                        wire:click="closeRecordModal">{{ __('Fechar') }}</button>
                </div>
            </div>
        </div>
    @endif

    @if ($showFocusConfig)
        <div class="pomodoro-modal" role="dialog" aria-modal="true" aria-labelledby="focus-config-title">
            <div class="pomodoro-modal__backdrop" wire:click="closeFocusConfig"></div>
            <div class="pomodoro-modal__panel pomodoro-focus-modal">
                <button type="button" class="pomodoro-modal__close" wire:click="closeFocusConfig"
                    aria-label="{{ __('Fechar') }}">×</button>
                <h2 id="focus-config-title">{{ __('Configuração do foco') }}</h2>
                <p class="pomodoro-focus-hint">{{ __('Os ajustes são salvos automaticamente.') }}</p>

                <div class="pomodoro-focus-form">
                    <div class="pomodoro-focus-field">
                        <label class="pomodoro-focus-label" for="focus-minutes">{{ __('Pomodoro (min)') }}</label>
                        <input id="focus-minutes" type="number" min="1" max="180" step="1" inputmode="numeric"
                            class="pomodoro-focus-input" wire:model.live.debounce.500ms="focusMinutes">
                        @error('focusMinutes')
                            <p class="pomodoro-focus-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pomodoro-focus-field">
                        <label class="pomodoro-focus-label" for="short-break-minutes">{{ __('Pausa curta (min)') }}</label>
                        <input id="short-break-minutes" type="number" min="1" max="60" step="1" inputmode="numeric"
                            class="pomodoro-focus-input" wire:model.live.debounce.500ms="shortBreakMinutes">
                        @error('shortBreakMinutes')
                            <p class="pomodoro-focus-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pomodoro-focus-field">
                        <label class="pomodoro-focus-label" for="long-break-minutes">{{ __('Pausa longa (min)') }}</label>
                        <input id="long-break-minutes" type="number" min="1" max="180" step="1" inputmode="numeric"
                            class="pomodoro-focus-input" wire:model.live.debounce.500ms="longBreakMinutes">
                        @error('longBreakMinutes')
                            <p class="pomodoro-focus-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pomodoro-focus-field">
                        <label class="pomodoro-focus-label" for="cycles-before-long-break">{{ __('Pomodoros por intervalo') }}</label>
                        <input id="cycles-before-long-break" type="number" min="1" max="12" step="1"
                            inputmode="numeric" class="pomodoro-focus-input"
                            wire:model.live.debounce.500ms="cyclesBeforeLongBreak">
                        @error('cyclesBeforeLongBreak')
                            <p class="pomodoro-focus-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="pomodoro-modal__actions">
                    <button type="button" class="pomodoro-btn pomodoro-btn--secondary"
                        wire:click="closeFocusConfig">{{ __('Fechar') }}</button>
                </div>
            </div>
        </div>
    @endif
</section>
