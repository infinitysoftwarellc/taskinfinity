<?php

namespace App\Livewire\Task;

use App\Models\Task;
use App\Models\TaskList;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Workspace extends Component
{
    public ?int $listId = null;

    public ?TaskList $list = null;

    public string $search = '';

    public array $statusMeta = [
        'todo' => ['label' => 'A fazer', 'badge' => 'bg-amber-500/20 text-amber-200 border border-amber-400/40'],
        'doing' => ['label' => 'Em progresso', 'badge' => 'bg-sky-500/20 text-sky-100 border border-sky-400/40'],
        'done' => ['label' => 'Concluídas', 'badge' => 'bg-emerald-500/20 text-emerald-100 border border-emerald-400/40'],
        'archived' => ['label' => 'Arquivadas', 'badge' => 'bg-slate-500/20 text-slate-100 border border-slate-400/40'],
    ];

    public function mount(?int $listId = null): void
    {
        $this->listId = $listId;
        $this->hydrateList();
    }

    public function updatedListId(): void
    {
        $this->hydrateList();
    }

    public function updatedSearch(): void
    {
        // Trigger re-render with updated search query.
    }

    protected function hydrateList(): void
    {
        if (! $this->listId) {
            $this->list = null;

            return;
        }

        $userId = Auth::id();

        $this->list = TaskList::query()
            ->where('user_id', $userId)
            ->withCount('tasks')
            ->findOrFail($this->listId);
    }

    public function render()
    {
        $taskGroups = collect();

        if ($this->list) {
            $userId = Auth::id();

            $tasks = Task::query()
                ->where('user_id', $userId)
                ->where('list_id', $this->list->id)
                ->when($this->search, fn ($query) => $query->where('title', 'like', '%' . $this->search . '%'))
                ->orderBy('position')
                ->orderBy('due_at')
                ->orderBy('created_at')
                ->get();

            $taskGroups = $tasks->groupBy('status');
        }

        return view('livewire.task.workspace', [
            'taskGroups' => $taskGroups,
            'statusMeta' => $this->statusMeta,
        ]);
    }
}
