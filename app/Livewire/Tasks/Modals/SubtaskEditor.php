<?php

namespace App\Livewire\Tasks\Modals;

use App\Livewire\Support\InteractsWithNotifications;
use App\Livewire\Tasks\MainPanel;
use App\Models\Checkpoint;
use App\Models\Mission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class SubtaskEditor extends Component
{
    use InteractsWithNotifications;

    public bool $open = true;

    public int $missionId;

    public ?int $parentId;

    public string $title = '';

    public array $parentOptions = [];

    public ?string $parentLabel = null;

    public int $maxSubtasks;

    public ?string $modalKey = null;

    public function mount(int $missionId, ?int $parentId = null, ?string $modalKey = null): void
    {
        $this->missionId = $missionId;
        $this->parentId = $parentId;
        $this->maxSubtasks = MainPanel::MAX_SUBTASKS;
        $this->modalKey = $modalKey;

        $this->hydrateParents();
    }

    public function render()
    {
        return view('livewire.tasks.modals.subtask-editor');
    }

    public function closeModal(): void
    {
        $this->open = false;
        $this->dispatch('subtask-editor-closed');
        if ($this->modalKey) {
            $this->dispatch('closeModal', $this->modalKey);
        } else {
            $this->dispatch('closeModal');
        }
    }

    public function save(): void
    {
        $userId = Auth::id();

        if (! $userId) {
            $this->notification()->error('Sessão expirada', 'Faça login novamente para continuar.');
            $this->closeModal();

            return;
        }

        $mission = Mission::query()
            ->where('user_id', $userId)
            ->find($this->missionId);

        if (! $mission) {
            $this->notification()->error('Tarefa não encontrada', 'Não foi possível localizar a tarefa selecionada.');
            $this->closeModal();

            return;
        }

        $validated = $this->validate([
            'title' => 'required|string|max:255',
            'parentId' => 'nullable|integer',
        ], [], [
            'title' => 'título da subtarefa',
        ]);

        $parentId = $validated['parentId'] ?? null;

        if ($parentId !== null && ! array_key_exists($parentId, $this->parentOptions)) {
            $parentId = null;
        }

        if ($this->reachedLimit($mission->id, $parentId)) {
            $this->notification()->warning('Limite atingido', 'Esse nível já possui o número máximo de subtarefas.');

            return;
        }

        $payload = [
            'mission_id' => $mission->id,
            'title' => trim($validated['title']),
            'is_done' => false,
            'position' => $this->nextPosition($mission->id, $parentId),
        ];

        if ($column = $this->parentColumn()) {
            $payload[$column] = $parentId;
        }

        $checkpoint = Checkpoint::create($payload);

        $this->notification()->success('Subtarefa criada', 'A subtarefa foi adicionada com sucesso.');

        $this->dispatch('tasks-updated');
        $this->dispatch('task-selected', $mission->id, $checkpoint->id);
        $this->closeModal();
    }

    private function hydrateParents(): void
    {
        $userId = Auth::id();

        if (! $userId) {
            $this->parentOptions = [];
            $this->parentLabel = null;

            return;
        }

        $exists = Mission::query()
            ->where('user_id', $userId)
            ->where('id', $this->missionId)
            ->exists();

        if (! $exists) {
            $this->parentOptions = [];
            $this->parentLabel = null;

            return;
        }

        $column = $this->parentColumn();

        $checkpoints = Checkpoint::query()
            ->where('mission_id', $this->missionId)
            ->orderBy('position')
            ->get(['id', 'title', $column]);

        $this->parentOptions = $checkpoints
            ->mapWithKeys(fn ($checkpoint) => [
                $checkpoint->id => $checkpoint->title ?: 'Sem título',
            ])
            ->all();

        $this->parentLabel = $this->parentId ? ($this->parentOptions[$this->parentId] ?? null) : null;
    }

    private function parentColumn(): ?string
    {
        if (Schema::hasColumn('checkpoints', 'parent_id')) {
            return 'parent_id';
        }

        if (Schema::hasColumn('checkpoints', 'parent_checkpoint_id')) {
            return 'parent_checkpoint_id';
        }

        return null;
    }

    private function reachedLimit(int $missionId, ?int $parentId = null): bool
    {
        $query = Checkpoint::query()->where('mission_id', $missionId);

        if ($column = $this->parentColumn()) {
            if ($parentId === null) {
                $query->whereNull($column);
            } else {
                $query->where($column, $parentId);
            }
        }

        return $query->count() >= $this->maxSubtasks;
    }

    private function nextPosition(int $missionId, ?int $parentId = null): int
    {
        $query = Checkpoint::query()->where('mission_id', $missionId);

        if ($column = $this->parentColumn()) {
            if ($parentId === null) {
                $query->whereNull($column);
            } else {
                $query->where($column, $parentId);
            }
        }

        return (int) $query->max('position') + 1;
    }
}
