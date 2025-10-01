<?php

namespace App\Livewire\Tasks;

use App\Models\Checkpoint;
use App\Models\Mission;
use App\Models\TaskList;
use App\Support\MissionShortcutFilter;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

/**
 * Componente que representa a lista principal de tarefas da página Tasks.
 * Lida com criação, seleção, ações inline e ordenação de missões e subtarefas.
 */
class MainPanel extends Component
{
    public const MAX_SUBTASKS = 7;

    /**
     * Eventos do Livewire que mantêm o painel sincronizado com outras áreas.
     */
    protected $listeners = [
        'tasks-updated' => '$refresh',
        'task-selected' => 'syncSelectedMission',
    ];

    /**
     * Lista atualmente exibida (null significa visão geral "All").
     */
    public ?int $currentListId = null;

    /**
     * Nome da tarefa a ser criada a partir da barra "Adicionar".
     */
    public string $newTaskTitle = '';

    /**
     * Lista selecionada (opcional) ao criar uma nova tarefa.
     */
    public ?int $newTaskListId = null;

    /**
     * Placeholder configurável para o campo de criação.
     */
    public string $inputPlaceholder = "Adicione uma tarefa";

    /**
     * Tarefa atualmente selecionada no painel (para destacar e exibir detalhes).
     */
    public ?int $selectedMissionId = null;

    /**
     * Subtarefa atualmente selecionada.
     */
    public ?int $selectedSubtaskId = null;

    /**
     * Controle de edição inline de missões.
     */
    public ?int $editingMissionId = null;

    public string $editingMissionTitle = '';

    /**
     * Controle de edição inline de subtarefas.
     */
    public ?int $editingSubtaskId = null;

    public ?int $editingSubtaskMissionId = null;

    public string $editingSubtaskTitle = '';

    public ?string $shortcut = null;

    /**
     * Sincroniza a seleção de missão/subtarefa ao receber eventos externos.
     */
    public function syncSelectedMission(?int $missionId = null, ?int $checkpointId = null): void
    {
        $this->selectedMissionId = $missionId;
        $this->selectedSubtaskId = $checkpointId;
    }

    /**
     * Inicializa o painel principal com filtros ativos.
     */
    public function mount(?int $currentListId = null, ?string $shortcut = null): void
    {
        $this->currentListId = $currentListId;

        if ($this->currentListId) {
            $this->newTaskListId = $this->currentListId;
        }

        if ($shortcut && in_array($shortcut, MissionShortcutFilter::supported(), true)) {
            $this->shortcut = $shortcut;
        }

        if ($this->currentListId) {
            $this->shortcut = null;
        }
    }

    /**
     * Cria uma nova missão na lista selecionada.
     */
    public function createTask(): void
    {
        $user = Auth::user();

        if (! $user) {
            throw ValidationException::withMessages([
                'newTaskTitle' => 'Sua sessão expirou. Faça login novamente.',
            ]);
        }

        $validated = $this->validate(
            [
                'newTaskTitle' => 'required|string|max:255',
                'newTaskListId' => 'nullable|integer',
            ],
            [],
            [
                'newTaskTitle' => 'título',
                'newTaskListId' => 'lista',
            ]
        );

        $listId = $validated['newTaskListId'] ?? null;

        if ($listId === null && $this->currentListId) {
            $listId = $this->currentListId;
        }

        if ($listId !== null) {
            $belongsToUser = TaskList::query()
                ->where('id', $listId)
                ->where('user_id', $user->id)
                ->whereNull('archived_at')
                ->exists();

            if (! $belongsToUser) {
                $this->addError('newTaskListId', 'Lista inválida para este usuário.');

                return;
            }
        }

        $mission = Mission::create([
            'user_id' => $user->id,
            'list_id' => $listId,
            'title' => trim($validated['newTaskTitle']),
            'status' => 'active',
            'position' => $this->nextPosition($user->id, $listId),
        ]);

        $this->reset(['newTaskTitle']);

        $this->selectedMissionId = $mission->id;
        $this->selectedSubtaskId = null;

        $this->dispatch('tasks-updated');
        $this->dispatch('task-selected', $mission->id, null);
    }

    /**
     * Define qual missão está ativa no painel e dispara eventos para detalhes.
     */
    public function selectMission(int $missionId): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $mission = Mission::query()
            ->where('user_id', $user->id)
            ->when($this->currentListId, fn ($query) => $query->where('list_id', $this->currentListId))
            ->when(
                $this->shortcut,
                fn ($query) => MissionShortcutFilter::apply($query, $this->shortcut, $user->timezone ?? config('app.timezone'))
            )
            ->find($missionId);

        if (! $mission) {
            return;
        }

        $this->selectedMissionId = $mission->id;
        $this->selectedSubtaskId = null;
        $this->dispatch('task-selected', $mission->id, null);

        $this->startMissionEdit($mission->id, $mission);
    }

    /**
     * Processa ações rápidas (datas, criação de subtarefa etc.) direto da lista.
     */
    public function runInlineAction(int $missionId, string $action, ?string $value = null, ?int $checkpointId = null): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $query = Mission::query()
            ->where('user_id', $user->id)
            ->when($this->currentListId, fn ($builder) => $builder->where('list_id', $this->currentListId));

        if ($this->shortcut) {
            MissionShortcutFilter::apply($query, $this->shortcut, $user->timezone ?? config('app.timezone'));
        }

        $mission = $query->find($missionId);

        if (! $mission) {
            return;
        }

        if ($checkpointId !== null) {
            $checkpointExists = Checkpoint::query()
                ->where('id', $checkpointId)
                ->where('mission_id', $mission->id)
                ->exists();

            if (! $checkpointExists) {
                return;
            }

            $this->selectedMissionId = $mission->id;
            $this->selectedSubtaskId = $checkpointId;
            $this->dispatch('task-selected', $mission->id, $checkpointId);

            // Subtarefas não herdam ações inline além da seleção.
            return;
        }

        $this->selectedMissionId = $mission->id;
        $this->selectedSubtaskId = null;
        $this->dispatch('task-selected', $mission->id, null);

        if ($action === 'create-subtask') {
            $this->createSubtaskForMission($mission->id);

            return;
        }

        $timezone = $user->timezone ?? config('app.timezone');

        if ($action === 'set-date') {
            $changed = $this->handleMissionDateSelection($mission, $value, $timezone);

            if ($changed) {
                $this->dispatch('tasks-updated');
            }

            $this->dispatch('tasks-inline-action', $mission->id, $action, $value, null);

            return;
        }

        if ($action === 'due-shortcut') {
            $changed = $this->handleMissionShortcut($mission, $value, $timezone);

            if ($changed) {
                $this->dispatch('tasks-updated');
            }

            $this->dispatch('tasks-inline-action', $mission->id, $action, $value, null);

            return;
        }

        $this->dispatch('tasks-inline-action', $mission->id, $action, $value, null);
    }

    /**
     * Seleciona uma subtarefa vinculada a determinada missão.
     */
    public function selectSubtask(int $missionId, int $checkpointId): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $mission = Mission::query()
            ->where('user_id', $user->id)
            ->when($this->currentListId, fn ($query) => $query->where('list_id', $this->currentListId))
            ->when(
                $this->shortcut,
                fn ($query) => MissionShortcutFilter::apply($query, $this->shortcut, $user->timezone ?? config('app.timezone'))
            )
            ->find($missionId);

        if (! $mission) {
            return;
        }

        $checkpoint = Checkpoint::query()
            ->where('id', $checkpointId)
            ->where('mission_id', $mission->id)
            ->exists();

        if (! $checkpoint) {
            return;
        }

        $this->selectedMissionId = $mission->id;
        $this->selectedSubtaskId = $checkpointId;

        $this->dispatch('task-selected', $mission->id, $checkpointId);

        $this->startSubtaskEdit($checkpointId);
    }

    /**
     * Alterna o status de conclusão de uma subtarefa direto da lista principal.
     */
    public function toggleSubtaskCompletion(int $missionId, int $checkpointId): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $checkpoint = Checkpoint::query()
            ->where('id', $checkpointId)
            ->whereHas('mission', function ($query) use ($user, $missionId) {
                $query->where('id', $missionId)
                    ->where('user_id', $user->id)
                    ->when(
                        $this->currentListId,
                        fn ($inner) => $inner->where('list_id', $this->currentListId)
                    );

                if ($this->shortcut) {
                    MissionShortcutFilter::apply($query, $this->shortcut, $user->timezone ?? config('app.timezone'));
                }
            })
            ->first();

        if (! $checkpoint) {
            return;
        }

        $checkpoint->is_done = ! $checkpoint->is_done;
        $checkpoint->save();

        if ($this->selectedMissionId !== $missionId) {
            $this->selectedMissionId = $missionId;
        }

        $this->selectedSubtaskId = $checkpoint->id;

        $this->dispatch('tasks-updated');
    }

    /**
     * Remove uma subtarefa sem precisar abrir o painel detalhado.
     */
    public function deleteSubtask(int $missionId, int $checkpointId): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $checkpoint = Checkpoint::query()
            ->where('id', $checkpointId)
            ->whereHas('mission', function ($query) use ($user, $missionId) {
                $query->where('id', $missionId)
                    ->where('user_id', $user->id)
                    ->when(
                        $this->currentListId,
                        fn ($inner) => $inner->where('list_id', $this->currentListId)
                    );

                if ($this->shortcut) {
                    MissionShortcutFilter::apply($query, $this->shortcut, $user->timezone ?? config('app.timezone'));
                }
            })
            ->first();

        if (! $checkpoint) {
            return;
        }

        $parentColumn = $this->checkpointParentColumn();
        $parentId = $parentColumn ? ($checkpoint->{$parentColumn} ?? null) : null;

        $checkpoint->delete();

        if ($this->selectedMissionId !== $missionId) {
            $this->selectedMissionId = $missionId;
        }

        if ($this->selectedSubtaskId === $checkpointId) {
            $this->selectedSubtaskId = $parentId;
            $this->dispatch('task-selected', $missionId, $parentId);
        }

        $this->dispatch('tasks-updated');
    }

    /**
     * Ativa o modo de edição inline para uma missão.
     */
    public function startMissionEdit(int $missionId, ?Mission $model = null): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $mission = $model;

        if (! $mission) {
            $mission = Mission::query()
                ->where('user_id', $user->id)
                ->find($missionId);
        }

        if (! $mission) {
            return;
        }

        $this->editingMissionId = $mission->id;
        $this->editingMissionTitle = $mission->title ?? '';

        $this->dispatch('focus-mission-input', missionId: $mission->id);
    }

    /**
     * Cancela a edição de missão e limpa o estado temporário.
     */
    public function cancelMissionEdit(): void
    {
        $this->editingMissionId = null;
        $this->editingMissionTitle = '';
    }

    /**
     * Salva alterações no título da missão (com opção de criar outra em sequência).
     */
    public function saveMissionEdit(int $missionId, bool $createAnother = false): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        if ($this->editingMissionId !== $missionId) {
            return;
        }

        $mission = Mission::query()
            ->where('user_id', $user->id)
            ->find($missionId);

        if (! $mission) {
            return;
        }

        $title = trim($this->editingMissionTitle);

        if ($title === '') {
            $title = 'Sem título';
        }

        $mission->title = $title;
        $mission->save();

        $this->editingMissionId = null;
        $this->editingMissionTitle = '';

        if ($createAnother) {
            $this->createMissionAfter($missionId);

            return;
        }

        $this->dispatch('tasks-updated');
    }

    /**
     * Cria uma nova missão logo abaixo da missão fornecida.
     */
    public function createMissionAfter(int $missionId): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $reference = Mission::query()
            ->where('user_id', $user->id)
            ->find($missionId);

        if (! $reference) {
            return;
        }

        $position = $reference->position ?? 0;
        $mission = null;

        DB::transaction(function () use ($user, $reference, $position, &$mission) {
            Mission::query()
                ->where('user_id', $user->id)
                ->when(
                    $reference->list_id !== null,
                    fn ($query) => $query->where('list_id', $reference->list_id),
                    fn ($query) => $query->whereNull('list_id')
                )
                ->where('position', '>', $position)
                ->increment('position');

            $mission = Mission::create([
                'user_id' => $user->id,
                'list_id' => $reference->list_id,
                'title' => 'Nova tarefa',
                'status' => 'active',
                'position' => $position + 1,
            ]);
        });

        if (! $mission) {
            return;
        }

        $this->editingMissionId = $mission->id;
        $this->editingMissionTitle = $mission->title;

        $this->selectedMissionId = $mission->id;
        $this->selectedSubtaskId = null;

        $this->dispatch('tasks-updated');
        $this->dispatch('task-selected', $mission->id, null);

        $this->dispatch('focus-mission-input', missionId: $mission->id);
    }

    /**
     * Atalho que cria uma subtarefa ao pressionar Shift+Enter na missão.
     */
    public function missionShiftEnter(int $missionId): void
    {
        if ($this->editingMissionId === $missionId) {
            $this->saveMissionEdit($missionId);
        }

        $this->createSubtaskForMission($missionId);
    }

    /**
     * Persiste a nova ordem de missões após drag and drop no painel.
     */
    public function reorderMissions(array $ordered): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $items = collect($ordered)
            ->map(function ($item) {
                if (! is_array($item)) {
                    return null;
                }

                $id = isset($item['id']) ? (int) $item['id'] : null;

                if (! $id) {
                    return null;
                }

                $listId = null;

                if (array_key_exists('list_id', $item)) {
                    $raw = $item['list_id'];
                    $listId = $raw === null || $raw === '' ? null : (int) $raw;
                }

                return [
                    'id' => $id,
                    'list_id' => $listId,
                ];
            })
            ->filter()
            ->values();

        if ($items->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($items, $user) {
            $items
                ->groupBy(fn ($item) => $item['list_id'] ?? '__null__')
                ->each(function ($group, $key) use ($user) {
                    $listId = $key === '__null__' ? null : (int) $key;
                    $position = 1;

                    foreach ($group as $item) {
                        Mission::query()
                            ->where('user_id', $user->id)
                            ->where('id', $item['id'])
                            ->update(['position' => $position++]);
                    }
                });
        });

        $this->dispatch('tasks-updated');
    }

    public function reorderSubtasks(int $missionId, array $data): void
    {
        $user = Auth::user();

        if (! $user) {
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
    }

    /**
     * Inicia a edição inline de uma subtarefa existente.
     */
    public function startSubtaskEdit(int $checkpointId): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $checkpoint = $this->findUserCheckpoint($checkpointId, $user->id);

        if (! $checkpoint) {
            return;
        }

        $this->editingSubtaskId = $checkpoint->id;
        $this->editingSubtaskTitle = $checkpoint->title ?? '';
        $this->editingSubtaskMissionId = $checkpoint->mission_id;

        $this->dispatch('focus-subtask-input', subtaskId: $checkpoint->id);
    }

    /**
     * Cancela a edição de subtarefa e restaura o estado padrão.
     */
    public function cancelSubtaskEdit(): void
    {
        $this->editingSubtaskId = null;
        $this->editingSubtaskTitle = '';
        $this->editingSubtaskMissionId = null;
    }

    /**
     * Salva a subtarefa editada e permite criar irmãs ou filhas rapidamente.
     */
    public function saveSubtaskEdit(int $checkpointId, bool $createSibling = false, bool $createChild = false): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        if ($this->editingSubtaskId !== $checkpointId) {
            return;
        }

        $checkpoint = $this->findUserCheckpoint($checkpointId, $user->id);

        if (! $checkpoint) {
            return;
        }

        $title = trim($this->editingSubtaskTitle);

        if ($title === '') {
            $title = 'Sem título';
        }

        $checkpoint->title = $title;
        $checkpoint->save();

        $missionId = $checkpoint->mission_id;

        $this->cancelSubtaskEdit();

        if ($createChild) {
            $this->createChildSubtask($checkpointId);

            return;
        }

        if ($createSibling) {
            $this->createSiblingSubtask($checkpointId);

            return;
        }

        $this->dispatch('tasks-updated');
        $this->selectedMissionId = $missionId;
    }

    /**
     * Cria rapidamente uma subtarefa básica para a missão informada.
     */
    public function createSubtaskForMission(int $missionId): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $mission = Mission::query()
            ->where('user_id', $user->id)
            ->find($missionId);

        if (! $mission) {
            return;
        }

        if ($this->reachedSubtaskLimit($mission->id, null)) {
            return;
        }

        $payload = [
            'mission_id' => $mission->id,
            'title' => 'Nova subtarefa',
            'is_done' => false,
            'position' => $this->nextCheckpointPosition($mission->id, null),
        ];

        if ($column = $this->checkpointParentColumn()) {
            $payload[$column] = null;
        }

        $subtask = Checkpoint::create($payload);

        $this->editingSubtaskId = $subtask->id;
        $this->editingSubtaskTitle = $subtask->title;
        $this->editingSubtaskMissionId = $mission->id;

        $this->selectedMissionId = $mission->id;
        $this->selectedSubtaskId = $subtask->id;

        $this->dispatch('tasks-updated');
        $this->dispatch('task-selected', $mission->id, $subtask->id);

        $this->dispatch('focus-subtask-input', subtaskId: $subtask->id);
    }

    /**
     * Gera uma subtarefa irmã alinhada ao mesmo nível hierárquico.
     */
    public function createSiblingSubtask(int $checkpointId): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $checkpoint = $this->findUserCheckpoint($checkpointId, $user->id);

        if (! $checkpoint) {
            return;
        }

        $column = $this->checkpointParentColumn();
        $parentId = $column ? ($checkpoint->{$column} ?? null) : null;

        if ($this->reachedSubtaskLimit($checkpoint->mission_id, $parentId)) {
            return;
        }

        $payload = [
            'mission_id' => $checkpoint->mission_id,
            'title' => 'Nova subtarefa',
            'is_done' => false,
            'position' => $this->nextCheckpointPosition($checkpoint->mission_id, $parentId),
        ];

        if ($column) {
            $payload[$column] = $parentId;
        }

        $sibling = Checkpoint::create($payload);

        $this->editingSubtaskId = $sibling->id;
        $this->editingSubtaskMissionId = $checkpoint->mission_id;
        $this->editingSubtaskTitle = $sibling->title;

        $this->selectedMissionId = $checkpoint->mission_id;
        $this->selectedSubtaskId = $sibling->id;

        $this->dispatch('tasks-updated');
        $this->dispatch('task-selected', $checkpoint->mission_id, $sibling->id);

        $this->dispatch('focus-subtask-input', subtaskId: $sibling->id);
    }

    /**
     * Cria uma subtarefa filha diretamente abaixo da atual.
     */
    public function createChildSubtask(int $checkpointId): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $checkpoint = $this->findUserCheckpoint($checkpointId, $user->id);

        if (! $checkpoint) {
            return;
        }

        $column = $this->checkpointParentColumn();

        if (! $column) {
            $this->createSiblingSubtask($checkpointId);

            return;
        }

        if ($this->reachedSubtaskLimit($checkpoint->mission_id, $checkpoint->id)) {
            return;
        }

        $payload = [
            'mission_id' => $checkpoint->mission_id,
            'title' => 'Nova subtarefa',
            'is_done' => false,
            'position' => $this->nextCheckpointPosition($checkpoint->mission_id, $checkpoint->id),
        ];

        $payload[$column] = $checkpoint->id;

        $child = Checkpoint::create($payload);

        $this->editingSubtaskId = $child->id;
        $this->editingSubtaskMissionId = $checkpoint->mission_id;
        $this->editingSubtaskTitle = $child->title;

        $this->selectedMissionId = $checkpoint->mission_id;
        $this->selectedSubtaskId = $child->id;

        $this->dispatch('tasks-updated');
        $this->dispatch('task-selected', $checkpoint->mission_id, $child->id);

        $this->dispatch('focus-subtask-input', subtaskId: $child->id);
    }

    /**
     * Calcula a posição da próxima missão ao criar itens no painel.
     */
    private function nextPosition(int $userId, ?int $listId): int
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
     * Processa a seleção manual de datas para missões.
     */
    private function handleMissionDateSelection(Mission $mission, ?string $value, string $timezone): bool
    {
        if ($value === null || $value === '') {
            return $this->updateMissionDueDate($mission, null);
        }

        $localDate = $this->parseLocalDate($value, $timezone);

        if (! $localDate) {
            return false;
        }

        return $this->updateMissionDueDate($mission, $localDate);
    }

    /**
     * Aplica atalhos de vencimento às missões (hoje, amanhã, etc.).
     */
    private function handleMissionShortcut(Mission $mission, ?string $shortcut, string $timezone): bool
    {
        if (! $shortcut) {
            return false;
        }

        if ($shortcut === 'clear') {
            return $this->updateMissionDueDate($mission, null);
        }

        $today = CarbonImmutable::now($timezone)->startOfDay();

        $target = match ($shortcut) {
            'today' => $today,
            'tomorrow' => $today->addDay(),
            'next7' => $today->addDays(7),
            default => null,
        };

        if (! $target) {
            return false;
        }

        return $this->updateMissionDueDate($mission, $target);
    }

    /**
     * Converte datas vindas da interface considerando o fuso horário do usuário.
     */
    private function parseLocalDate(string $value, string $timezone): ?CarbonImmutable
    {
        try {
            return CarbonImmutable::createFromFormat('Y-m-d', $value, $timezone);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Atualiza o campo de vencimento da missão e evita gravações desnecessárias.
     */
    private function updateMissionDueDate(Mission $mission, ?CarbonImmutable $localDate): bool
    {
        $current = $mission->due_at;

        if ($localDate === null) {
            if ($current === null) {
                return false;
            }

            $mission->due_at = null;
            $mission->save();

            return true;
        }

        $serverDate = $localDate->setTimezone(config('app.timezone'));

        if ($current && $current->equalTo($serverDate)) {
            return false;
        }

        $mission->due_at = $serverDate;
        $mission->save();

        return true;
    }

    /**
     * Renderiza a lista principal de missões com o estado atual.
     */
    public function render()
    {
        $user = Auth::user();

        if (! $user) {
            return view('livewire.tasks.main-panel', [
                'totalCount' => 0,
                'primaryGroupTitle' => 'All',
                'unlistedMissions' => collect(),
                'lists' => collect(),
                'availableLists' => collect(),
                'selectedMissionId' => null,
                'showListSelector' => true,
                'listView' => false,
            ]);
        }

        $timezone = $user->timezone ?? config('app.timezone');

        $lists = TaskList::query()
            ->with([
                'missions' => function ($query) use ($timezone) {
                    $query->orderBy('position')->orderBy('created_at');

                    if ($this->shortcut) {
                        MissionShortcutFilter::apply($query, $this->shortcut, $timezone);
                    }
                },
                'missions.checkpoints' => fn ($query) => $query->orderBy('position')->orderBy('created_at'),
            ])
            ->where('user_id', $user->id)
            ->whereNull('archived_at')
            ->orderByDesc('is_pinned')
            ->orderBy('position')
            ->orderBy('name')
            ->get();

        $unlistedMissions = Mission::query()
            ->where('user_id', $user->id)
            ->whereNull('list_id')
            ->orderBy('position')
            ->orderBy('created_at')
            ->with(['checkpoints' => fn ($query) => $query->orderBy('position')->orderBy('created_at')])
            ->when($this->shortcut, fn ($query) => MissionShortcutFilter::apply($query, $this->shortcut, $timezone))
            ->get();

        $lists->each(function ($list) {
            $this->attachCheckpointTree($list->missions);
        });

        $this->attachCheckpointTree($unlistedMissions);

        $totalCount = $lists->sum(fn ($list) => $list->missions->count()) + $unlistedMissions->count();

        if ($this->selectedMissionId) {
            $ownsMission = Mission::query()
                ->where('user_id', $user->id)
                ->when($this->currentListId, fn ($query) => $query->where('list_id', $this->currentListId))
                ->when($this->shortcut, fn ($query) => MissionShortcutFilter::apply($query, $this->shortcut, $timezone))
                ->where('id', $this->selectedMissionId)
                ->exists();

            if (! $ownsMission) {
                $this->selectedMissionId = null;
                $this->selectedSubtaskId = null;
            }
        }

        if ($this->selectedSubtaskId !== null) {
            $hasSubtask = Checkpoint::query()
                ->where('id', $this->selectedSubtaskId)
                ->whereHas('mission', fn ($query) => $query->where('user_id', $user->id))
                ->exists();

            if (! $hasSubtask) {
                $this->selectedSubtaskId = null;
            }
        }

        $primaryGroupTitle = $this->shortcut ? $this->labelForShortcut($this->shortcut) : 'All';
        $showListSelector = $this->currentListId === null;

        if ($this->currentListId) {
            $activeList = TaskList::query()
                ->with([
                    'missions' => function ($query) use ($timezone) {
                        $query->orderBy('position')->orderBy('created_at');

                        if ($this->shortcut) {
                            MissionShortcutFilter::apply($query, $this->shortcut, $timezone);
                        }
                    },
                    'missions.checkpoints' => fn ($query) => $query->orderBy('position')->orderBy('created_at'),
                ])
                ->where('user_id', $user->id)
                ->whereNull('archived_at')
                ->find($this->currentListId);

            if ($activeList) {
                $this->attachCheckpointTree($activeList->missions);

                $lists = collect([$activeList]);
                $unlistedMissions = collect();
                $totalCount = $activeList->missions->count();
                $primaryGroupTitle = $activeList->name;
            } else {
                $this->currentListId = null;
                $showListSelector = true;
            }
        }

        if ($showListSelector) {
            $primaryGroupTitle = $this->shortcut ? $this->labelForShortcut($this->shortcut) : 'All';
        }

        $availableLists = TaskList::query()
            ->where('user_id', $user->id)
            ->whereNull('archived_at')
            ->orderBy('name')
            ->get();

        return view('livewire.tasks.main-panel', [
            'totalCount' => $totalCount,
            'primaryGroupTitle' => $primaryGroupTitle,
            'unlistedMissions' => $unlistedMissions,
            'lists' => $lists,
            'availableLists' => $availableLists,
            'selectedMissionId' => $this->selectedMissionId,
            'selectedSubtaskId' => $this->selectedSubtaskId,
            'showListSelector' => $showListSelector,
            'listView' => $this->currentListId !== null,
            'maxSubtasks' => self::MAX_SUBTASKS,
        ]);
    }

    /**
     * Converte códigos de atalho em rótulos humanizados.
     */
    private function labelForShortcut(string $shortcut): string
    {
        return match ($shortcut) {
            MissionShortcutFilter::TODAY => 'Today',
            MissionShortcutFilter::TOMORROW => 'Tomorrow',
            MissionShortcutFilter::NEXT_SEVEN_DAYS => 'Next 7 Days',
            default => 'All',
        };
    }

    /**
     * Recupera uma subtarefa garantindo o vínculo com o usuário logado.
     */
    private function findUserCheckpoint(int $checkpointId, int $userId): ?Checkpoint
    {
        return Checkpoint::query()
            ->where('id', $checkpointId)
            ->whereHas('mission', fn ($query) => $query->where('user_id', $userId))
            ->first();
    }

    private function normalizeParentId($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    /**
     * Sincroniza a ordem das subtarefas após arrastar e soltar itens.
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
     * Detecta dinamicamente a coluna usada para relacionar subtarefas pai/filho.
     */
    private function checkpointParentColumn(): ?string
    {
        static $column;

        if ($column === null) {
            if (Schema::hasColumn('checkpoints', 'parent_id')) {
                $column = 'parent_id';
            } elseif (Schema::hasColumn('checkpoints', 'parent_checkpoint_id')) {
                $column = 'parent_checkpoint_id';
            } else {
                $column = '';
            }
        }

        return $column !== '' ? $column : null;
    }

    /**
     * Calcula a próxima posição disponível na hierarquia de subtarefas.
     */
    private function nextCheckpointPosition(int $missionId, ?int $parentId = null): int
    {
        $query = Checkpoint::query()->where('mission_id', $missionId);

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
     * Valida se a missão atingiu o limite máximo de subtarefas.
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
     * Anexa a árvore de subtarefas a cada missão retornada para a view.
     */
    private function attachCheckpointTree(Collection $missions): void
    {
        $missions->each(function ($mission) {
            $tree = $this->buildCheckpointTree(collect($mission->checkpoints ?? []));
            $mission->setRelation('checkpointTree', collect($tree));
        });
    }

    /**
     * Gera a estrutura hierárquica usada pela lista principal.
     */
    private function buildCheckpointTree(Collection $checkpoints): array
    {
        if ($checkpoints->isEmpty()) {
            return [];
        }

        $parentColumn = $this->checkpointParentColumn();

        $grouped = $checkpoints->groupBy(function ($checkpoint) use ($parentColumn) {
            $parentId = $parentColumn ? ($checkpoint->{$parentColumn} ?? null) : null;

            return $parentId === null ? '__root__' : (string) $parentId;
        });

        return $this->buildCheckpointBranch($grouped, null, 0);
    }

    /**
     * Percorre recursivamente os checkpoints agrupados para montar nós filhos.
     */
    private function buildCheckpointBranch(Collection $grouped, ?int $parentId, int $depth): array
    {
        $key = $parentId === null ? '__root__' : (string) $parentId;

        return $grouped->get($key, collect())->map(function ($checkpoint) use ($grouped, $depth, $parentId) {
            return [
                'id' => $checkpoint->id,
                'mission_id' => $checkpoint->mission_id,
                'parent_id' => $parentId,
                'title' => $checkpoint->title,
                'is_done' => (bool) $checkpoint->is_done,
                'position' => $checkpoint->position,
                'children' => $depth >= 6
                    ? []
                    : $this->buildCheckpointBranch($grouped, $checkpoint->id, $depth + 1),
            ];
        })->values()->toArray();
    }
}
