<aside
    @class([
        'ti-details',
        'is-done' => ($mission['status'] ?? null) === 'done',
    ])
    data-selected-subtask="{{ $selectedSubtaskId ?? '' }}"
    data-mission-id="{{ $mission ? ($mission['id'] ?? '') : '' }}"
>
    @if ($mission)
        @if ($showDatePicker ?? false)
            <div class="ti-date-overlay" wire:click="closeDatePicker"></div>
        @endif

        <div class="ti-details-wrapper">

        <!-- Top bar -->
        <div class="ti-details-top">
            <div class="ti-topbar">
            <div class="ti-topbar-left">
                <button
                    class="ti-check {{ ($mission['status'] ?? null) === 'done' ? 'is-active' : '' }}"
                    type="button"
                    title="Concluir missão"
                    aria-pressed="{{ ($mission['status'] ?? null) === 'done' ? 'true' : 'false' }}"
                    wire:click="toggleCompletion"
                >
                    <span class="ti-check-mark"></span>
                </button>

                <span class="ti-topbar-separator" aria-hidden="true">|</span>

                <div class="ti-date-picker-container" wire:keydown.escape.window="closeDatePicker">
                    <div class="ti-date-actions">
                        <button
                            class="pill {{ ($showDatePicker ?? false) ? 'is-active' : '' }}"
                            type="button"
                            title="Adicionar data"
                            wire:click="toggleDatePicker"
                        >
                            <i class="fa-solid fa-calendar" aria-hidden="true"></i>
                            <span>
                              @if ($mission['due_at'])
                                 {{ $mission['due_at']->format('d/m/Y') }}
                              @else
                                 Adicionar data
                              @endif
                            </span>
                        </button>

                    </div>

                    @if (($showDatePicker ?? false) && $pickerCalendar)
                        <div class="ti-date-popover" wire:click.stop>
                            <div class="ti-date-header">
                                <button
                                    class="nav"
                                    type="button"
                                    title="Mês anterior"
                                    wire:click="movePicker(-1)"
                                >
                                    <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
                                </button>
                                <span class="label">{{ $pickerCalendar['label'] ?? '' }}</span>
                                <button
                                    class="nav"
                                    type="button"
                                    title="Próximo mês"
                                    wire:click="movePicker(1)"
                                >
                                    <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                                </button>
                            </div>

                            <div class="ti-date-grid">
                                @foreach ($pickerCalendar['weekDays'] ?? [] as $weekDay)
                                    <span class="weekday">{{ $weekDay }}</span>
                                @endforeach

                                @foreach ($pickerCalendar['weeks'] ?? [] as $weekIndex => $week)
                                    @foreach ($week as $dayIndex => $day)
                                        @php
                                            $classes = [];
                                            if (!($day['isCurrentMonth'] ?? false)) {
                                                $classes[] = 'is-muted';
                                            }
                                            if ($day['isToday'] ?? false) {
                                                $classes[] = 'is-today';
                                            }
                                            if ($day['isSelected'] ?? false) {
                                                $classes[] = 'is-selected';
                                            }
                                            $classAttr = implode(' ', $classes);
                                        @endphp
                                        <button
                                            class="day {{ $classAttr }}"
                                            type="button"
                                            wire:key="calendar-day-{{ $weekIndex }}-{{ $dayIndex }}-{{ $day['date'] }}"
                                            wire:click="selectDueDate('{{ $day['date'] }}')"
                                        >
                                            {{ $day['label'] ?? '' }}
                                        </button>
                                    @endforeach
                                @endforeach
                            </div>

                            <div class="ti-date-footer">
                                @if ($pickerCalendar['hasSelected'] ?? false)
                                    <button class="link" type="button" wire:click="clearDueDate">Remover data</button>
                                @else
                                    <button class="link disabled" type="button" disabled>Sem data definida</button>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="ti-topbar-right">
                <div class="ti-priority-selector">
                    <button class="icon ghost" type="button" title="Prioridade">
                        <i class="fa-solid fa-flag" aria-hidden="true"></i>
                        <span class="ti-priority-current">{{ $mission['priority_label'] ?? 'Nenhuma' }}</span>
                    </button>

                    <div class="ti-priority-menu" role="menu">
                        <button class="ti-priority-option is-high" type="button" role="menuitem" wire:click="setPriority(3)">
                            <span class="dot"></span> Alta
                        </button>
                        <button class="ti-priority-option is-medium" type="button" role="menuitem" wire:click="setPriority(2)">
                            <span class="dot"></span> Média
                        </button>
                        <button class="ti-priority-option is-low" type="button" role="menuitem" wire:click="setPriority(1)">
                            <span class="dot"></span> Baixa
                        </button>
                        <button class="ti-priority-option is-none" type="button" role="menuitem" wire:click="setPriority(0)">
                            <span class="dot"></span> Nenhuma
                        </button>
                    </div>
                </div>
            </div>
            </div>
        </div>

        <div class="ti-details-scroll">

        <div class="ti-divider"></div>

        <!-- Title + actions -->
        <div class="ti-header">
            <div class="ti-title-block">
                @php
                    $activeSubtask = $mission['active_subtask'] ?? null;
                @endphp
                @if (!empty($activeSubtask))
                    <span class="ti-parent" title="Tarefa pai">{{ $mission['parent_title'] ?? ($mission['title'] ?? '') }}</span>
                    <h1 class="ti-title" title="{{ $activeSubtask['title'] ?? '' }}">{{ ($activeSubtask['title'] ?? '') !== '' ? $activeSubtask['title'] : 'Sem título' }}</h1>
                @elseif (!empty($mission['parent_title']))
                    <span class="ti-parent" title="Tarefa pai">{{ $mission['parent_title'] ?? '' }}</span>
                    <h1 class="ti-title" title="{{ $mission['title'] }}">{{ $mission['title'] ?: 'Sem título' }}</h1>
                @else
                    <h1 class="ti-title" title="{{ $mission['title'] }}">{{ $mission['title'] ?: 'Sem título' }}</h1>
                @endif
            </div>
            <div class="actions">
                <button class="icon ghost" title="Editar"><i class="fa-solid fa-pen" aria-hidden="true"></i></button>
                <div class="ti-menu">
                    <button class="icon ghost" title="Mais opções">
                        <i class="fa-solid fa-ellipsis" aria-hidden="true"></i>
                    </button>
                    <div class="ti-menu-dropdown" role="menu">
                        @include('livewire.tasks.partials.menu-content', ['context' => 'details'])
                    </div>
                </div>
            </div>
        </div>

        <div class="ti-description">
            <div class="ti-format-toolbar" role="toolbar" aria-label="Formatação de texto">
                <button type="button" title="Negrito"><i class="fa-solid fa-bold" aria-hidden="true"></i></button>
                <button type="button" title="Itálico"><i class="fa-solid fa-italic" aria-hidden="true"></i></button>
                <button type="button" title="Sublinhado"><i class="fa-solid fa-underline" aria-hidden="true"></i></button>
                <button type="button" title="Tachado"><i class="fa-solid fa-strikethrough" aria-hidden="true"></i></button>
                <span class="ti-toolbar-separator"></span>
                <button type="button" title="Lista"><i class="fa-solid fa-list" aria-hidden="true"></i></button>
                <button type="button" title="Checklist"><i class="fa-solid fa-square-check" aria-hidden="true"></i></button>
                <button type="button" title="Citação"><i class="fa-solid fa-quote-right" aria-hidden="true"></i></button>
                <span class="ti-toolbar-separator"></span>
                <button type="button" title="Inserir link"><i class="fa-solid fa-link" aria-hidden="true"></i></button>
                <button type="button" title="Inserir imagem"><i class="fa-solid fa-image" aria-hidden="true"></i></button>
            </div>

            <div class="ti-description-content">
                @if ($mission['description'])
                    <p>{!! nl2br(e($mission['description'])) !!}</p>
                @else
                    <p class="muted">Adicione detalhes ou cole notas importantes aqui…</p>
                @endif
            </div>
        </div>

        <!-- Subtasks -->
        <section class="ti-subtasks">
            @php
                $subtasks = $mission['subtasks'] ?? [];
                $maxSubtasks = $maxSubtasks ?? \App\Livewire\Tasks\MainPanel::MAX_SUBTASKS;
                $rootSubtaskCount = count($subtasks);
                $canAddRootSubtask = $rootSubtaskCount < $maxSubtasks;
            @endphp

            @if ($rootSubtaskCount)
                <ul class="ti-subtask-list" role="list">
                    @foreach ($subtasks as $st)
                        @include('livewire.tasks.partials.subtask-item', [
                            'item' => $st,
                            'depth' => 0,
                            'selectedSubtaskId' => $selectedSubtaskId,
                            'maxSubtasks' => $maxSubtasks,
                        ])
                    @endforeach
                </ul>
            @else
                <p class="muted" style="margin:8px 0 0;">Sem subtarefas</p>
            @endif

            @if ($showSubtaskForm && $canAddRootSubtask)
                <form class="ti-subtask-form" wire:submit.prevent="saveSubtask">
                    @if ($newSubtaskParentLabel)
                        <div class="ti-subtask-context">Dentro de <span>{{ $newSubtaskParentLabel }}</span></div>
                    @endif
                    <input
                        type="text"
                        class="ti-subtask-input"
                        placeholder="Título da subtarefa"
                        wire:model.defer="newSubtaskTitle"
                    />
                    <div class="ti-subtask-form-actions">
                        <button type="button" class="ghost" wire:click="cancelSubtaskForm">Cancelar</button>
                        <button type="submit" class="primary">Adicionar</button>
                    </div>
                </form>
            @elseif ($canAddRootSubtask)
                <button class="add-subtask" type="button" wire:click="openSubtaskForm">
                    <i class="fa-solid fa-plus" aria-hidden="true"></i> Adicionar subtarefa
                </button>
            @else
                <div class="subtasks-limit">Limite de {{ $maxSubtasks }} subtarefas atingido.</div>
            @endif
        </section>

        <div class="ti-divider"></div>

        <footer class="ti-footer">
            <div @class(['ti-list-selector', 'is-open' => $showMoveListMenu]) wire:click.away="closeMoveListMenu">
                <button class="ti-list-selector-toggle" type="button" title="Mover para outra lista" wire:click="toggleMoveListMenu">
                    <i class="fa-solid fa-list" aria-hidden="true"></i>
                    <span>{{ $mission['list'] ?? 'Sem lista' }}</span>
                    <i class="fa-solid fa-chevron-down chevron" aria-hidden="true"></i>
                </button>

                @if ($showMoveListMenu)
                    <div class="ti-list-dropdown" role="menu">
                        <button type="button" class="ti-list-option {{ ($mission['list_id'] ?? null) === null ? 'is-active' : '' }}" wire:click.stop="moveToList(null)">
                            <span>Sem lista</span>
                        </button>
                        @foreach ($availableLists as $listOption)
                            <button
                                type="button"
                                class="ti-list-option {{ $listOption['is_current'] ? 'is-active' : '' }}"
                                wire:click.stop="moveToList({{ $listOption['id'] }})"
                            >
                                <span>{{ $listOption['name'] }}</span>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="ti-footer-actions">
                <button class="ti-footer-icon" type="button" title="Iniciar Pomodoro" wire:click="startPomodoro">
                    <i class="fa-solid fa-stopwatch" aria-hidden="true"></i>
                </button>
                <button
                    class="ti-footer-icon {{ ($mission['is_starred'] ?? false) ? 'is-active' : '' }}"
                    type="button"
                    title="Favoritar"
                    aria-pressed="{{ ($mission['is_starred'] ?? false) ? 'true' : 'false' }}"
                    wire:click="toggleStar"
                >
                    <i class="fa-solid fa-star" aria-hidden="true"></i>
                </button>
            </div>
        </footer>
        </div>
    </div>
    @else
        <div class="ti-empty">
            <h3>Selecione uma tarefa</h3>
            <p>Escolha uma tarefa para ver detalhes e subtarefas.</p>
        </div>
    @endif
</aside>
