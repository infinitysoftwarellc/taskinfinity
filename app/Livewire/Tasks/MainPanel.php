<?php

namespace App\Livewire\Tasks;

use App\Models\Mission;
use App\Models\TaskList;
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

    public function mount(?int $currentListId = null): void
    {
        $this->currentListId = $currentListId;

        if ($this->currentListId) {
            $this->newTaskListId = $this->currentListId;
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
            $belongsToUser = TaskList::where('id', $listId)
                ->where('user_id', $user->id)
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

        $this->dispatch('tasks-updated');
        $this->dispatch('task-selected', id: $mission->id);
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
            ->find($missionId);

        if (! $mission) {
            return;
        }

        $this->selectedMissionId = $mission->id;
        $this->dispatch('task-selected', id: $mission->id);
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

        $lists = TaskList::query()
            ->with(['missions' => fn ($query) => $query->orderBy('position')->orderBy('created_at')])
            ->where('user_id', $user->id)
            ->orderBy('position')
            ->orderBy('name')
            ->get();

        $unlistedMissions = Mission::query()
            ->where('user_id', $user->id)
            ->whereNull('list_id')
            ->orderBy('position')
            ->orderBy('created_at')
            ->get();

        $totalCount = $lists->sum(fn ($list) => $list->missions->count()) + $unlistedMissions->count();

        if ($this->selectedMissionId) {
            $ownsMission = Mission::query()
                ->where('user_id', $user->id)
                ->when($this->currentListId, fn ($query) => $query->where('list_id', $this->currentListId))
                ->where('id', $this->selectedMissionId)
                ->exists();

            if (! $ownsMission) {
                $this->selectedMissionId = null;
            }
        }

        $primaryGroupTitle = 'All';
        $showListSelector = $this->currentListId === null;

        if ($this->currentListId) {
            $activeList = TaskList::query()
                ->with(['missions' => fn ($query) => $query->orderBy('position')->orderBy('created_at')])
                ->where('user_id', $user->id)
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
            $primaryGroupTitle = 'All';
        }

        $availableLists = TaskList::query()
            ->where('user_id', $user->id)
            ->orderBy('name')
            ->get();

        return view('livewire.tasks.main-panel', [
            'totalCount' => $totalCount,
            'primaryGroupTitle' => $primaryGroupTitle,
            'unlistedMissions' => $unlistedMissions,
            'lists' => $lists,
            'availableLists' => $availableLists,
            'selectedMissionId' => $this->selectedMissionId,
            'showListSelector' => $showListSelector,
            'listView' => $this->currentListId !== null,
        ]);
    }
}
