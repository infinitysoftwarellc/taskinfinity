<?php

namespace App\Livewire\Tasks;

use App\Models\Task;
use App\Models\TaskList;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class ListView extends Component
{
    /**
     * @var array<int, array{value:string,label:string}>
     */
    public array $statusOptions = [
        ['value' => 'all', 'label' => 'Todas'],
        ['value' => 'todo', 'label' => 'A fazer'],
        ['value' => 'doing', 'label' => 'Em progresso'],
        ['value' => 'done', 'label' => 'Concluídas'],
        ['value' => 'archived', 'label' => 'Arquivadas'],
    ];

    public ?int $listId = null;

    public string $status = 'all';

    public string $search = '';

    protected $listeners = [
        'task-updated' => '$refresh',
        'task-tags-updated' => '$refresh',
    ];

    public function mount(?int $listId = null): void
    {
        $this->listId = $listId;
    }

    public function updatedStatus(string $value): void
    {
        $allowed = collect($this->statusOptions)->pluck('value')->all();

        if (! in_array($value, $allowed, true)) {
            $this->status = 'all';
        }
    }

    public function updatedSearch(): void
    {
        $this->search = Str::of($this->search)->limit(120)->toString();
    }

    public function clearFilters(): void
    {
        $this->reset(['listId', 'status', 'search']);
        $this->status = 'all';
    }

    public function toggleTaskDone(int $taskId): void
    {
        $task = $this->findTask($taskId);

        $status = $task->status === 'done' ? 'todo' : 'done';

        $task->update([
            'status' => $status,
            'completed_at' => $status === 'done'
                ? ($task->completed_at ?? now())
                : null,
        ]);

        $this->dispatch('task-updated');
    }

    public function render()
    {
        $userId = Auth::id();

        if (! $userId) {
            return view('livewire.tasks.list-view', [
                'lists' => collect(),
                'tasks' => collect(),
            ]);
        }

        $lists = $this->lists($userId);

        $tasksQuery = $this->baseQuery($userId)
            ->with(['list', 'tags']);

        if ($this->listId) {
            $tasksQuery->where('list_id', $this->listId);
        }

        if ($this->status !== 'all') {
            $tasksQuery->where('status', $this->status);
        }

        if ($this->search !== '') {
            $term = '%' . Str::of($this->search)->trim() . '%';

            $tasksQuery->where(function ($query) use ($term) {
                $query->where('title', 'like', $term)
                    ->orWhere('description', 'like', $term);
            });
        }

        $tasks = $tasksQuery->get();

        return view('livewire.tasks.list-view', [
            'lists' => $lists,
            'tasks' => $tasks,
        ]);
    }

    protected function lists(int $userId): Collection
    {
        return TaskList::query()
            ->where('user_id', $userId)
            ->orderBy('position')
            ->orderBy('name')
            ->get();
    }

    protected function baseQuery(int $userId): Builder
    {
        return Task::query()
            ->where('user_id', $userId)
            ->whereNull('parent_id')
            ->orderByRaw("FIELD(status, 'todo','doing','done','archived')")
            ->orderBy('position')
            ->orderBy('created_at');
    }

    protected function findTask(int $taskId): Task
    {
        $userId = Auth::id();

        return Task::query()
            ->where('user_id', $userId)
            ->where('id', $taskId)
            ->firstOrFail();
    }
}
