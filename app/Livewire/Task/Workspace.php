<?php

namespace App\Livewire\Task;

use App\Models\Task;
use App\Models\TaskList;
use App\Models\TaskTag;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Workspace extends Component
{
    /**
     * Currently active high-level view (today, next7, inbox, etc.).
     */
    public string $activeView = 'all';

    /**
     * Currently selected list.
     */
    public ?int $activeListId = null;

    /**
     * Selected task for the detail panel.
     */
    public ?int $selectedTaskId = null;

    /**
     * Draft title for quick-add.
     */
    public string $newTaskTitle = '';

    /**
     * Collapsed state for task groups (no-date, completed, etc.).
     */
    public array $collapsedGroups = [];

    public function mount(): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $firstListId = TaskList::query()
            ->where('user_id', $user->id)
            ->orderBy('position')
            ->orderBy('name')
            ->value('id');

        $this->activeListId = $firstListId;
    }

    #[On('tasks-sidebar:view-selected')]
    public function setActiveView(string $view): void
    {
        $allowed = ['all', 'today', 'tomorrow', 'next7', 'inbox', 'summary'];

        if (! in_array($view, $allowed, true)) {
            return;
        }

        $this->activeView = $view;
        $this->selectedTaskId = null;
    }

    #[On('tasks-sidebar:list-selected')]
    public function setActiveList(int $listId): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $exists = TaskList::query()
            ->where('user_id', $user->id)
            ->where('id', $listId)
            ->exists();

        if (! $exists) {
            return;
        }

        $this->activeListId = $listId;
        $this->selectedTaskId = null;
    }

    public function toggleGroup(string $group): void
    {
        $allowed = ['no-date', 'completed'];

        if (! in_array($group, $allowed, true)) {
            return;
        }

        $this->collapsedGroups[$group] = ! ($this->collapsedGroups[$group] ?? false);
    }

    public function selectTask(int $taskId): void
    {
        $this->selectedTaskId = $taskId;
    }

    public function toggleTaskStatus(int $taskId): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        /** @var Task|null $task */
        $task = Task::query()
            ->where('user_id', $user->id)
            ->find($taskId);

        if (! $task) {
            return;
        }

        $isDone = $task->status === 'done';

        $task->forceFill([
            'status' => $isDone ? 'todo' : 'done',
            'completed_at' => $isDone ? null : now(),
        ])->save();

        if ($isDone && $this->selectedTaskId === $taskId) {
            $this->selectedTaskId = null;
        }
    }

    public function createTask(): void
    {
        $this->validate([
            'newTaskTitle' => ['required', 'string', 'max:255'],
        ], [
            'newTaskTitle.required' => 'Digite um título para criar a tarefa.',
        ]);

        $user = Auth::user();

        if (! $user) {
            return;
        }

        $listId = $this->activeListId;

        if (! $listId) {
            $listId = TaskList::query()
                ->where('user_id', $user->id)
                ->orderBy('position')
                ->orderBy('name')
                ->value('id');
        }

        if (! $listId) {
            return;
        }

        $task = Task::create([
            'user_id' => $user->id,
            'list_id' => $listId,
            'title' => trim($this->newTaskTitle),
            'status' => 'todo',
        ]);

        $this->reset('newTaskTitle');
        $this->selectedTaskId = $task->id;
    }

    public function render(): View
    {
        $user = Auth::user();

        $lists = $this->fetchLists($user?->id);
        $views = $this->buildViews($user?->id);
        $tags = $this->fetchTags($user?->id);

        $tasks = $this->fetchTasks($user?->id);
        $selectedTask = $this->resolveSelectedTask($tasks);

        $noDateTasks = $tasks->filter(fn (Task $task) => $task->status !== 'done' && $task->due_at === null);
        $completedTasks = $tasks->filter(fn (Task $task) => $task->status === 'done');

        return view('livewire.task.workspace', [
            'lists' => $lists,
            'views' => $views,
            'tags' => $tags,
            'tasks' => $tasks,
            'selectedTask' => $selectedTask,
            'noDateTasks' => $noDateTasks,
            'completedTasks' => $completedTasks,
            'filtersDescription' => 'Exiba tarefas por lista, data, prioridade e tags de forma rápida.',
        ]);
    }

    private function fetchLists(?int $userId): Collection
    {
        if (! $userId) {
            return collect();
        }

        return TaskList::query()
            ->where('user_id', $userId)
            ->withCount('tasks')
            ->orderBy('position')
            ->orderBy('name')
            ->get()
            ->map(fn (TaskList $list) => [
                'id' => $list->id,
                'name' => $list->name,
                'tasks_count' => $list->tasks_count,
            ]);
    }

    private function buildViews(?int $userId): array
    {
        if (! $userId) {
            return [];
        }

        $base = Task::query()->where('user_id', $userId);

        $today = Carbon::now();

        $allCount = (clone $base)->count();
        $todayCount = (clone $base)->whereDate('due_at', $today->toDateString())->count();
        $tomorrowCount = (clone $base)->whereDate('due_at', $today->copy()->addDay()->toDateString())->count();
        $next7Count = (clone $base)->whereBetween('due_at', [$today->startOfDay(), $today->copy()->addDays(7)->endOfDay()])->count();
        $inboxCount = (clone $base)->whereHas('list', fn ($query) => $query->where('name', 'Inbox'))->count();

        return [
            [
                'slug' => 'all',
                'label' => 'All',
                'icon' => 'M3 12h18',
                'count' => $allCount,
            ],
            [
                'slug' => 'today',
                'label' => 'Today',
                'icon' => 'M8 7h8M8 12h8M8 17h3',
                'count' => $todayCount,
            ],
            [
                'slug' => 'tomorrow',
                'label' => 'Tomorrow',
                'icon' => 'M5 12l2 2 4-4 4 4 4-4',
                'count' => $tomorrowCount,
            ],
            [
                'slug' => 'next7',
                'label' => 'Next 7 days',
                'icon' => 'M3 5h18M3 10h18M3 15h18M3 20h18',
                'count' => $next7Count,
            ],
            [
                'slug' => 'inbox',
                'label' => 'Inbox',
                'icon' => 'M4 6h16l-2 12H6L4 6z',
                'count' => $inboxCount,
            ],
            [
                'slug' => 'summary',
                'label' => 'Summary',
                'icon' => 'M4 19h16M4 5h16M9 9h6v6H9z',
                'count' => $allCount,
            ],
        ];
    }

    private function fetchTags(?int $userId): Collection
    {
        if (! $userId) {
            return collect();
        }

        return TaskTag::query()
            ->where('user_id', $userId)
            ->withCount('tasks')
            ->orderBy('name')
            ->get()
            ->map(fn (TaskTag $tag) => [
                'id' => $tag->id,
                'label' => $tag->name,
                'color' => $tag->color,
                'tasks_count' => $tag->tasks_count,
            ]);
    }

    private function fetchTasks(?int $userId): Collection
    {
        if (! $userId) {
            return collect();
        }

        $query = Task::query()
            ->with('list')
            ->where('user_id', $userId);

        if ($this->activeListId) {
            $query->where('list_id', $this->activeListId);
        }

        $now = Carbon::now();

        switch ($this->activeView) {
            case 'today':
                $query->whereDate('due_at', $now->toDateString());
                break;
            case 'tomorrow':
                $query->whereDate('due_at', $now->copy()->addDay()->toDateString());
                break;
            case 'next7':
                $query->whereBetween('due_at', [$now->copy()->startOfDay(), $now->copy()->addDays(7)->endOfDay()]);
                break;
            case 'inbox':
                $query->whereHas('list', fn ($q) => $q->where('name', 'Inbox'));
                break;
        }

        return $query
            ->orderByRaw("CASE WHEN status = 'done' THEN 1 ELSE 0 END")
            ->orderByRaw('COALESCE(due_at, created_at)')
            ->orderBy('position')
            ->orderBy('id')
            ->get();
    }

    private function resolveSelectedTask(Collection $tasks): ?Task
    {
        if ($this->selectedTaskId) {
            /** @var Task|null $matched */
            $matched = $tasks->firstWhere('id', $this->selectedTaskId);

            if ($matched) {
                return $matched;
            }

            $this->selectedTaskId = null;
        }

        return $tasks->first();
    }
}
