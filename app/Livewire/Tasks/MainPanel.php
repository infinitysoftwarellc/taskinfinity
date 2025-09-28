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
                ->where('id', $this->selectedMissionId)
                ->exists();

            if (! $ownsMission) {
                $this->selectedMissionId = null;
            }
        }

        return view('livewire.tasks.main-panel', [
            'totalCount' => $totalCount,
            'primaryGroupTitle' => 'All',
            'unlistedMissions' => $unlistedMissions,
            'lists' => $lists,
            'availableLists' => $lists,
            'selectedMissionId' => $this->selectedMissionId,
        ]);
    }
}
