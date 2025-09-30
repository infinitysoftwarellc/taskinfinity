<?php

namespace App\Livewire\Tasks;

use App\Models\Checkpoint;
use App\Models\Mission;
use App\Models\PomodoroSession;
use App\Models\TaskList;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Componente que controla o painel de detalhes da página de tarefas.
 * Responsável por carregar uma missão, gerenciar subtarefas e ações rápidas.
 */
class Details extends Component
{
    public const MAX_SUBTASKS = MainPanel::MAX_SUBTASKS;

    /**
     * Identificador da missão atualmente carregada.
     */
    public ?int $missionId = null;

    /**
     * Dados estruturados da missão apresentada no painel.
     */
    public ?array $mission = null;

    /**
     * Tags associadas à missão carregada.
     */
    public array $missionTags = [];

    /**
     * Controle de exibição do seletor de data principal.
     */
    public bool $showDatePicker = false;

    /**
     * Controle de exibição do seletor de data para subtarefas.
     */
    public bool $showSubtaskDatePicker = false;

    /**
     * Data usada como cursor no calendário do seletor.
     */
    public ?string $pickerCursorDate = null;

    /**
     * Data usada como cursor no calendário de subtarefas.
     */
    public ?string $subtaskPickerCursorDate = null;

    /**
     * Data atualmente selecionada pelo usuário no calendário.
     */
    public ?string $pickerSelectedDate = null;

    /**
     * Data atualmente selecionada no calendário de subtarefas.
     */
    public ?string $subtaskPickerSelectedDate = null;

    /**
     * Data escolhida através do menu contextual de datas.
     */
    public ?string $menuDate = null;

    /**
     * Controla a abertura do menu de mover missão para outra lista.
     */
    public bool $showMoveListMenu = false;

    /**
     * Listas disponíveis para mover a missão.
     */
    public array $availableLists = [];

    /**
     * Controla a abertura do formulário de nova subtarefa.
     */
    public bool $showSubtaskForm = false;

    /**
     * Título digitado ao criar uma nova subtarefa.
     */
    public string $newSubtaskTitle = '';

    /**
     * Identificador do possível pai da subtarefa em criação.
     */
    public ?int $newSubtaskParentId = null;

    /**
     * Rótulo exibido para o pai da subtarefa em criação.
     */
    public ?string $newSubtaskParentLabel = null;

    /**
     * Subtarefa atualmente selecionada na árvore.
     */
    public ?int $selectedSubtaskId = null;

    /**
     * Carrega missão e subtarefas quando o usuário seleciona um item na lista.
     */
    #[On('task-selected')]
    public function loadMission(?int $missionId = null, ?int $checkpointId = null): void
    {
        if (! $missionId) {
            $this->missionId = null;
            $this->mission = null;
            $this->missionTags = [];
            $this->availableLists = [];
            $this->showMoveListMenu = false;
            $this->showSubtaskForm = false;
            $this->newSubtaskTitle = '';
            $this->newSubtaskParentId = null;
            $this->newSubtaskParentLabel = null;
            $this->menuDate = null;
            $this->showDatePicker = false;
            $this->showSubtaskDatePicker = false;
            $this->pickerCursorDate = null;
            $this->pickerSelectedDate = null;
            $this->subtaskPickerCursorDate = null;
            $this->subtaskPickerSelectedDate = null;
            $this->selectedSubtaskId = null;

            return;
        }

        $user = Auth::user();

        if (! $user) {
            return;
        }

        $mission = Mission::query()
            ->with('list')
            ->with(['checkpoints' => fn ($query) => $query->orderBy('position')->orderBy('created_at')])
            ->withCount([
                'checkpoints',
                'checkpoints as checkpoints_done_count' => fn ($query) => $query->where('is_done', true),
                'attachments',
            ])
            ->where('user_id', $user->id)
            ->find($missionId);

        if (! $mission) {
            $this->missionId = null;
            $this->mission = null;
            $this->missionTags = [];

            return;
        }

        $timezone = $user->timezone ?? config('app.timezone');

        $this->missionId = $mission->id;
        $checkpoints = collect($mission->checkpoints ?? []);

        $subtasks = $this->buildCheckpointTree($checkpoints, $timezone);

        $this->mission = [
            'id' => $mission->id,
            'title' => $mission->title,
            'status' => $mission->status,
            'list' => $mission->list?->name,
            'list_id' => $mission->list_id,
            'parent_title' => null,
            'created_at' => $mission->created_at?->copy()->setTimezone($timezone),
            'updated_at' => $mission->updated_at?->copy()->setTimezone($timezone),
            'due_at' => $mission->due_at?->copy()->setTimezone($timezone),
            'priority' => $mission->priority,
            'priority_label' => $this->priorityLabel($mission->priority),
            'is_starred' => (bool) $mission->is_starred,
            'xp_reward' => $mission->xp_reward,
            'checkpoints_total' => $mission->checkpoints_count ?? 0,
            'checkpoints_done' => $mission->checkpoints_done_count ?? 0,
            'attachments_count' => $mission->attachments_count ?? 0,
            'subtasks' => $subtasks,
            'active_subtask' => null,
            'active_subtask_path' => [],
        ];

        $labels = $mission->labels_json ?? [];
        if (is_array($labels)) {
            $this->missionTags = $labels;
        } else {
            $this->missionTags = [];
        }

        $this->pickerSelectedDate = $mission->due_at?->copy()->setTimezone($timezone)?->format('Y-m-d');
        $this->pickerCursorDate = $this->pickerSelectedDate
            ? CarbonImmutable::createFromFormat('Y-m-d', $this->pickerSelectedDate, $timezone)->startOfMonth()->format('Y-m-d')
            : CarbonImmutable::now($timezone)->startOfMonth()->format('Y-m-d');
        $this->showDatePicker = false;
        $this->showSubtaskDatePicker = false;
        $this->menuDate = $this->pickerSelectedDate;
        $this->showMoveListMenu = false;
        $this->showSubtaskForm = false;
        $this->newSubtaskTitle = '';
        $this->newSubtaskParentId = null;
        $this->newSubtaskParentLabel = null;
        $this->subtaskPickerSelectedDate = null;
        $this->subtaskPickerCursorDate = null;
        $this->applyActiveSubtaskContext($checkpointId);

        $this->availableLists = TaskList::query()
            ->where('user_id', $user->id)
            ->whereNull('archived_at')
            ->orderByDesc('is_pinned')
            ->orderBy('position')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($list) => [
                'id' => $list->id,
                'name' => $list->name,
                'is_current' => $list->id === $mission->list_id,
            ])
            ->values()
            ->toArray();
    }

    #[On('tasks-inline-action')]
    /**
     * Trata ações rápidas vindas do painel principal (atalhos de datas, prioridade etc.).
     */
    public function handleInlineAction(?int $missionId = null, string $action = '', ?string $value = null, ?int $checkpointId = null): void
    {
        if (! $missionId) {
            return;
        }

        $shouldReload = $this->missionId !== $missionId
            || ($checkpointId !== null && $this->selectedSubtaskId !== $checkpointId);

        if ($shouldReload) {
            $this->loadMission($missionId, $checkpointId);
        }

        if ($this->missionId !== $missionId) {
            return;
        }

        if ($checkpointId !== null) {
            switch ($action) {
                case 'due-shortcut':
                    if ($value !== null) {
                        $this->applySubtaskShortcut($checkpointId, $value);
                    }

                    break;
                case 'set-date':
                    if ($value) {
                        $this->selectSubtaskDueDate($checkpointId, $value);
                    }

                    break;
            }

            return;
        }

        switch ($action) {
            case 'due-shortcut':
                if ($value !== null) {
                    $this->applyDueShortcut($value);
                }

                break;
            case 'set-date':
                if ($value) {
                    $this->selectDueDate($value);
                }

                break;
            case 'clear-date':
                $this->clearDueDate();

                break;
            case 'set-priority':
                $priority = is_numeric($value) ? (int) $value : 0;
                $this->setPriority($priority);

                break;
            case 'toggle-star':
                $this->toggleStar();

                break;
            case 'move-list':
                $this->showMoveListMenu = true;

                break;
            case 'start-pomodoro':
                $this->startPomodoro();

                break;
            case 'duplicate':
                $this->duplicateMission();

                break;
            case 'delete':
                $this->deleteMission();

                break;
            case 'create-subtask':
                $this->openSubtaskForm();

                break;
        }
    }

    #[On('tasks-updated')]
    /**
     * Recarrega os dados da missão mantendo a seleção atual.
     */
    public function refreshMission(): void
    {
        if ($this->missionId) {
            $activeSubtask = $this->selectedSubtaskId;
            $this->loadMission($this->missionId, $activeSubtask);
        }
    }

    /**
     * Seleciona uma subtarefa específica dentro da árvore.
     */
    public function selectCheckpoint(int $checkpointId): void
    {
        if (! $this->missionId) {
            return;
        }

        $exists = Checkpoint::query()
            ->where('id', $checkpointId)
            ->where('mission_id', $this->missionId)
            ->whereHas('mission', fn ($query) => $query->where('user_id', Auth::id()))
            ->exists();

        if (! $exists) {
            return;
        }

        if (! empty($this->mission)) {
            $this->applyActiveSubtaskContext($checkpointId);
        } else {
            $this->selectedSubtaskId = $checkpointId;
        }
    }

    /**
     * Alterna o estado de conclusão de uma subtarefa.
     */
    public function toggleCheckpoint(int $checkpointId): void
    {
        if (! $this->missionId) {
            return;
        }

        $checkpoint = Checkpoint::query()
            ->where('id', $checkpointId)
            ->whereHas('mission', fn ($query) => $query
                ->where('id', $this->missionId)
                ->where('user_id', Auth::id()))
            ->first();

        if (! $checkpoint) {
            return;
        }

        $checkpoint->is_done = ! $checkpoint->is_done;
        $checkpoint->save();

        $desiredSubtask = $checkpoint->id;

        $this->loadMission($this->missionId, $desiredSubtask);

        $this->dispatch('tasks-updated');
    }

    /**
     * Reordena subtarefas após drag and drop no painel de detalhes.
     */
    public function reorderSubtasks(int $missionId, array $data): void
    {
        $user = Auth::user();

        if (! $user || ! $this->missionId || $missionId !== $this->missionId) {
            return;
        }

        $mission = Mission::query()
            ->where('user_id', $user->id)
            ->find($missionId);

        if (! $mission) {
            return;
        }

        $movedId = isset($data['moved_id']) ? (int) $data['moved_id'] : null;
        $toParentId = $this->normalizeParentId($data['to_parent_id'] ?? null);
        $fromParentId = $this->normalizeParentId($data['from_parent_id'] ?? null);

        $toOrder = collect($data['to_order'] ?? [])
            ->map(fn ($item) => isset($item['id']) ? (int) $item['id'] : null)
            ->filter()
            ->values();

        $fromOrder = collect($data['from_order'] ?? [])
            ->map(fn ($item) => isset($item['id']) ? (int) $item['id'] : null)
            ->filter()
            ->values();

        $parentColumn = $this->checkpointParentColumn();

        DB::transaction(function () use ($missionId, $movedId, $toParentId, $fromParentId, $toOrder, $fromOrder, $parentColumn) {
            if ($movedId && $parentColumn && $toParentId !== $fromParentId) {
                $query = Checkpoint::query()
                    ->where('mission_id', $missionId)
                    ->where('id', $movedId);

                if ($toParentId === null) {
                    $query->update([$parentColumn => null]);
                } else {
                    $query->update([$parentColumn => $toParentId]);
                }
            }

            $this->syncCheckpointOrder($missionId, $toParentId, $toOrder);

            if ($fromParentId !== $toParentId) {
                $this->syncCheckpointOrder($missionId, $fromParentId, $fromOrder);
            }
        });

        $this->loadMission($missionId, $this->selectedSubtaskId);

        $this->dispatch('tasks-updated');
    }

    /**
     * Remove uma subtarefa específica da missão em foco.
     */
    public function deleteSubtask(int $checkpointId): void
    {
        if (! $this->missionId) {
            return;
        }

        $checkpoint = Checkpoint::query()
            ->where('id', $checkpointId)
            ->whereHas('mission', fn ($query) => $query
                ->where('id', $this->missionId)
                ->where('user_id', Auth::id()))
            ->first();

        if (! $checkpoint) {
            return;
        }

        $parentColumn = $this->checkpointParentColumn();
        $parentId = $parentColumn ? ($checkpoint->{$parentColumn} ?? null) : null;

        $checkpoint->delete();

        if ($this->selectedSubtaskId === $checkpointId) {
            $this->selectedSubtaskId = $parentId;
        }

        $this->dispatch('tasks-updated');
    }

    /**
     * Renderiza a view com os dados da missão selecionada.
     */
    public function render()
    {
        return view('livewire.tasks.details', [
            'mission' => $this->mission,
            'missionTags' => $this->missionTags,
            'pickerCalendar' => $this->mission ? $this->buildCalendar() : null,
            'subtaskCalendar' => $this->mission && ($this->mission['active_subtask'] ?? null)
                ? $this->buildSubtaskCalendar()
                : null,
            'selectedSubtaskId' => $this->selectedSubtaskId,
            'maxSubtasks' => self::MAX_SUBTASKS,
        ]);
    }

    /**
     * Retorna o texto amigável para prioridade da missão.
     */
    private function priorityLabel(?int $priority): string
    {
        return match ($priority) {
            3 => 'Alta',
            2 => 'Média',
            1 => 'Baixa',
            default => 'Nenhuma',
        };
    }

    /**
     * Abre ou fecha o calendário principal de data de entrega.
     */
    public function toggleDatePicker(): void
    {
        if (! $this->missionId) {
            return;
        }

        $this->showDatePicker = ! $this->showDatePicker;

        if ($this->showDatePicker) {
            $this->resolveCursor($this->userTimezone());
        }
    }

    /**
     * Fecha o calendário de data de entrega.
     */
    public function closeDatePicker(): void
    {
        $this->showDatePicker = false;
    }

    /**
     * Alterna o seletor de data específico das subtarefas.
     */
    public function toggleSubtaskDatePicker(): void
    {
        if (! $this->missionId || ! $this->selectedSubtaskId) {
            return;
        }

        $this->showSubtaskDatePicker = ! $this->showSubtaskDatePicker;

        if ($this->showSubtaskDatePicker) {
            $this->resolveSubtaskCursor($this->userTimezone());
        }
    }

    /**
     * Fecha o seletor de data das subtarefas.
     */
    public function closeSubtaskDatePicker(): void
    {
        $this->showSubtaskDatePicker = false;
    }

    /**
     * Fecha todos os seletores de data abertos no painel.
     */
    public function closeAllDatePickers(): void
    {
        $this->closeDatePicker();
        $this->closeSubtaskDatePicker();
    }

    /**
     * Alterna o status de conclusão da missão em destaque.
     */
    public function toggleCompletion(): void
    {
        if (! $this->missionId) {
            return;
        }

        $mission = Mission::query()
            ->where('user_id', Auth::id())
            ->find($this->missionId);

        if (! $mission) {
            return;
        }

        $mission->status = $mission->status === 'done' ? 'active' : 'done';
        $mission->save();

        $this->loadMission($mission->id);
        $this->dispatch('tasks-updated');
    }

    /**
     * Atualiza a prioridade da missão.
     */
    public function setPriority(?int $priority): void
    {
        if (! $this->missionId) {
            return;
        }

        $value = in_array($priority, [1, 2, 3], true) ? $priority : 0;

        $mission = Mission::query()
            ->where('user_id', Auth::id())
            ->find($this->missionId);

        if (! $mission) {
            return;
        }

        $mission->priority = $value;
        $mission->save();

        $this->loadMission($mission->id);
        $this->dispatch('tasks-updated');
    }

    /**
     * Aplica um atalho rápido de data para a missão.
     */
    public function applyDueShortcut(string $shortcut): void
    {
        if (! $this->missionId) {
            return;
        }

        $timezone = $this->userTimezone();
        $today = CarbonImmutable::now($timezone)->startOfDay();

        if ($this->selectedSubtaskId) {
            $this->applySubtaskShortcut($this->selectedSubtaskId, $shortcut);

            return;
        }

        $target = match ($shortcut) {
            'today' => $today,
            'tomorrow' => $today->addDay(),
            'next7' => $today->addDays(7),
            'clear' => null,
            default => null,
        };

        if ($target === null) {
            $this->clearDueDate();

            return;
        }

        $this->selectDueDate($target->format('Y-m-d'));
    }

    /**
     * Salva a data escolhida no menu contextual do calendário.
     */
    public function applyMenuDate(): void
    {
        if (! $this->missionId || ! $this->menuDate) {
            return;
        }

        if ($this->selectedSubtaskId) {
            $this->selectSubtaskDueDate($this->selectedSubtaskId, $this->menuDate);

            return;
        }

        $this->selectDueDate($this->menuDate);
    }

    /**
     * Navega entre meses dentro do seletor de datas.
     */
    public function movePicker(int $offset): void
    {
        if (! $this->missionId) {
            return;
        }

        $timezone = $this->userTimezone();
        $cursor = $this->resolveCursor($timezone)->addMonths($offset);
        $this->pickerCursorDate = $cursor->startOfMonth()->format('Y-m-d');
    }

    /**
     * Navega entre meses dentro do seletor de datas de subtarefas.
     */
    public function moveSubtaskPicker(int $offset): void
    {
        if (! $this->missionId || ! $this->selectedSubtaskId) {
            return;
        }

        $timezone = $this->userTimezone();
        $cursor = $this->resolveSubtaskCursor($timezone)->addMonths($offset);
        $this->subtaskPickerCursorDate = $cursor->startOfMonth()->format('Y-m-d');
    }

    /**
     * Seleciona manualmente uma data de vencimento.
     */
    public function selectDueDate(?string $date): void
    {
        if (! $this->missionId || ! $date) {
            return;
        }

        $timezone = $this->userTimezone();

        try {
            $selectedLocal = CarbonImmutable::createFromFormat('Y-m-d', $date, $timezone);
        } catch (\Throwable) {
            return;
        }

        $mission = Mission::query()
            ->where('user_id', Auth::id())
            ->find($this->missionId);

        if (! $mission) {
            return;
        }

        $mission->due_at = $selectedLocal->setTimezone(config('app.timezone'));
        $mission->save();

        $this->pickerSelectedDate = $selectedLocal->format('Y-m-d');
        $this->pickerCursorDate = $selectedLocal->startOfMonth()->format('Y-m-d');
        $this->menuDate = $this->pickerSelectedDate;
        $this->closeDatePicker();

        $this->loadMission($mission->id);
        $this->dispatch('tasks-updated');
    }

    /**
     * Remove qualquer data de vencimento aplicada à missão.
     */
    public function clearDueDate(): void
    {
        if (! $this->missionId) {
            return;
        }

        $mission = Mission::query()
            ->where('user_id', Auth::id())
            ->find($this->missionId);

        if (! $mission) {
            return;
        }

        $mission->due_at = null;
        $mission->save();

        $timezone = $this->userTimezone();
        $this->pickerSelectedDate = null;
        $this->pickerCursorDate = CarbonImmutable::now($timezone)->startOfMonth()->format('Y-m-d');
        $this->menuDate = null;

        $this->closeDatePicker();

        $this->loadMission($mission->id);
        $this->dispatch('tasks-updated');
    }

    /**
     * Limpa a data de entrega de uma subtarefa específica.
     */
    public function clearSubtaskDueDate(int $checkpointId): void
    {
        if (! $this->missionId) {
            return;
        }

        $checkpoint = $this->resolveCheckpoint($checkpointId);

        if (! $checkpoint) {
            return;
        }

        if (! $this->updateCheckpointDueDate($checkpoint, null)) {
            return;
        }

        $this->menuDate = null;
        $timezone = $this->userTimezone();
        $this->subtaskPickerSelectedDate = null;
        $this->subtaskPickerCursorDate = CarbonImmutable::now($timezone)->startOfMonth()->format('Y-m-d');
        $this->showSubtaskDatePicker = false;

        $this->loadMission($this->missionId, $checkpointId);
        $this->dispatch('tasks-updated');
    }

    /**
     * Define uma nova data de entrega para a subtarefa escolhida.
     */
    public function selectSubtaskDueDate(int $checkpointId, string $date): void
    {
        if (! $this->missionId || $date === '') {
            return;
        }

        $timezone = $this->userTimezone();

        try {
            $selectedLocal = CarbonImmutable::createFromFormat('Y-m-d', $date, $timezone);
        } catch (\Throwable) {
            return;
        }

        $checkpoint = $this->resolveCheckpoint($checkpointId);

        if (! $checkpoint) {
            return;
        }

        if (! $this->updateCheckpointDueDate($checkpoint, $selectedLocal)) {
            return;
        }

        $this->menuDate = $selectedLocal->format('Y-m-d');
        $this->subtaskPickerSelectedDate = $selectedLocal->format('Y-m-d');
        $this->subtaskPickerCursorDate = $selectedLocal->startOfMonth()->format('Y-m-d');
        $this->showSubtaskDatePicker = false;

        $this->loadMission($this->missionId, $checkpointId);
        $this->dispatch('tasks-updated');
    }

    /**
     * Marca ou desmarca a missão como favorita.
     */
    public function toggleStar(): void
    {
        if (! $this->missionId) {
            return;
        }

        $mission = Mission::query()
            ->where('user_id', Auth::id())
            ->find($this->missionId);

        if (! $mission) {
            return;
        }

        $mission->is_starred = ! $mission->is_starred;
        $mission->save();

        $this->loadMission($mission->id);
        $this->dispatch('tasks-updated');
    }

    /**
     * Aplica atalhos de data às subtarefas.
     */
    private function applySubtaskShortcut(int $checkpointId, string $shortcut): void
    {
        if (! $this->missionId) {
            return;
        }

        $checkpoint = $this->resolveCheckpoint($checkpointId);

        if (! $checkpoint) {
            return;
        }

        if ($shortcut === 'clear') {
            $this->clearSubtaskDueDate($checkpointId);

            return;
        }

        $timezone = $this->userTimezone();
        $today = CarbonImmutable::now($timezone)->startOfDay();

        $target = match ($shortcut) {
            'today' => $today,
            'tomorrow' => $today->addDay(),
            'next7' => $today->addDays(7),
            default => null,
        };

        if (! $target) {
            return;
        }

        if (! $this->updateCheckpointDueDate($checkpoint, $target)) {
            return;
        }

        $this->menuDate = $target->format('Y-m-d');
        $this->subtaskPickerSelectedDate = $target->format('Y-m-d');
        $this->subtaskPickerCursorDate = $target->startOfMonth()->format('Y-m-d');
        $this->showSubtaskDatePicker = false;

        $this->loadMission($this->missionId, $checkpointId);
        $this->dispatch('tasks-updated');
    }

    /**
     * Abre ou fecha o menu de mover missão entre listas.
     */
    public function toggleMoveListMenu(): void
    {
        if (! $this->missionId) {
            return;
        }

        $this->showMoveListMenu = ! $this->showMoveListMenu;
    }

    /**
     * Fecha explicitamente o menu de mover missão.
     */
    public function closeMoveListMenu(): void
    {
        $this->showMoveListMenu = false;
    }

    /**
     * Move a missão atual para outra lista escolhida.
     */
    public function moveToList(?int $listId): void
    {
        if (! $this->missionId) {
            return;
        }

        $user = Auth::user();

        if (! $user) {
            return;
        }

        $mission = Mission::query()
            ->where('user_id', $user->id)
            ->find($this->missionId);

        if (! $mission) {
            return;
        }

        if ($listId !== null) {
            $listExists = TaskList::query()
                ->where('user_id', $user->id)
                ->whereNull('archived_at')
                ->where('id', $listId)
                ->exists();

            if (! $listExists) {
                return;
            }
        }

        $mission->list_id = $listId;
        $mission->save();

        $this->showMoveListMenu = false;

        $this->loadMission($mission->id);
        $this->dispatch('tasks-updated');
    }

    /**
     * Prepara o formulário para criar uma nova subtarefa (opcionalmente com pai).
     */
    public function openSubtaskForm(?int $parentId = null): void
    {
        if (! $this->missionId) {
            return;
        }

        if ($parentId === null && $this->selectedSubtaskId) {
            $parentId = $this->selectedSubtaskId;
        }

        if ($this->reachedSubtaskLimit($this->missionId, $parentId)) {
            return;
        }

        $this->newSubtaskParentId = $parentId;
        $this->newSubtaskParentLabel = $parentId ? $this->resolveSubtaskTitle($parentId) : null;
        $this->newSubtaskTitle = '';
        $this->showSubtaskForm = true;
    }

    /**
     * Cancela a criação de subtarefa e limpa o formulário.
     */
    public function cancelSubtaskForm(): void
    {
        $this->showSubtaskForm = false;
        $this->newSubtaskTitle = '';
        $this->newSubtaskParentId = null;
        $this->newSubtaskParentLabel = null;
    }

    /**
     * Persiste a nova subtarefa criada no painel de detalhes.
     */
    public function saveSubtask(): void
    {
        if (! $this->missionId) {
            return;
        }

        $title = trim($this->newSubtaskTitle);

        if ($title === '') {
            return;
        }

        $mission = Mission::query()
            ->where('user_id', Auth::id())
            ->find($this->missionId);

        if (! $mission) {
            return;
        }

        if ($this->reachedSubtaskLimit($mission->id, $this->newSubtaskParentId)) {
            $this->showSubtaskForm = false;
            $this->newSubtaskTitle = '';
            $this->newSubtaskParentId = null;
            $this->newSubtaskParentLabel = null;

            return;
        }

        $payload = [
            'mission_id' => $mission->id,
            'title' => $title,
            'position' => $this->nextCheckpointPosition($mission->id, $this->newSubtaskParentId),
            'is_done' => false,
        ];

        if ($column = $this->checkpointParentColumn()) {
            $payload[$column] = $this->newSubtaskParentId;
        }

        Checkpoint::create($payload);

        $this->newSubtaskTitle = '';
        $this->showSubtaskForm = false;
        $this->newSubtaskParentId = null;
        $this->newSubtaskParentLabel = null;

        $this->loadMission($mission->id, $this->selectedSubtaskId);
        $this->dispatch('tasks-updated');
    }

    /**
     * Limpa o destaque atual de subtarefa.
     */
    public function clearSelectedSubtask(): void
    {
        if (! $this->missionId) {
            return;
        }

        $this->cancelSubtaskForm();
        $this->applyActiveSubtaskContext(null);
        $this->dispatch('task-selected', $this->missionId, null);
    }

    /**
     * Duplica a missão atual incluindo subtarefas.
     */
    public function duplicateMission(): void
    {
        if (! $this->missionId) {
            return;
        }

        $user = Auth::user();

        if (! $user) {
            return;
        }

        $mission = Mission::query()
            ->with('checkpoints')
            ->where('user_id', $user->id)
            ->find($this->missionId);

        if (! $mission) {
            return;
        }

        $clone = $mission->replicate();
        $clone->status = 'active';
        $clone->is_starred = false;
        $clone->position = $this->nextMissionPosition($user->id, $mission->list_id);
        $clone->title = $this->duplicatedTitle($mission->title);
        $clone->created_at = now();
        $clone->updated_at = now();
        $clone->save();

        $tree = $this->buildCheckpointTree(collect($mission->checkpoints ?? []), $this->userTimezone());

        if ($tree !== []) {
            $this->replicateCheckpointBranch($tree, $clone->id, null);
        }

        $this->dispatch('tasks-updated');
    }

    /**
     * Remove definitivamente a missão em edição.
     */
    public function deleteMission(): void
    {
        if (! $this->missionId) {
            return;
        }

        $mission = Mission::query()
            ->where('user_id', Auth::id())
            ->find($this->missionId);

        if (! $mission) {
            return;
        }

        $mission->delete();

        $this->missionId = null;
        $this->mission = null;
        $this->missionTags = [];
        $this->availableLists = [];
        $this->showMoveListMenu = false;
        $this->showDatePicker = false;
        $this->showSubtaskForm = false;
        $this->newSubtaskTitle = '';
        $this->pickerSelectedDate = null;
        $this->pickerCursorDate = null;
        $this->menuDate = null;

        $this->dispatch('tasks-updated');
        $this->dispatch('task-selected', null, null);
    }

    /**
     * Cria uma sessão de Pomodoro vinculada à missão atual.
     */
    public function startPomodoro(): void
    {
        if (! $this->missionId) {
            return;
        }

        $user = Auth::user();

        if (! $user) {
            return;
        }

        $mission = Mission::query()
            ->where('user_id', $user->id)
            ->find($this->missionId);

        if (! $mission) {
            return;
        }

        $timezone = $this->userTimezone();
        $nowClient = CarbonImmutable::now($timezone);
        $nowServer = CarbonImmutable::now(config('app.timezone'));

        $session = PomodoroSession::create([
            'user_id' => $user->id,
            'mission_id' => $mission->id,
            'type' => 'focus',
            'started_at_client' => $nowClient,
            'client_timezone' => $timezone,
            'client_utc_offset_minutes' => $nowClient->utcOffset(),
            'started_at_server' => $nowServer,
            'created_at' => $nowServer,
            'duration_seconds' => 0,
            'pause_count' => 0,
            'pause_total_seconds' => 0,
        ]);

        $this->dispatch('pomodoro-started', [
            'sessionId' => $session->id,
            'missionId' => $mission->id,
        ]);
    }

    /**
     * Monta uma árvore de subtarefas respeitando a ordem e o limite de profundidade.
     */
    /**
     * Constrói a árvore hierárquica de subtarefas para exibição.
     */
    private function buildCheckpointTree(Collection $checkpoints, ?string $timezone = null): array
    {
        if ($checkpoints->isEmpty()) {
            return [];
        }

        $parentColumn = $this->checkpointParentColumn();

        $timezone = $timezone ?? $this->userTimezone();

        $grouped = $checkpoints->groupBy(function ($checkpoint) use ($parentColumn) {
            $parentId = $parentColumn ? ($checkpoint->{$parentColumn} ?? null) : null;

            return $parentId === null ? '__root__' : (string) $parentId;
        });

        return $this->buildCheckpointBranch($grouped, null, 0, $timezone);
    }

    /**
     * Normaliza o identificador do pai recebido do frontend.
     */
    private function normalizeParentId($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    /**
     * Informa qual coluna representa o relacionamento pai nas subtarefas.
     */
    private function checkpointParentColumn(): ?string
    {
        static $parentColumn;

        if ($parentColumn === null) {
            if (Schema::hasColumn('checkpoints', 'parent_id')) {
                $parentColumn = 'parent_id';
            } elseif (Schema::hasColumn('checkpoints', 'parent_checkpoint_id')) {
                $parentColumn = 'parent_checkpoint_id';
            } else {
                $parentColumn = '';
            }
        }

        return $parentColumn !== '' ? $parentColumn : null;
    }

    /**
     * Monta recursivamente um ramo de subtarefas.
     */
    private function buildCheckpointBranch(Collection $grouped, ?int $parentId, int $depth, string $timezone): array
    {
        $key = $parentId === null ? '__root__' : (string) $parentId;

        return $grouped->get($key, collect())->map(function ($checkpoint) use ($grouped, $depth, $timezone, $parentId) {
            return [
                'id' => $checkpoint->id,
                'mission_id' => $checkpoint->mission_id,
                'parent_id' => $parentId,
                'title' => $checkpoint->title,
                'is_done' => (bool) $checkpoint->is_done,
                'position' => $checkpoint->position,
                'xp_reward' => $checkpoint->xp_reward,
                'created_at' => $checkpoint->created_at?->copy()->setTimezone($timezone),
                'updated_at' => $checkpoint->updated_at?->copy()->setTimezone($timezone),
                'due_at' => $checkpoint->due_at?->copy()->setTimezone($timezone),
                'children' => $depth >= 6
                    ? []
                    : $this->buildCheckpointBranch($grouped, $checkpoint->id, $depth + 1, $timezone),
            ];
        })->values()->toArray();
    }

    /**
     * Atualiza a ordenação das subtarefas após interação do usuário.
     */
    private function syncCheckpointOrder(int $missionId, ?int $parentId, Collection $order): void
    {
        if ($order->isEmpty()) {
            return;
        }

        $parentColumn = $this->checkpointParentColumn();

        $position = 1;

        foreach ($order as $checkpointId) {
            $query = Checkpoint::query()
                ->where('mission_id', $missionId)
                ->where('id', $checkpointId);

            if ($parentColumn) {
                if ($parentId === null) {
                    $query->whereNull($parentColumn);
                } else {
                    $query->where($parentColumn, $parentId);
                }
            }

            $query->update(['position' => $position++]);
        }
    }

    /**
     * Ajusta o contexto da subtarefa ativa para destacar a navegação.
     */
    private function applyActiveSubtaskContext(?int $checkpointId): void
    {
        if (! is_array($this->mission)) {
            $this->selectedSubtaskId = null;
            $this->showSubtaskDatePicker = false;
            $this->subtaskPickerSelectedDate = null;
            $this->subtaskPickerCursorDate = null;

            return;
        }

        if ($checkpointId === null) {
            $this->selectedSubtaskId = null;
            $this->mission['active_subtask'] = null;
            $this->mission['active_subtask_path'] = [];
            $this->mission['parent_title'] = null;
            $this->menuDate = $this->pickerSelectedDate;
            $this->showSubtaskDatePicker = false;
            $this->subtaskPickerSelectedDate = null;
            $this->subtaskPickerCursorDate = null;

            return;
        }

        $trail = $this->findSubtaskTrail($this->mission['subtasks'] ?? [], $checkpointId);

        if ($trail === null) {
            $this->selectedSubtaskId = null;
            $this->mission['active_subtask'] = null;
            $this->mission['active_subtask_path'] = [];
            $this->mission['parent_title'] = null;
            $this->menuDate = $this->pickerSelectedDate;
            $this->showSubtaskDatePicker = false;
            $this->subtaskPickerSelectedDate = null;
            $this->subtaskPickerCursorDate = null;

            return;
        }

        $this->selectedSubtaskId = $checkpointId;
        $this->mission['active_subtask'] = $trail['node'];
        $this->mission['active_subtask_path'] = $trail['ancestors'];
        $this->mission['parent_title'] = $this->formatParentTitle($this->mission['title'] ?? '', $trail['ancestors']);

        $activeNode = $trail['node'] ?? null;
        $dueAt = $activeNode['due_at'] ?? null;
        $timezone = $this->userTimezone();

        if ($dueAt instanceof CarbonInterface) {
            $this->menuDate = $dueAt->format('Y-m-d');
            $this->subtaskPickerSelectedDate = $dueAt->format('Y-m-d');
            $this->subtaskPickerCursorDate = $dueAt->copy()->setTimezone($timezone)->startOfMonth()->format('Y-m-d');
        } else {
            $this->menuDate = null;
            $this->subtaskPickerSelectedDate = null;
            $this->subtaskPickerCursorDate = CarbonImmutable::now($timezone)->startOfMonth()->format('Y-m-d');
        }

        $this->showSubtaskDatePicker = false;
    }

    /**
     * Localiza o caminho (ancestrais) de uma subtarefa na árvore.
     */
    private function findSubtaskTrail(array $nodes, int $checkpointId, array $trail = []): ?array
    {
        foreach ($nodes as $node) {
            $currentTrail = [...$trail, $node];

            if (($node['id'] ?? null) === $checkpointId) {
                return [
                    'node' => $node,
                    'ancestors' => $trail,
                ];
            }

            if (! empty($node['children'])) {
                $found = $this->findSubtaskTrail($node['children'], $checkpointId, $currentTrail);

                if ($found !== null) {
                    return $found;
                }
            }
        }

        return null;
    }

    /**
     * Busca uma subtarefa específica dentro da árvore montada.
     */
    private function findSubtaskInTree(array $nodes, int $checkpointId): ?array
    {
        $trail = $this->findSubtaskTrail($nodes, $checkpointId);

        return $trail['node'] ?? null;
    }

    /**
     * Descobre o título atual de uma subtarefa.
     */
    private function resolveSubtaskTitle(int $checkpointId): ?string
    {
        if (! is_array($this->mission)) {
            return null;
        }

        $node = $this->findSubtaskInTree($this->mission['subtasks'] ?? [], $checkpointId);

        if (! $node) {
            return null;
        }

        $title = trim((string) ($node['title'] ?? ''));

        return $title !== '' ? $title : 'Sem título';
    }

    /**
     * Gera o rótulo do pai ao abrir o formulário de subtarefa.
     */
    private function formatParentTitle(?string $missionTitle, array $ancestors): ?string
    {
        $segments = [];

        $missionTitle = trim((string) $missionTitle);
        if ($missionTitle !== '') {
            $segments[] = $missionTitle;
        }

        foreach ($ancestors as $node) {
            $title = trim((string) ($node['title'] ?? ''));

            if ($title !== '') {
                $segments[] = $title;
            }
        }

        if ($segments === []) {
            return null;
        }

        return implode(' › ', $segments);
    }

    /**
     * Duplicação recursiva das subtarefas ao clonar missões.
     */
    private function replicateCheckpointBranch(array $nodes, int $missionId, ?int $parentId, int $depth = 0): void
    {
        if ($depth >= 7) {
            return;
        }

        foreach ($nodes as $node) {
            $payload = [
                'mission_id' => $missionId,
                'title' => trim((string) ($node['title'] ?? '')),
                'position' => $node['position'] ?? 0,
                'is_done' => false,
                'xp_reward' => $node['xp_reward'] ?? null,
            ];

            if ($column = $this->checkpointParentColumn()) {
                $payload[$column] = $parentId;
            }

            $checkpoint = Checkpoint::create($payload);

            if (! empty($node['children'])) {
                $this->replicateCheckpointBranch($node['children'], $missionId, $checkpoint->id, $depth + 1);
            }
        }
    }

    /**
     * Recupera uma subtarefa garantindo que pertence ao usuário logado.
     */
    private function resolveCheckpoint(int $checkpointId): ?Checkpoint
    {
        if (! $this->missionId) {
            return null;
        }

        return Checkpoint::query()
            ->where('id', $checkpointId)
            ->whereHas('mission', fn ($query) => $query
                ->where('id', $this->missionId)
                ->where('user_id', Auth::id())
            )
            ->first();
    }

    /**
     * Atualiza a data de entrega de uma subtarefa específica.
     */
    private function updateCheckpointDueDate(Checkpoint $checkpoint, ?CarbonImmutable $localDate): bool
    {
        $current = $checkpoint->due_at;

        if ($localDate === null) {
            if ($current === null) {
                return false;
            }

            $checkpoint->due_at = null;
            $checkpoint->save();

            return true;
        }

        $serverDate = $localDate->setTimezone(config('app.timezone'));

        if ($current && $current->equalTo($serverDate)) {
            return false;
        }

        $checkpoint->due_at = $serverDate;
        $checkpoint->save();

        return true;
    }

    /**
     * Constrói os dados do calendário exibido no seletor de datas.
     */
    private function buildCalendar(): array
    {
        return $this->buildCalendarData(
            $this->pickerSelectedDate,
            $this->resolveCursor($this->userTimezone()),
            $this->userTimezone(),
        );
    }

    /**
     * Constrói o calendário usado pelo seletor de subtarefas.
     */
    private function buildSubtaskCalendar(): array
    {
        return $this->buildCalendarData(
            $this->subtaskPickerSelectedDate,
            $this->resolveSubtaskCursor($this->userTimezone()),
            $this->userTimezone(),
            true,
        );
    }

    /**
     * Gera a matriz de semanas comum aos seletores de data.
     */
    private function buildCalendarData(?string $selectedDate, CarbonImmutable $cursor, string $timezone, bool $isSubtask = false): array
    {
        $today = CarbonImmutable::now($timezone)->startOfDay();
        $selected = null;

        if ($selectedDate) {
            try {
                $selected = CarbonImmutable::createFromFormat('Y-m-d', $selectedDate, $timezone);
            } catch (\Throwable) {
                $selected = null;
            }
        }

        $start = $cursor->startOfWeek(CarbonInterface::MONDAY);
        $end = $cursor->endOfMonth()->endOfWeek(CarbonInterface::SUNDAY);

        $date = $start;
        $weeks = [];
        $week = [];

        while ($date <= $end) {
            $week[] = [
                'date' => $date->format('Y-m-d'),
                'label' => $date->format('j'),
                'isCurrentMonth' => $date->month === $cursor->month,
                'isToday' => $date->isSameDay($today),
                'isSelected' => $selected ? $date->isSameDay($selected) : false,
            ];

            if (count($week) === 7) {
                $weeks[] = $week;
                $week = [];
            }

            $date = $date->addDay();
        }

        if ($week !== []) {
            $weeks[] = $week;
        }

        $label = $cursor
            ->locale(app()->getLocale() ?? 'en')
            ->translatedFormat('F Y');

        return [
            'label' => Str::title($label),
            'weeks' => $weeks,
            'weekDays' => $this->weekDays(),
            'hasSelected' => (bool) $selectedDate,
            'isSubtask' => $isSubtask,
        ];
    }

    /**
     * Determina o cursor de datas usado no calendário.
     */
    private function resolveCursor(string $timezone): CarbonImmutable
    {
        if ($this->pickerCursorDate) {
            try {
                return CarbonImmutable::createFromFormat('Y-m-d', $this->pickerCursorDate, $timezone)->startOfMonth();
            } catch (\Throwable) {
                // fallback below
            }
        }

        if ($this->pickerSelectedDate) {
            try {
                $cursor = CarbonImmutable::createFromFormat('Y-m-d', $this->pickerSelectedDate, $timezone)->startOfMonth();
                $this->pickerCursorDate = $cursor->format('Y-m-d');

                return $cursor;
            } catch (\Throwable) {
                // fallback below
            }
        }

        $cursor = CarbonImmutable::now($timezone)->startOfMonth();
        $this->pickerCursorDate = $cursor->format('Y-m-d');

        return $cursor;
    }

    /**
     * Determina o cursor de datas usado no calendário de subtarefas.
     */
    private function resolveSubtaskCursor(string $timezone): CarbonImmutable
    {
        if ($this->subtaskPickerCursorDate) {
            try {
                return CarbonImmutable::createFromFormat('Y-m-d', $this->subtaskPickerCursorDate, $timezone)->startOfMonth();
            } catch (\Throwable) {
                // fallback below
            }
        }

        if ($this->subtaskPickerSelectedDate) {
            try {
                $cursor = CarbonImmutable::createFromFormat('Y-m-d', $this->subtaskPickerSelectedDate, $timezone)->startOfMonth();
                $this->subtaskPickerCursorDate = $cursor->format('Y-m-d');

                return $cursor;
            } catch (\Throwable) {
                // fallback below
            }
        }

        $cursor = CarbonImmutable::now($timezone)->startOfMonth();
        $this->subtaskPickerCursorDate = $cursor->format('Y-m-d');

        return $cursor;
    }

    /**
     * Retorna o fuso horário preferido do usuário autenticado.
     */
    private function userTimezone(): string
    {
        $user = Auth::user();

        return $user?->timezone ?? config('app.timezone');
    }

    /**
     * Lista abreviada dos dias da semana para o calendário.
     */
    private function weekDays(): array
    {
        return ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'];
    }

    /**
     * Calcula a próxima posição disponível para duplicar missões.
     */
    private function nextMissionPosition(int $userId, ?int $listId): int
    {
        return (int) Mission::query()
            ->where('user_id', $userId)
            ->when(
                $listId,
                fn ($query) => $query->where('list_id', $listId),
                fn ($query) => $query->whereNull('list_id')
            )
            ->max('position') + 1;
    }

    /**
     * Determina a próxima posição para inserir uma subtarefa.
     */
    private function nextCheckpointPosition(int $missionId, ?int $parentId = null): int
    {
        $query = Checkpoint::query()
            ->where('mission_id', $missionId);

        if ($column = $this->checkpointParentColumn()) {
            if ($parentId === null) {
                $query->whereNull($column);
            } else {
                $query->where($column, $parentId);
            }
        }

        $position = (int) $query->max('position');

        return $position + 1;
    }

    /**
     * Verifica se o limite de subtarefas foi atingido.
     */
    private function reachedSubtaskLimit(int $missionId, ?int $parentId = null): bool
    {
        $query = Checkpoint::query()->where('mission_id', $missionId);

        if ($column = $this->checkpointParentColumn()) {
            if ($parentId === null) {
                $query->whereNull($column);
            } else {
                $query->where($column, $parentId);
            }
        }

        return $query->count() >= self::MAX_SUBTASKS;
    }

    /**
     * Cria um título amigável ao duplicar subtarefas ou missões.
     */
    private function duplicatedTitle(?string $title): string
    {
        $base = trim((string) $title);

        if ($base === '') {
            $base = 'Sem título';
        }

        return sprintf('%s (Cópia)', $base);
    }
}
