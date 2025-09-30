@php
    $context = $context ?? 'static';
    $isDetails = $context === 'details';
    $missionId = $missionId ?? null;
    $subtaskId = $subtaskId ?? null;
    $isSubtask = $subtaskId !== null;
    $customDateValue = $isDetails ? ($menuDate ?? '') : ($dueDate ?? '');
@endphp

<div class="ti-floating-menu" role="none">
    <div class="ti-menu-section" role="group" aria-label="Data">
        <span class="ti-menu-section-label">Data</span>
        <div class="ti-menu-icon-grid">
            <button
                class="ti-menu-icon"
                type="button"
                data-menu-item
                @if ($isDetails)
                    wire:click="applyDueShortcut('today')"
                @elseif ($missionId)
                    wire:click="runInlineAction({{ $missionId }}, 'due-shortcut', 'today')"
                @endif
            >
                <i class="fa-solid fa-sun" aria-hidden="true"></i>
                <span>Hoje</span>
            </button>
            <button
                class="ti-menu-icon"
                type="button"
                data-menu-item
                @if ($isDetails)
                    wire:click="applyDueShortcut('tomorrow')"
                @elseif ($missionId)
                    wire:click="runInlineAction({{ $missionId }}, 'due-shortcut', 'tomorrow')"
                @endif
            >
                <i class="fa-solid fa-cloud-sun" aria-hidden="true"></i>
                <span>Amanhã</span>
            </button>
            <button
                class="ti-menu-icon"
                type="button"
                data-menu-item
                @if ($isDetails)
                    wire:click="applyDueShortcut('next7')"
                @elseif ($missionId)
                    wire:click="runInlineAction({{ $missionId }}, 'due-shortcut', 'next7')"
                @endif
            >
                <i class="fa-solid fa-calendar-week" aria-hidden="true"></i>
                <span>7 dias</span>
            </button>
            <label class="ti-menu-icon is-picker">
                <i class="fa-solid fa-calendar-day" aria-hidden="true"></i>
                <span>Customizar</span>
                <input
                    class="ti-menu-date-input"
                    type="date"
                    aria-label="Escolher data personalizada"
                    value="{{ $customDateValue }}"
                    @if ($isDetails)
                        wire:model.live="menuDate"
                        wire:change="applyMenuDate"
                    @elseif ($missionId)
                        wire:change="runInlineAction({{ $missionId }}, 'set-date', $event.target.value)"
                    @endif
                >
            </label>
            <button
                class="ti-menu-icon"
                type="button"
                data-menu-item
                @if ($isDetails)
                    wire:click="applyDueShortcut('clear')"
                @elseif ($missionId)
                    wire:click="runInlineAction({{ $missionId }}, 'due-shortcut', 'clear')"
                @endif
            >
                <i class="fa-solid fa-calendar-xmark" aria-hidden="true"></i>
                <span>Remover</span>
            </button>
        </div>
    </div>

    <div class="ti-menu-section" role="group" aria-label="Prioridade">
        <span class="ti-menu-section-label">Prioridade</span>
        <div class="ti-menu-flag-list">
            <button
                class="ti-menu-flag is-high"
                type="button"
                data-menu-item
                @if ($isDetails)
                    wire:click="setPriority(3)"
                @elseif ($missionId)
                    wire:click="runInlineAction({{ $missionId }}, 'set-priority', 3)"
                @endif
            >
                <i class="fa-solid fa-flag" aria-hidden="true"></i>
                <span>Alta</span>
            </button>
            <button
                class="ti-menu-flag is-medium"
                type="button"
                data-menu-item
                @if ($isDetails)
                    wire:click="setPriority(2)"
                @elseif ($missionId)
                    wire:click="runInlineAction({{ $missionId }}, 'set-priority', 2)"
                @endif
            >
                <i class="fa-solid fa-flag" aria-hidden="true"></i>
                <span>Média</span>
            </button>
            <button
                class="ti-menu-flag is-low"
                type="button"
                data-menu-item
                @if ($isDetails)
                    wire:click="setPriority(1)"
                @elseif ($missionId)
                    wire:click="runInlineAction({{ $missionId }}, 'set-priority', 1)"
                @endif
            >
                <i class="fa-solid fa-flag" aria-hidden="true"></i>
                <span>Baixa</span>
            </button>
            <button
                class="ti-menu-flag is-none"
                type="button"
                data-menu-item
                @if ($isDetails)
                    wire:click="setPriority(0)"
                @elseif ($missionId)
                    wire:click="runInlineAction({{ $missionId }}, 'set-priority', 0)"
                @endif
            >
                <i class="fa-solid fa-flag" aria-hidden="true"></i>
                <span>Nenhuma</span>
            </button>
        </div>
    </div>

    <div class="ti-menu-section" role="group" aria-label="Ações">
        <div class="ti-menu-actions">
            <button
                class="ti-menu-action"
                type="button"
                data-menu-item
                @if ($isSubtask && $isDetails)
                    wire:click="openSubtaskForm({{ $subtaskId }})"
                @elseif ($isSubtask && $missionId)
                    wire:click="createChildSubtask({{ $subtaskId }})"
                @elseif ($isDetails)
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
                <span>Fixar</span>
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
                @if ($isSubtask && $isDetails)
                    wire:click="deleteSubtask({{ $subtaskId }})"
                @elseif ($isSubtask && $missionId)
                    wire:click="deleteSubtask({{ $missionId }}, {{ $subtaskId }})"
                @elseif ($isDetails)
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
</div>
