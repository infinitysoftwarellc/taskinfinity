{{-- This Blade view renders the livewire tasks partials menu content interface. --}}
@php
    $context = $context ?? 'static';
    $isDetails = $context === 'details';
    $missionId = $missionId ?? null;
    $subtaskId = $subtaskId ?? null;
    $isSubtask = $subtaskId !== null;
    $customDateValue = $isDetails
        ? ($isSubtask ? ($dueDate ?? '') : ($menuDate ?? ''))
        : ($dueDate ?? '');
    $inlineSubtaskArg = ($isSubtask && $missionId && $subtaskId !== null)
        ? ', ' . $subtaskId
        : '';
    $currentPriority = is_numeric($priority ?? null) ? (int) $priority : 0;
    $isStarred = (bool) ($isStarred ?? false);
@endphp

<div class="ti-floating-menu" role="none">
    <div class="ti-menu-section" role="group" aria-label="Data">
        <span class="ti-menu-section-label">Data</span>
        <div class="ti-menu-icon-grid">
            <button
                class="ti-menu-icon"
                type="button"
                data-menu-item
                title="Hoje"
                @if ($isDetails && $isSubtask)
                    wire:click="applySubtaskShortcut({{ $subtaskId }}, 'today')"
                @elseif ($isDetails)
                    wire:click="applyDueShortcut('today')"
                @elseif ($isSubtask && $missionId)
                    wire:click="runInlineAction({{ $missionId }}, 'due-shortcut', 'today'{{ $inlineSubtaskArg }})"
                @elseif ($missionId)
                    wire:click="runInlineAction({{ $missionId }}, 'due-shortcut', 'today')"
                @endif
            >
                <i class="fa-solid fa-sun" aria-hidden="true"></i>
                <span class="sr-only">Hoje</span>
            </button>
            <button
                class="ti-menu-icon"
                type="button"
                data-menu-item
                title="Amanhã"
                @if ($isDetails && $isSubtask)
                    wire:click="applySubtaskShortcut({{ $subtaskId }}, 'tomorrow')"
                @elseif ($isDetails)
                    wire:click="applyDueShortcut('tomorrow')"
                @elseif ($isSubtask && $missionId)
                    wire:click="runInlineAction({{ $missionId }}, 'due-shortcut', 'tomorrow'{{ $inlineSubtaskArg }})"
                @elseif ($missionId)
                    wire:click="runInlineAction({{ $missionId }}, 'due-shortcut', 'tomorrow')"
                @endif
            >
                <i class="fa-solid fa-cloud-sun" aria-hidden="true"></i>
                <span class="sr-only">Amanhã</span>
            </button>
            <button
                class="ti-menu-icon"
                type="button"
                data-menu-item
                title="Próximos 7 dias"
                @if ($isDetails && $isSubtask)
                    wire:click="applySubtaskShortcut({{ $subtaskId }}, 'next7')"
                @elseif ($isDetails)
                    wire:click="applyDueShortcut('next7')"
                @elseif ($isSubtask && $missionId)
                    wire:click="runInlineAction({{ $missionId }}, 'due-shortcut', 'next7'{{ $inlineSubtaskArg }})"
                @elseif ($missionId)
                    wire:click="runInlineAction({{ $missionId }}, 'due-shortcut', 'next7')"
                @endif
            >
                <i class="fa-solid fa-calendar-week" aria-hidden="true"></i>
                <span class="sr-only">Próximos 7 dias</span>
            </button>
            <label class="ti-menu-icon is-picker" title="Data personalizada">
                <i class="fa-solid fa-calendar-day" aria-hidden="true"></i>
                <span class="sr-only">Data personalizada</span>
                <input
                    class="ti-menu-date-input"
                    type="date"
                    aria-label="Escolher data personalizada"
                    value="{{ $customDateValue }}"
                    @if ($isDetails && $isSubtask)
                        wire:change="selectSubtaskDueDate({{ $subtaskId }}, $event.target.value)"
                    @elseif ($isDetails)
                        wire:model.live="menuDate"
                        wire:change="applyMenuDate"
                    @elseif ($isSubtask && $missionId)
                        wire:change="runInlineAction({{ $missionId }}, 'set-date', $event.target.value{{ $inlineSubtaskArg }})"
                    @elseif ($missionId)
                        wire:change="runInlineAction({{ $missionId }}, 'set-date', $event.target.value)"
                    @endif
                >
            </label>
            <button
                class="ti-menu-icon"
                type="button"
                data-menu-item
                title="Remover data"
                @if ($isDetails && $isSubtask)
                    wire:click="applySubtaskShortcut({{ $subtaskId }}, 'clear')"
                @elseif ($isDetails)
                    wire:click="applyDueShortcut('clear')"
                @elseif ($isSubtask && $missionId)
                    wire:click="runInlineAction({{ $missionId }}, 'due-shortcut', 'clear'{{ $inlineSubtaskArg }})"
                @elseif ($missionId)
                    wire:click="runInlineAction({{ $missionId }}, 'due-shortcut', 'clear')"
                @endif
            >
                <i class="fa-solid fa-calendar-xmark" aria-hidden="true"></i>
                <span class="sr-only">Remover data</span>
            </button>
        </div>
    </div>

    @if (! $isSubtask)
        <div class="ti-menu-section" role="group" aria-label="Prioridade">
            <span class="ti-menu-section-label">Prioridade</span>
            <div class="ti-menu-flag-list">
                <button
                    @class(['ti-menu-flag', 'is-high', 'is-active' => $currentPriority === 3])
                    type="button"
                    data-menu-item
                    title="Prioridade alta"
                    aria-pressed="{{ $currentPriority === 3 ? 'true' : 'false' }}"
                    @if ($isDetails)
                        wire:click="setPriority(3)"
                    @elseif ($missionId)
                        wire:click="runInlineAction({{ $missionId }}, 'set-priority', 3)"
                    @endif
                >
                    <i class="fa-solid fa-flag" aria-hidden="true"></i>
                    <span class="sr-only">Alta</span>
                </button>
                <button
                    @class(['ti-menu-flag', 'is-medium', 'is-active' => $currentPriority === 2])
                    type="button"
                    data-menu-item
                    title="Prioridade média"
                    aria-pressed="{{ $currentPriority === 2 ? 'true' : 'false' }}"
                    @if ($isDetails)
                        wire:click="setPriority(2)"
                    @elseif ($missionId)
                        wire:click="runInlineAction({{ $missionId }}, 'set-priority', 2)"
                    @endif
                >
                    <i class="fa-solid fa-flag" aria-hidden="true"></i>
                    <span class="sr-only">Média</span>
                </button>
                <button
                    @class(['ti-menu-flag', 'is-low', 'is-active' => $currentPriority === 1])
                    type="button"
                    data-menu-item
                    title="Prioridade baixa"
                    aria-pressed="{{ $currentPriority === 1 ? 'true' : 'false' }}"
                    @if ($isDetails)
                        wire:click="setPriority(1)"
                    @elseif ($missionId)
                        wire:click="runInlineAction({{ $missionId }}, 'set-priority', 1)"
                    @endif
                >
                    <i class="fa-solid fa-flag" aria-hidden="true"></i>
                    <span class="sr-only">Baixa</span>
                </button>
                <button
                    @class(['ti-menu-flag', 'is-none', 'is-active' => $currentPriority === 0])
                    type="button"
                    data-menu-item
                    title="Sem prioridade"
                    aria-pressed="{{ $currentPriority === 0 ? 'true' : 'false' }}"
                    @if ($isDetails)
                        wire:click="setPriority(0)"
                    @elseif ($missionId)
                        wire:click="runInlineAction({{ $missionId }}, 'set-priority', 0)"
                    @endif
                >
                    <i class="fa-solid fa-flag" aria-hidden="true"></i>
                    <span class="sr-only">Nenhuma</span>
                </button>
            </div>
        </div>
    @endif

    @if ($isSubtask)
        <div class="ti-menu-section" role="group" aria-label="Subtarefa">
            <div class="ti-menu-actions">
                <button
                    class="ti-menu-action"
                    type="button"
                    data-menu-item
                    @if ($isDetails)
                        wire:click="openSubtaskForm({{ $subtaskId }})"
                    @elseif ($missionId)
                        wire:click="createChildSubtask({{ $subtaskId }})"
                    @endif
                >
                    <i class="fa-solid fa-diagram-project" aria-hidden="true"></i>
                    <span>Criar subtarefa filha</span>
                </button>
                <button
                    class="ti-menu-action danger"
                    type="button"
                    data-menu-item
                    @if ($isDetails)
                        wire:click="deleteSubtask({{ $subtaskId }})"
                    @elseif ($missionId)
                        wire:click="deleteSubtask({{ $missionId }}, {{ $subtaskId }})"
                    @endif
                >
                    <i class="fa-solid fa-trash" aria-hidden="true"></i>
                    <span>Excluir subtarefa</span>
                </button>
            </div>
        </div>
    @else
        <div class="ti-menu-section" role="group" aria-label="Ações">
            <div class="ti-menu-actions">
                <button
                    class="ti-menu-action"
                    type="button"
                    data-menu-item
                    @if ($isDetails)
                        wire:click="openSubtaskForm"
                    @elseif ($missionId)
                        wire:click="runInlineAction({{ $missionId }}, 'create-subtask')"
                    @endif
                >
                    <i class="fa-solid fa-square-plus" aria-hidden="true"></i>
                    <span>Adicionar subtarefa</span>
                </button>
                <button
                    class="ti-menu-action"
                    type="button"
                    data-menu-item
                    @if ($isDetails)
                        wire:click="toggleStar"
                    @elseif ($missionId)
                        wire:click="runInlineAction({{ $missionId }}, 'toggle-star')"
                    @endif
                >
                    <i class="fa-solid fa-thumbtack" aria-hidden="true"></i>
                    <span>{{ $isStarred ? 'Desafixar' : 'Fixar' }}</span>
                </button>
                <button
                    class="ti-menu-action"
                    type="button"
                    data-menu-item
                    @if ($isDetails)
                        wire:click="toggleMoveListMenu"
                    @elseif ($missionId)
                        wire:click="runInlineAction({{ $missionId }}, 'move-list')"
                    @endif
                >
                    <i class="fa-solid fa-right-left" aria-hidden="true"></i>
                    <span>Mover para outra lista</span>
                </button>
                <button
                    class="ti-menu-action"
                    type="button"
                    data-menu-item
                    @if ($isDetails)
                        wire:click="startPomodoro"
                    @elseif ($missionId)
                        wire:click="runInlineAction({{ $missionId }}, 'start-pomodoro')"
                    @endif
                >
                    <i class="fa-solid fa-stopwatch" aria-hidden="true"></i>
                    <span>Iniciar Pomodoro</span>
                </button>
                <button
                    class="ti-menu-action"
                    type="button"
                    data-menu-item
                    @if ($isDetails)
                        wire:click="duplicateMission"
                    @elseif ($missionId)
                        wire:click="runInlineAction({{ $missionId }}, 'duplicate')"
                    @endif
                >
                    <i class="fa-solid fa-copy" aria-hidden="true"></i>
                    <span>Duplicar</span>
                </button>
                <button
                    class="ti-menu-action danger"
                    type="button"
                    data-menu-item
                    @if ($isDetails)
                        wire:click="deleteMission"
                    @elseif ($missionId)
                        wire:click="runInlineAction({{ $missionId }}, 'delete')"
                    @endif
                >
                    <i class="fa-solid fa-trash" aria-hidden="true"></i>
                    <span>Excluir</span>
                </button>
            </div>
        </div>
    @endif
</div>
