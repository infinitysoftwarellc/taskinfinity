<?php

namespace App\Livewire\Task;

use App\Models\Task;
use App\Models\TaskList;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Sidebar extends Component
{
    public array $views = [
        ['label' => 'All', 'count' => 0, 'icon' => 'M3.75 5.25h16.5M3.75 9.75h16.5M3.75 14.25h16.5M3.75 18.75h16.5'],
        ['label' => 'Today', 'count' => 0, 'icon' => 'M6 4.5h12m-12 6.75h12M9.75 3v3m4.5-3v3M9 15.75l2.25 2.25L15 13.5'],
        ['label' => 'Next 7 Days', 'count' => 0, 'icon' => 'M8.25 6.75h7.5M8.25 12h7.5M8.25 17.25H12m-9.75-9l9-6 9 6v9a3 3 0 01-3 3h-12a3 3 0 01-3-3z'],
        ['label' => 'Inbox', 'count' => 0, 'icon' => 'M3.75 5.25h16.5a1.5 1.5 0 011.5 1.5v10.5a1.5 1.5 0 01-1.5 1.5h-16.5a1.5 1.5 0 01-1.5-1.5V6.75a1.5 1.5 0 011.5-1.5zm0 0L12 13.5l8.25-8.25'],
        ['label' => 'Summary', 'count' => 0, 'icon' => 'M4.5 6.75h15m-15 6h15M4.5 16.5h8.25'],
    ];

    public string $filtersDescription = 'Display tasks filtered by list, date, priority, tag, and more.';

    public array $tags = [
        ['label' => 'PRÓXIMAS TASKS', 'color' => 'bg-sky-500'],
        ['label' => 'FOLDERS', 'color' => 'bg-purple-500'],
        ['label' => 'PORTFOLIO GUILHERMEINFINITY', 'color' => 'bg-amber-500'],
        ['label' => 'PORTFOLIO SOFTWAREINFINITY', 'color' => 'bg-lime-500'],
    ];

    public ?int $activeListId = null;

    public bool $showCreateList = false;

    public array $form = [
        'name' => '',
        'view_mode' => 'list',
    ];

    public function mount(?int $activeListId = null): void
    {
        $this->activeListId = $activeListId;
    }

    public function updatedShowCreateList(): void
    {
        if ($this->showCreateList === false) {
            $this->resetForm();
        }
    }

    public function openList(int $listId)
    {
        return redirect()->route('tasks.lists.show', ['list' => $listId], navigate: true);
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

        return redirect()->route('tasks.lists.show', ['list' => $list->getRouteKey()], navigate: true);
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
            $this->views[3]['count'] = (clone $tasksQuery)
                ->whereNull('due_at')
                ->count();
            $this->views[4]['count'] = (clone $tasksQuery)
                ->where('status', 'done')
                ->count();
        }

        $lists = TaskList::query()
            ->where('user_id', $userId)
            ->withCount('tasks')
            ->orderBy('position')
            ->orderBy('name')
            ->get();

        return view('livewire.task.sidebar', [
            'lists' => $lists,
            'views' => $this->views,
        ]);
    }
}
