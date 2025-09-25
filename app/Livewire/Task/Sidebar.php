<?php

namespace App\Livewire\Task;

use App\Models\Task;
use App\Models\TaskList;
use App\Models\TaskTag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Sidebar extends Component
{
    public array $views = [
        ['label' => 'All', 'slug' => 'all', 'count' => 0, 'icon' => 'M3.75 5.25h16.5M3.75 9.75h16.5M3.75 14.25h16.5M3.75 18.75h16.5'],
        ['label' => 'Today', 'slug' => 'today', 'count' => 0, 'icon' => 'M6 4.5h12m-12 6.75h12M9.75 3v3m4.5-3v3M9 15.75l2.25 2.25L15 13.5'],
        ['label' => 'Next 7 Days', 'slug' => 'next-7-days', 'count' => 0, 'icon' => 'M8.25 6.75h7.5M8.25 12h7.5M8.25 17.25H12m-9.75-9l9-6 9 6v9a3 3 0 01-3 3h-12a3 3 0 01-3-3z'],
    ];

    public string $filtersDescription = 'Display tasks filtered by list, date, priority, tag, and more.';

    public array $tags = [];

    public ?int $activeListId = null;

    public ?string $activeView = null;

    public bool $showCreateList = false;

    public array $form = [
        'name' => '',
        'view_mode' => 'list',
    ];

    public function mount(?int $activeListId = null, ?string $activeView = null): void
    {
        $this->activeListId = $activeListId;
        $this->activeView = $activeView;
    }

    public function updatedActiveListId(): void
    {
        if ($this->activeListId) {
            $this->activeView = null;
        }
    }

    public function updatedShowCreateList(): void
    {
        if ($this->showCreateList === false) {
            $this->resetForm();
        }
    }

    public function openList(int $listId)
    {
        $this->activeView = null;

        return $this->redirectRoute('tasks.lists.show', ['list' => $listId], navigate: true);
    }

    public function openView(string $view)
    {
        $view = strtolower($view);
        $availableViews = collect($this->views)->pluck('slug')->all();

        abort_unless(in_array($view, $availableViews, true), 404);

        $this->activeListId = null;
        $this->activeView = $view;

        $params = $view === 'all'
            ? []
            : ['view' => $view];

        return $this->redirectRoute('tasks.index', $params, navigate: true);
    }

    public function createList()
    {
        $userId = Auth::id();

        $data = $this->validate([
            'form.name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('lists', 'name')->where(fn ($query) => $query->where('user_id', $userId)),
            ],
            'form.view_mode' => ['required', Rule::in(['list', 'kanban', 'timeline'])],
        ]);

        $position = TaskList::query()
            ->where('user_id', $userId)
            ->max('position');

        $list = TaskList::create([
            'user_id' => $userId,
            'name' => $data['form']['name'],
            'view_mode' => $data['form']['view_mode'],
            'position' => (int) $position + 1,
        ]);

        $this->resetForm();
        $this->showCreateList = false;
        $this->activeListId = $list->id;

        session()->flash('task_lists.created', 'Lista criada com sucesso.');

        return $this->redirectRoute('tasks.lists.show', ['list' => $list->getRouteKey()], navigate: true);
    }

    public function resetForm(): void
    {
        $this->form = [
            'name' => '',
            'view_mode' => 'list',
        ];
    }

    public function render()
    {
        $userId = Auth::id();

        if ($userId) {
            $tasksQuery = Task::query()
                ->where('user_id', $userId);

            $this->views[0]['count'] = (clone $tasksQuery)->count();
            $this->views[1]['count'] = (clone $tasksQuery)
                ->whereDate('due_at', now()->toDateString())
                ->count();
            $this->views[2]['count'] = (clone $tasksQuery)
                ->whereBetween('due_at', [now()->startOfDay(), now()->addDays(7)->endOfDay()])
                ->count();
        }

        $lists = TaskList::query()
            ->where('user_id', $userId)
            ->withCount('tasks')
            ->orderBy('position')
            ->orderBy('name')
            ->get();

        if ($userId) {
            $this->tags = TaskTag::query()
                ->where('user_id', $userId)
                ->withCount('tasks')
                ->orderBy('name')
                ->get()
                ->map(function (TaskTag $tag) {
                    return [
                        'id' => $tag->id,
                        'label' => $tag->name,
                        'color' => $tag->color,
                        'tasks_count' => $tag->tasks_count,
                    ];
                })
                ->all();
        } else {
            $this->tags = [];
        }

        return view('livewire.task.sidebar', [
            'lists' => $lists,
            'views' => $this->views,
            'activeView' => $this->activeView,
        ]);
    }
}
