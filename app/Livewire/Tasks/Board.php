<?php

namespace App\Livewire\Tasks;

use App\Models\Task;
use App\Models\TaskList;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class Board extends Component
{
    /**
     * @var array<int, array{status:string,label:string,description:string}>
     */
    public array $columns = [
        ['status' => 'todo', 'label' => 'A fazer', 'description' => 'Ideias e tarefas aguardando início.'],
        ['status' => 'doing', 'label' => 'Em progresso', 'description' => 'Tarefas em andamento agora.'],
        ['status' => 'done', 'label' => 'Concluídas', 'description' => 'Trabalhos finalizados e validados.'],
        ['status' => 'archived', 'label' => 'Arquivadas', 'description' => 'Itens guardados para referência futura.'],
    ];

    public ?int $listId = null;

    public string $search = '';

    protected $listeners = [
        'task-updated' => '$refresh',
        'task-tags-updated' => '$refresh',
        'task-lists-updated' => '$refresh',
    ];

    public function mount(?int $listId = null): void
    {
        $this->listId = $listId;
    }

    public function updatedSearch(): void
    {
        $this->search = Str::of($this->search)->limit(120)->toString();
    }

    public function clearFilters(): void
    {
        $this->reset(['listId', 'search']);
    }

    public function moveTask(int $taskId, string $status): void
    {
        $status = Str::of($status)->lower()->toString();

        if (! collect($this->columns)->pluck('status')->contains($status)) {
            return;
        }

        $task = $this->findTask($taskId);

        $task->update([
            'status' => $status,
            'completed_at' => $status === 'done'
                ? ($task->completed_at ?? now())
                : null,
        ]);

        $this->dispatch('task-updated');
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
            return view('livewire.tasks.board', [
                'lists' => collect(),
                'columns' => collect($this->columns)->map(function (array $column) {
                    return $column + ['tasks' => collect()];
                }),
            ]);
        }

        $lists = $this->lists($userId);

        $tasksQuery = $this->baseQuery($userId)
            ->with(['list', 'tags']);

        if ($this->listId) {
            $tasksQuery->where('list_id', $this->listId);
        }

        if ($this->search !== '') {
            $term = '%' . Str::of($this->search)->trim() . '%';

            $tasksQuery->where(function ($query) use ($term) {
                $query->where('title', 'like', $term)
                    ->orWhere('description', 'like', $term);
            });
        }

        $grouped = $tasksQuery
            ->get()
            ->groupBy('status');

        $columns = collect($this->columns)->map(function (array $column) use ($grouped) {
            return $column + ['tasks' => $grouped->get($column['status'], collect())];
        });

        return view('livewire.tasks.board', [
            'lists' => $lists,
            'columns' => $columns,
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
