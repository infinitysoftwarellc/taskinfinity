{{-- This Blade view renders the livewire tasks details interface. --}}
@php
    $missionData = is_array($mission) ? $mission : null;
    $activeSubtaskContext = $missionData['active_subtask'] ?? null;
    $isSubtaskContext = !empty($activeSubtaskContext);
    $isDoneContext = $isSubtaskContext
        ? (bool) ($activeSubtaskContext['is_done'] ?? false)
        : ($missionData['status'] ?? null) === 'done';
@endphp

<aside @class([
    'ti-details',
    'is-done' => $isDoneContext,
    'is-subtask' => $isSubtaskContext,
]) data-selected-subtask="{{ $selectedSubtaskId ?? '' }}"
    data-mission-id="{{ $missionData['id'] ?? '' }}">
    @if ($missionData)
        @php
            $activeSubtask = $missionData['active_subtask'] ?? null;
            $isSubtask = !empty($activeSubtask);
        @endphp
        @if ($showDatePicker ?? false)
            <div class="ti-date-overlay" wire:click="closeDatePicker"></div>
        @endif

        <div class="ti-details-wrapper">

            <!-- Top bar -->
            <div class="ti-details-top">
                @if ($isSubtask)
                    @php
                        $subtaskCreated = $activeSubtask['created_at'] ?? null;
                        $subtaskDue = $activeSubtask['due_at'] ?? null;
                        $createdLabel =
                            $subtaskCreated instanceof \Carbon\CarbonInterface ? $subtaskCreated->format('d/m/Y') : '—';
                        $dueLabel =
                            $subtaskDue instanceof \Carbon\CarbonInterface
                                ? $subtaskDue->format('d/m/Y')
                                : ($mission['due_at'] instanceof \Carbon\CarbonInterface
                                    ? $mission['due_at']->format('d/m/Y')
                                    : 'Sem prazo');
                        $createdClass = $createdLabel === '—' ? 'is-empty' : '';
                        $dueClass = $dueLabel === 'Sem prazo' ? 'is-empty' : '';
                    @endphp
                    <div class="ti-topbar">
                        <div class="ti-topbar-left">
                            <!-- Botão de concluir subtarefa -->
                            <button class="ti-check {{ $activeSubtask['is_done'] ?? false ? 'is-active' : '' }}"
                                type="button" title="Concluir subtarefa"
                                aria-pressed="{{ $activeSubtask['is_done'] ?? false ? 'true' : 'false' }}"
                                wire:click="toggleCheckpoint({{ $activeSubtask['id'] }})">
                                <span class="ti-check-mark"></span>
                            </button>

                            <!-- Separador visual -->
                            <span class="ti-topbar-separator" aria-hidden="true">|</span>

                            <!-- Bloco de status e metadados -->
                            <div class="ti-subtask-status-block">
                                <!-- Status da subtarefa -->
                                <span class="ti-subtask-status">
                                    {{ $activeSubtask['is_done'] ?? false ? 'Concluída' : 'Em andamento' }}
                                </span>

                                <!-- Grupo de metadados -->
                                <div class="ti-subtask-meta-group">
                                    <!-- Data de criação -->
                                    <div class="ti-subtask-meta">
                                        <span class="ti-subtask-meta-label">Data</span>
                                        <span class="ti-subtask-meta-value {{ $createdClass }}">
                                            {{ $createdLabel }}
                                        </span>
                                    </div>

                                    <!-- Prazo -->
                                    <div class="ti-subtask-meta">
                                        <span class="ti-subtask-meta-label">Prazo</span>
                                        <span class="ti-subtask-meta-value {{ $dueClass }}">
                                            {{ $dueLabel }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Flag de prioridade (se quiser manter no canto direito) -->
                            <button class="ti-flag {{ $activeSubtask['is_flagged'] ?? false ? 'is-active' : '' }}"
                                type="button" title="Marcar subtarefa"
                                aria-pressed="{{ $activeSubtask['is_flagged'] ?? false ? 'true' : 'false' }}"
                                wire:click="toggleFlag({{ $activeSubtask['id'] }})">
                                <i data-lucide="flag"></i>
                            </button>
                        </div>


                        <div class="ti-topbar-right">
                            <div class="ti-menu">
                                <button class="icon ghost" title="Mais opções">
                                    <i class="fa-solid fa-ellipsis" aria-hidden="true"></i>
                                </button>
                                <div class="ti-menu-dropdown" role="menu">
                                    @include('livewire.tasks.partials.menu-content', [
                                        'context' => 'details',
                                        'subtaskId' => $activeSubtask['id'] ?? null,
                                    ])
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="ti-topbar">
                        <div class="ti-topbar-left">
                            <button class="ti-check {{ ($mission['status'] ?? null) === 'done' ? 'is-active' : '' }}"
                                type="button" title="Concluir missão"
                                aria-pressed="{{ ($mission['status'] ?? null) === 'done' ? 'true' : 'false' }}"
                                wire:click="toggleCompletion">
                                <span class="ti-check-mark"></span>
                            </button>

                            <span class="ti-topbar-separator" aria-hidden="true">|</span>

                            <div class="ti-date-picker-container" wire:keydown.escape.window="closeDatePicker">
                                <div class="ti-date-actions">
                                    <button class="pill {{ $showDatePicker ?? false ? 'is-active' : '' }}"
                                        type="button" title="Adicionar data" wire:click="toggleDatePicker">
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
                                            <button class="nav" type="button" title="Mês anterior"
                                                wire:click="movePicker(-1)">
                                                <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
                                            </button>
                                            <span class="label">{{ $pickerCalendar['label'] ?? '' }}</span>
                                            <button class="nav" type="button" title="Próximo mês"
                                                wire:click="movePicker(1)">
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
                                                    <button class="day {{ $classAttr }}" type="button"
                                                        wire:key="calendar-day-{{ $weekIndex }}-{{ $dayIndex }}-{{ $day['date'] }}"
                                                        wire:click="selectDueDate('{{ $day['date'] }}')">
                                                        {{ $day['label'] ?? '' }}
                                                    </button>
                                                @endforeach
                                            @endforeach
                                        </div>

                                        <div class="ti-date-footer">
                                            @if ($pickerCalendar['hasSelected'] ?? false)
                                                <button class="link" type="button" wire:click="clearDueDate">Remover
                                                    data</button>
                                            @else
                                                <button class="link disabled" type="button" disabled>Sem data
                                                    definida</button>
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
                                    <span
                                        class="ti-priority-current">{{ $mission['priority_label'] ?? 'Nenhuma' }}</span>
                                </button>

                                <div class="ti-priority-menu" role="menu">
                                    <button class="ti-priority-option is-high" type="button" role="menuitem"
                                        wire:click="setPriority(3)">
                                        <span class="dot"></span> Alta
                                    </button>
                                    <button class="ti-priority-option is-medium" type="button" role="menuitem"
                                        wire:click="setPriority(2)">
                                        <span class="dot"></span> Média
                                    </button>
                                    <button class="ti-priority-option is-low" type="button" role="menuitem"
                                        wire:click="setPriority(1)">
                                        <span class="dot"></span> Baixa
                                    </button>
                                    <button class="ti-priority-option is-none" type="button" role="menuitem"
                                        wire:click="setPriority(0)">
                                        <span class="dot"></span> Nenhuma
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
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
                            <span class="ti-parent"
                                title="Tarefa pai">{{ $mission['parent_title'] ?? ($mission['title'] ?? '') }}</span>
                            <h1 class="ti-title" title="{{ $activeSubtask['title'] ?? '' }}">
                                {{ ($activeSubtask['title'] ?? '') !== '' ? $activeSubtask['title'] : 'Sem título' }}
                            </h1>
                        @elseif (!empty($mission['parent_title']))
                            <span class="ti-parent" title="Tarefa pai">{{ $mission['parent_title'] ?? '' }}</span>
                            <h1 class="ti-title" title="{{ $mission['title'] }}">
                                {{ $mission['title'] ?: 'Sem título' }}</h1>
                        @else
                            <h1 class="ti-title" title="{{ $mission['title'] }}">
                                {{ $mission['title'] ?: 'Sem título' }}</h1>
                        @endif
                    </div>
                    @if (!$isSubtask)
                        <div class="actions">
                            <button class="icon ghost" type="button" title="Editar descrição"
                                wire:click="startDescriptionEdit">
                                <i class="fa-solid fa-pen" aria-hidden="true"></i>
                            </button>
                            <div class="ti-menu">
                                <button class="icon ghost" title="Mais opções">
                                    <i class="fa-solid fa-ellipsis" aria-hidden="true"></i>
                                </button>
                                <div class="ti-menu-dropdown" role="menu">
                                    @include('livewire.tasks.partials.menu-content', [
                                        'context' => 'details',
                                    ])
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                @if ($isSubtask)
                    <div class="ti-description">
                        <div class="ti-description-content is-static">
                            <div class="ti-description-empty">
                                <p class="muted">Use subtarefas para quebrar esta tarefa em etapas menores.</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="ti-description">
                        <div class="ti-description-content {{ $isEditingDescription ? 'is-editing' : '' }}">
                            @if ($isEditingDescription)
                                <div class="ti-description-editor">
                                    <!-- Input hidden que guarda o valor -->
                                    <input type="hidden" id="descriptionDraft" wire:model.defer="descriptionDraft">

                                    <!-- Área do editor -->
                                    <div class="ti-description-editor-surface">
                                        <div id="quill-toolbar"></div>
                                        <div id="quill-container" style="min-height:150px;"></div>
                                    </div>

                                    <div class="ti-description-actions">
                                        <button type="button" class="ghost"
                                            wire:click="cancelDescriptionEdit">Cancelar</button>
                                        <button type="button" class="primary"
                                            wire:click="saveDescription">Salvar</button>
                                    </div>
                                </div>
                            @elseif (!empty($mission['description']))
                                <div class="ti-description-display" role="button" tabindex="0"
                                    wire:click="startDescriptionEdit"
                                    wire:keydown.enter.prevent="startDescriptionEdit"
                                    wire:keydown.space.prevent="startDescriptionEdit">
                                    <div class="ti-description-html">{!! $mission['description'] !!}</div>
                                </div>
                            @else
                                <div class="ti-description-empty" role="button" tabindex="0"
                                    wire:click="startDescriptionEdit"
                                    wire:keydown.enter.prevent="startDescriptionEdit"
                                    wire:keydown.space.prevent="startDescriptionEdit">
                                    <p class="muted">Adicione detalhes ou cole notas importantes aqui…</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Subtasks -->
                <section class="ti-subtasks">
                    @php
                        $activeNode = $mission['active_subtask'] ?? null;
                        $subtasks = $activeNode ? $activeNode['children'] ?? [] : $mission['subtasks'] ?? [];
                        $maxSubtasks = $maxSubtasks ?? \App\Livewire\Tasks\MainPanel::MAX_SUBTASKS;
                        $rootSubtaskCount = count($subtasks);
                        $canAddRootSubtask = $rootSubtaskCount < $maxSubtasks;
                    @endphp

                    @if ($rootSubtaskCount)
                        <ul
                            class="ti-subtask-list"
                            role="list"
                            data-subtask-container
                            data-mission-id="{{ $missionData['id'] ?? '' }}"
                            data-parent-id=""
                        >
                            @foreach ($subtasks as $st)
                                @include('livewire.tasks.partials.subtask-item', [
                                    'item' => $st,
                                    'depth' => 0,
                                    'selectedSubtaskId' => $selectedSubtaskId,
                                    'maxSubtasks' => $maxSubtasks,
                                    'missionId' => $missionData['id'] ?? null,
                                ])
                            @endforeach
                        </ul>
                    @else
                        <p class="muted" style="margin:8px 0 0;">Sem subtarefas</p>
                    @endif

                    @if ($showSubtaskForm && $canAddRootSubtask)
                        <form class="ti-subtask-form" wire:submit.prevent="saveSubtask">
                            @if ($newSubtaskParentLabel)
                                <div class="ti-subtask-context">Dentro de <span>{{ $newSubtaskParentLabel }}</span>
                                </div>
                            @endif
                            <input type="text" class="ti-subtask-input" placeholder="Título da subtarefa"
                                wire:model.defer="newSubtaskTitle" />
                            <div class="ti-subtask-form-actions">
                                <button type="button" class="ghost"
                                    wire:click="cancelSubtaskForm">Cancelar</button>
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

            </div>

            @if (!$isSubtask)
                <footer class="ti-footer">
                    <div @class(['ti-list-selector', 'is-open' => $showMoveListMenu]) wire:click.away="closeMoveListMenu">
                        <button class="ti-list-selector-toggle" type="button" title="Mover para outra lista"
                            wire:click="toggleMoveListMenu">
                            <i class="fa-solid fa-list" aria-hidden="true"></i>
                            <span>{{ $mission['list'] ?? 'Sem lista' }}</span>
                            <i class="fa-solid fa-chevron-down chevron" aria-hidden="true"></i>
                        </button>

                        @if ($showMoveListMenu)
                            <div class="ti-list-dropdown" role="menu">
                                <button type="button"
                                    class="ti-list-option {{ ($mission['list_id'] ?? null) === null ? 'is-active' : '' }}"
                                    wire:click.stop="moveToList(null)">
                                    <span>Sem lista</span>
                                </button>
                                @foreach ($availableLists as $listOption)
                                    <button type="button"
                                        class="ti-list-option {{ $listOption['is_current'] ? 'is-active' : '' }}"
                                        wire:click.stop="moveToList({{ $listOption['id'] }})">
                                        <span>{{ $listOption['name'] }}</span>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="ti-footer-actions">
                        <button class="ti-footer-icon" type="button" title="Iniciar Pomodoro"
                            wire:click="startPomodoro">
                            <i class="fa-solid fa-stopwatch" aria-hidden="true"></i>
                        </button>
                        <button class="ti-footer-icon {{ $mission['is_starred'] ?? false ? 'is-active' : '' }}"
                            type="button" title="Favoritar"
                            aria-pressed="{{ $mission['is_starred'] ?? false ? 'true' : 'false' }}"
                            wire:click="toggleStar">
                            <i class="fa-solid fa-star" aria-hidden="true"></i>
                        </button>
                    </div>
                </footer>
            @endif
        </div>
    @else
        <div class="ti-empty">
            <h3>Selecione uma tarefa</h3>
            <p>Escolha uma tarefa para ver detalhes e subtarefas.</p>
        </div>
    @endif
</aside>

@once
    @push('styles')
        <link rel="stylesheet" href="https://cdn.quilljs.com/1.3.7/quill.snow.css"
            integrity="sha384-jNvm9X3qoL1M23JycQexzDva8PU15ag8RSXGsAi7z74oQ1d0N+PMIvWn5a2CXiIc" crossorigin="anonymous" />
    @endpush

    @push('scripts')
        <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"
            integrity="sha384-O5zU9lFNX3ob18J7hsouKUT1zsVXni4eWh05rq6ArlTc95xJMu38xpv8uKXu95Pr" crossorigin="anonymous">
        </script>
    @endpush
@endonce
