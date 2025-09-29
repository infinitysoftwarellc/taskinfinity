<aside class="ti-details">
    @if ($mission)
        @if ($showDatePicker ?? false)
            <div class="ti-date-overlay" wire:click="closeDatePicker"></div>
        @endif

        <!-- Top bar -->
        <div class="ti-topbar">
            <div class="left">
                <div class="ti-date-picker-container" wire:keydown.escape.window="closeDatePicker">
                    <button
                        class="pill {{ ($showDatePicker ?? false) ? 'is-active' : '' }}"
                        type="button"
                        title="Adicionar data"
                        wire:click="toggleDatePicker"
                    >
                        <i data-lucide="calendar"></i>
                        <span>
                          @if ($mission['due_at'])
                             {{ $mission['due_at']->format('d/m/Y') }}
                          @else
                             Adicionar data
                          @endif
                        </span>
                    </button>

                    @if (($showDatePicker ?? false) && $pickerCalendar)
                        <div class="ti-date-popover" wire:click.stop>
                            <div class="ti-date-header">
                                <button
                                    class="nav"
                                    type="button"
                                    title="Mês anterior"
                                    wire:click="movePicker(-1)"
                                >
                                    <i data-lucide="chevron-left"></i>
                                </button>
                                <span class="label">{{ $pickerCalendar['label'] ?? '' }}</span>
                                <button
                                    class="nav"
                                    type="button"
                                    title="Próximo mês"
                                    wire:click="movePicker(1)"
                                >
                                    <i data-lucide="chevron-right"></i>
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
            <div class="right">
                <button class="icon ghost" type="button" title="Flag"><i data-lucide="flag"></i></button>
            </div>
        </div>

        <!-- Title + actions -->
        <div class="ti-header">
            <h2 class="ti-title" title="{{ $mission['title'] }}">{{ $mission['title'] ?: 'No Title' }}</h2>
            <div class="actions">
                <button class="icon ghost" title="Editar"><i data-lucide="pencil"></i></button>
                <button class="icon ghost" title="Mais opções"><i data-lucide="more-horizontal"></i></button>
            </div>
        </div>

        <div class="ti-divider"></div>

        <!-- Subtasks -->
        <section class="ti-subtasks">
            @php
                // $mission['subtasks'] = [
                //   ['id'=>1,'title'=>'s','done'=>false,'children'=>[
                //       ['id'=>2,'title'=>'S','done'=>false,'children'=>[]],
                //       ['id'=>3,'title'=>'No Title','done'=>false,'children'=>[]],
                //   ]],
                // ];
                $subtasks = $mission['subtasks'] ?? [];
            @endphp

            @if (count($subtasks))
                <ul class="ti-list" role="list">
                    @foreach ($subtasks as $st)
                        @include('components.subtask-item', ['item'=>$st, 'depth'=>0])
                    @endforeach
                </ul>
            @else
                <p class="muted" style="margin:8px 0 0;">Sem subtarefas</p>
            @endif

            <button class="add-subtask" type="button"
                {{-- wire:click="openNewSubtask({{ $mission['id'] }})" --}}
            >
                <i data-lucide="plus"></i> Add Subtask
            </button>
        </section>
    @else
        <div class="ti-empty">
            <h3>Selecione uma tarefa</h3>
            <p>Escolha uma tarefa para ver detalhes e subtarefas.</p>
        </div>
    @endif
</aside>
