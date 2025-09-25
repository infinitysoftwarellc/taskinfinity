<?php

namespace App\Livewire\Task;

use App\Models\Task;
use App\Models\TaskList;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Workspace extends Component
{
    public ?int $listId = null;

    public ?TaskList $list = null;

    public string $search = '';

    public string $newTaskTitle = '';

    public bool $showEditor = false;

    public ?Task $editorTask = null;

    /**
     * @var array<string, mixed>
     */
    public array $editorForm = [
        'title' => '',
        'description' => '',
        'status' => 'todo',
        'priority' => 'none',
        'due_at' => null,
        'estimate_pomodoros' => 0,
        'pomodoros_done' => 0,
    ];

    public array $statusOptions = [
        ['value' => 'todo', 'label' => 'A fazer'],
        ['value' => 'doing', 'label' => 'Em progresso'],
        ['value' => 'done', 'label' => 'Concluída'],
        ['value' => 'archived', 'label' => 'Arquivada'],
    ];

    public array $priorityOptions = [
        ['value' => 'none', 'label' => 'Sem prioridade'],
        ['value' => 'low', 'label' => 'Baixa'],
        ['value' => 'med', 'label' => 'Média'],
        ['value' => 'high', 'label' => 'Alta'],
    ];

    protected $listeners = [
        'task-updated' => '$refresh',
        'open-task-editor' => 'openEditor',
    ];

    public function mount(?int $listId = null): void
    {
        $this->listId = $listId;
        $this->loadList();
    }

    public function updatedListId(): void
    {
        $this->loadList();
    }

    public function updatedSearch(): void
    {
        // Trigger re-render with updated search query.
    }

    public function createRootTask(): void
    {
        if (! $this->list) {
            return;
        }

        $userId = Auth::id();

        abort_unless($this->list->user_id === $userId, 403);

        $validated = $this->validate([
            'newTaskTitle' => ['required', 'string', 'max:255'],
        ], [
            'newTaskTitle.required' => 'Informe um nome para a tarefa.',
        ]);

        $title = trim($validated['newTaskTitle']);

        if ($title === '') {
            $this->addError('newTaskTitle', 'Informe um nome para a tarefa.');

            return;
        }

        $position = Task::query()
            ->where('list_id', $this->list->id)
            ->whereNull('parent_id')
            ->max('position');

        Task::create([
            'user_id' => $userId,
            'list_id' => $this->list->id,
            'parent_id' => null,
            'depth' => 0,
            'title' => $title,
            'status' => 'todo',
            'priority' => 'none',
            'position' => (int) $position + 1,
        ]);

        $this->newTaskTitle = '';
        $this->resetErrorBag('newTaskTitle');
        $this->loadList();
        $this->dispatch('task-updated');
    }

    public function openEditor(int $taskId): void
    {
        if (! $this->list) {
            return;
        }

        $task = $this->findTask($taskId);

        $this->editorTask = $task;
        $this->editorForm = [
            'title' => $task->title,
            'description' => $task->description ?? '',
            'status' => $task->status,
            'priority' => $task->priority,
            'due_at' => $task->due_at?->format('Y-m-d\TH:i'),
            'estimate_pomodoros' => $task->estimate_pomodoros,
            'pomodoros_done' => $task->pomodoros_done,
        ];

        $this->showEditor = true;
    }

    public function closeEditor(): void
    {
        $this->showEditor = false;
        $this->editorTask = null;
        $this->editorForm = [
            'title' => '',
            'description' => '',
            'status' => 'todo',
            'priority' => 'none',
            'due_at' => null,
            'estimate_pomodoros' => 0,
            'pomodoros_done' => 0,
        ];
    }

    public function saveEditor(): void
    {
        if (! $this->showEditor || ! $this->editorTask) {
            return;
        }

        $task = $this->findTask($this->editorTask->id);

        $validated = $this->validate([
            'editorForm.title' => ['required', 'string', 'max:255'],
            'editorForm.description' => ['nullable', 'string'],
            'editorForm.status' => ['required', Rule::in(array_column($this->statusOptions, 'value'))],
            'editorForm.priority' => ['required', Rule::in(array_column($this->priorityOptions, 'value'))],
            'editorForm.due_at' => ['nullable', 'date'],
            'editorForm.estimate_pomodoros' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'editorForm.pomodoros_done' => ['nullable', 'integer', 'min:0', 'max:1000'],
        ]);

        $payload = [
            'title' => trim($validated['editorForm']['title']),
            'description' => $validated['editorForm']['description'] ?? null,
            'status' => $validated['editorForm']['status'],
            'priority' => $validated['editorForm']['priority'],
            'estimate_pomodoros' => $validated['editorForm']['estimate_pomodoros'] ?? 0,
            'pomodoros_done' => $validated['editorForm']['pomodoros_done'] ?? 0,
        ];

        $timezone = config('app.timezone') ?? 'UTC';

        $dueAt = $validated['editorForm']['due_at']
            ? Carbon::parse($validated['editorForm']['due_at'], $timezone)
            : null;

        $payload['due_at'] = $dueAt;
        $payload['completed_at'] = $payload['status'] === 'done'
            ? ($task->completed_at ?? now())
            : null;

        $task->update($payload);

        $this->editorTask = $task->fresh();
        $this->showEditor = false;
        $this->dispatch('task-updated');
    }

    protected function loadList(): void
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

    protected function findTask(int $taskId): Task
    {
        $userId = Auth::id();

        return Task::query()
            ->where('user_id', $userId)
            ->where('list_id', $this->list?->id)
            ->with('childrenRecursive')
            ->findOrFail($taskId);
    }

    protected function filterTaskTree(Collection $tasks): Collection
    {
        if ($this->search === '') {
            return $tasks;
        }

        $term = Str::lower($this->search);

        return $tasks
            ->map(function (Task $task) use ($term) {
                $children = $this->filterTaskTree($task->childrenRecursive);
                $task->setRelation('childrenRecursive', $children);

                $matchesSelf = Str::contains(Str::lower($task->title), $term)
                    || Str::contains(Str::lower((string) $task->description), $term);

                if ($matchesSelf || $children->isNotEmpty()) {
                    return $task;
                }

                return null;
            })
            ->filter()
            ->values();
    }

    public function render()
    {
        $tasks = collect();

        if ($this->list) {
            $this->list->loadCount('tasks');

            $userId = Auth::id();

            $tasks = Task::query()
                ->where('user_id', $userId)
                ->where('list_id', $this->list->id)
                ->whereNull('parent_id')
                ->orderBy('position')
                ->orderBy('created_at')
                ->with(['childrenRecursive' => function ($query) {
                    $query->orderBy('position')->orderBy('created_at');
                }])
                ->get();

            $tasks = $this->filterTaskTree($tasks);
        }

        return view('livewire.task.workspace', [
            'tasks' => $tasks,
        ]);
    }
}
