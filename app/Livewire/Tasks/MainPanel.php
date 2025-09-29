<?php

namespace App\Livewire\Tasks;

use App\Models\Checkpoint;
use App\Models\Mission;
use App\Models\TaskList;
use App\Support\MissionShortcutFilter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class MainPanel extends Component
{
    protected $listeners = ['tasks-updated' => '$refresh'];

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

    public ?string $shortcut = null;

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
    }

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
    }

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
        ]);
    }

    private function labelForShortcut(string $shortcut): string
    {
        return match ($shortcut) {
            MissionShortcutFilter::TODAY => 'Today',
            MissionShortcutFilter::TOMORROW => 'Tomorrow',
            MissionShortcutFilter::NEXT_SEVEN_DAYS => 'Next 7 Days',
            default => 'All',
        };
    }
}
