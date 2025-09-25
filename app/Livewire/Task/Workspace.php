<?php

namespace App\Livewire\Task;

use App\Models\Task;
use App\Models\TaskList;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Workspace extends Component
{
    protected bool $supportsHierarchy = false;

    protected array $availableViews = ['all', 'today', 'next-7-days'];

    public ?int $listId = null;

    public ?TaskList $list = null;

    public ?string $view = null;

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

    public function mount(?int $listId = null, ?string $view = null): void
    {
        $this->supportsHierarchy = Schema::hasColumn('tasks', 'parent_id')
            && Schema::hasColumn('tasks', 'depth');

        $this->listId = $listId;
        $this->view = $this->normalizeView($view);

        if ($this->view) {
            $this->listId = null;
        }

        $this->loadList();
    }

    public function updatedListId(): void
    {
        if ($this->listId) {
            $this->view = null;
        }

        $this->loadList();
    }

    public function updatedView(): void
    {
        $this->view = $this->normalizeView($this->view);

        if ($this->view) {
            $this->listId = null;
            $this->list = null;
        }
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

        $positionQuery = Task::query()
            ->where('list_id', $this->list->id);

        if ($this->supportsHierarchy) {
            $positionQuery->whereNull('parent_id');
        }

        $position = $positionQuery->max('position');

        $payload = [
            'user_id' => $userId,
            'list_id' => $this->list->id,
            'title' => $title,
            'status' => 'todo',
            'priority' => 'none',
            'position' => (int) $position + 1,
        ];

        if ($this->supportsHierarchy) {
            $payload['parent_id'] = null;
            $payload['depth'] = 0;
        }

        Task::create($payload);

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

        $query = Task::query()
            ->where('user_id', $userId)
            ->where('list_id', $this->list?->id);

        if ($this->supportsHierarchy) {
            $query->with('childrenRecursive');
        }

        $task = $query->findOrFail($taskId);

        if (! $this->supportsHierarchy) {
            $task->setRelation('childrenRecursive', collect());
        }

        return $task;
    }

    protected function filterTaskTree(Collection $tasks): Collection
    {
        if ($this->search === '') {
            return $tasks;
        }

        $term = Str::lower($this->search);

        return $tasks
            ->map(function (Task $task) use ($term) {
                $children = $task->relationLoaded('childrenRecursive')
                    ? $task->childrenRecursive
                    : collect();

                $children = $this->filterTaskTree($children);
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

    protected function normalizeView(?string $view): ?string
    {
        if (! $view) {
            return null;
        }

        $normalized = Str::of($view)->lower()->toString();

        return in_array($normalized, $this->availableViews, true)
            ? $normalized
            : null;
    }

    protected function viewMeta(string $view): array
    {
        return match ($view) {
            'today' => [
                'title' => 'Hoje',
                'description' => 'Tarefas com prazo para hoje.',
            ],
            'next-7-days' => [
                'title' => 'Próximos 7 dias',
                'description' => 'Acompanhe o que está previsto para a próxima semana.',
            ],
            default => [
                'title' => 'Todas as tarefas',
                'description' => 'Visualize todas as tarefas organizadas por lista.',
            ],
        };
    }

    protected function prepareTasks(Collection $tasks): Collection
    {
        if ($this->supportsHierarchy) {
            return $tasks;
        }

        return $tasks->map(function (Task $task) {
            return $task->setRelation('childrenRecursive', collect());
        });
    }

    protected function buildViewPayload(): ?array
    {
        $view = $this->normalizeView($this->view);

        if (! $view) {
            return null;
        }

        $meta = $this->viewMeta($view) + [
            'slug' => $view,
            'lists' => collect(),
            'tasks' => collect(),
        ];

        $userId = Auth::id();

        if (! $userId) {
            return $meta;
        }

        if ($view === 'all') {
            $lists = TaskList::query()
                ->where('user_id', $userId)
                ->orderBy('position')
                ->orderBy('name')
                ->get();

            if ($lists->isEmpty()) {
                return $meta;
            }

            $tasksQuery = Task::query()
                ->where('user_id', $userId)
                ->whereIn('list_id', $lists->pluck('id'))
                ->orderBy('position')
                ->orderBy('created_at');

            if ($this->supportsHierarchy) {
                $tasksQuery->whereNull('parent_id')
                    ->with(['childrenRecursive' => function ($query) {
                        $query->orderBy('position')->orderBy('created_at');
                    }]);
            } else {
                $tasksQuery->with('list');
            }

            $tasks = $tasksQuery->get();
            $tasks = $this->prepareTasks($tasks);
            $tasks = $this->filterTaskTree($tasks);

            $grouped = $tasks->groupBy('list_id');

            $meta['lists'] = $lists->map(function (TaskList $list) use ($grouped) {
                return [
                    'id' => $list->id,
                    'name' => $list->name,
                    'tasks' => $grouped->get($list->id, collect()),
                ];
            })->filter(function (array $list) {
                return $list['tasks']->isNotEmpty();
            })->values();

            return $meta;
        }

        $tasksQuery = Task::query()
            ->where('user_id', $userId)
            ->whereNotNull('due_at')
            ->orderBy('due_at')
            ->orderBy('created_at')
            ->with('list');

        if ($this->supportsHierarchy) {
            $tasksQuery->whereNull('parent_id')
                ->with(['childrenRecursive' => function ($query) {
                    $query->orderBy('position')->orderBy('created_at');
                }]);
        }

        if ($view === 'today') {
            $tasksQuery->whereDate('due_at', now()->toDateString());
        }

        if ($view === 'next-7-days') {
            $tasksQuery->whereBetween('due_at', [now()->startOfDay(), now()->addDays(7)->endOfDay()]);
        }

        $tasks = $tasksQuery->get();
        $tasks = $this->prepareTasks($tasks);
        $meta['tasks'] = $this->filterTaskTree($tasks);

        return $meta;
    }

    public function render()
    {
        $tasks = collect();
        $viewPayload = null;

        if ($this->list) {
            $this->list->loadCount('tasks');

            $userId = Auth::id();

            $tasksQuery = Task::query()
                ->where('user_id', $userId)
                ->where('list_id', $this->list->id)
                ->orderBy('position')
                ->orderBy('created_at');

            if ($this->supportsHierarchy) {
                $tasksQuery->whereNull('parent_id')
                    ->with(['childrenRecursive' => function ($query) {
                        $query->orderBy('position')->orderBy('created_at');
                    }]);
            }

            $tasks = $tasksQuery->get();
            $tasks = $this->prepareTasks($tasks);
            $tasks = $this->filterTaskTree($tasks);
        }

        if (! $this->list && $this->view) {
            $viewPayload = $this->buildViewPayload();
        }

        return view('livewire.task.workspace', [
            'tasks' => $tasks,
            'viewPayload' => $viewPayload,
        ]);
    }
}
