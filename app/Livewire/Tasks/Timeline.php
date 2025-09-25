<?php

namespace App\Livewire\Tasks;

use App\Models\Task;
use App\Models\TaskList;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class Timeline extends Component
{
    /**
     * @var array<int, array{value:string,label:string}>
     */
    public array $timeframes = [
        ['value' => 'overdue', 'label' => 'Atrasadas'],
        ['value' => 'today', 'label' => 'Hoje'],
        ['value' => 'next-30-days', 'label' => 'Próximos 30 dias'],
        ['value' => 'no-due-date', 'label' => 'Sem prazo definido'],
    ];

    public ?int $listId = null;

    public string $timeframe = 'next-30-days';

    public string $search = '';

    protected $listeners = [
        'task-updated' => '$refresh',
        'task-tags-updated' => '$refresh',
        'task-lists-updated' => '$refresh',
    ];

    public function mount(?int $listId = null, ?string $timeframe = null): void
    {
        $this->listId = $listId;

        if ($timeframe && collect($this->timeframes)->pluck('value')->contains($timeframe)) {
            $this->timeframe = $timeframe;
        }
    }

    public function updatedTimeframe(string $value): void
    {
        if (! collect($this->timeframes)->pluck('value')->contains($value)) {
            $this->timeframe = 'next-30-days';
        }
    }

    public function updatedSearch(): void
    {
        $this->search = Str::of($this->search)->limit(120)->toString();
    }

    public function clearFilters(): void
    {
        $this->reset(['listId', 'timeframe', 'search']);
        $this->timeframe = 'next-30-days';
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
            return view('livewire.tasks.timeline', [
                'lists' => collect(),
                'groups' => collect(),
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

        $this->applyTimeframeFilter($tasksQuery);

        $tasks = $tasksQuery->get();

        $groups = $tasks->groupBy(function (Task $task) {
            return $task->due_at
                ? $task->due_at->copy()->startOfDay()->toDateString()
                : 'no-date';
        })->map(function (Collection $tasks, string $key) {
            if ($key === 'no-date') {
                return [
                    'key' => $key,
                    'label' => 'Sem prazo definido',
                    'date' => Carbon::create(9999, 1, 1),
                    'tasks' => $tasks,
                ];
            }

            $date = Carbon::createFromFormat('Y-m-d', $key)->startOfDay();
            $label = $date->format('d/m/Y');

            if ($date->isToday()) {
                $label = 'Hoje • ' . $label;
            } elseif ($date->isTomorrow()) {
                $label = 'Amanhã • ' . $label;
            } elseif ($date->isPast()) {
                $label = 'Atrasada • ' . $label;
            }

            return [
                'key' => $key,
                'label' => $label,
                'date' => $date,
                'tasks' => $tasks,
            ];
        })->values()->sortBy('date')->values();

        return view('livewire.tasks.timeline', [
            'lists' => $lists,
            'groups' => $groups,
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
            ->orderBy('due_at')
            ->orderBy('position')
            ->orderBy('created_at');
    }

    protected function applyTimeframeFilter(Builder $query): void
    {
        $today = now()->startOfDay();

        switch ($this->timeframe) {
            case 'overdue':
                $query->whereNotNull('due_at')
                    ->where('due_at', '<', $today);
                break;
            case 'today':
                $query->whereDate('due_at', $today);
                break;
            case 'no-due-date':
                $query->whereNull('due_at');
                break;
            default:
                $query->whereNotNull('due_at')
                    ->whereBetween('due_at', [$today, $today->copy()->addDays(30)->endOfDay()]);
                break;
        }
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
