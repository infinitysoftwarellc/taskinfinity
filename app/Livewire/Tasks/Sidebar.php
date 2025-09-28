<?php

namespace App\Livewire\Tasks;

use App\Models\Mission;
use App\Models\Tag;
use App\Models\TaskList;
use App\Support\MissionShortcutFilter;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Sidebar extends Component
{
    protected $listeners = ['tasks-updated' => '$refresh'];

    public string $workspaceTitle = 'SOFTWAREINFINITY';

    public ?int $currentListId = null;

    public ?string $currentShortcut = null;

    public bool $showListForm = false;

    public string $newListName = '';

    public ?string $newListColor = null;

    public ?string $newListIcon = null;

    public bool $showTagForm = false;

    public string $newTagName = '';

    public string $newTagColor = '#7aa2ff';

    public function toggleListForm(): void
    {
        $this->showListForm = ! $this->showListForm;
    }

    public function createList(): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $validated = $this->validate(
            [
                'newListName' => 'required|string|max:255',
                'newListColor' => 'nullable|string|max:20',
                'newListIcon' => 'nullable|string|max:100',
            ],
            [],
            [
                'newListName' => 'nome',
                'newListColor' => 'cor',
                'newListIcon' => 'ícone',
            ]
        );

        $position = (int) TaskList::query()
            ->where('user_id', $user->id)
            ->max('position') + 1;

        TaskList::create([
            'user_id' => $user->id,
            'name' => trim($validated['newListName']),
            'color' => $validated['newListColor'] ?? null,
            'icon' => $validated['newListIcon'] ?? null,
            'position' => $position,
        ]);

        $this->reset(['newListName', 'newListColor', 'newListIcon']);
        $this->showListForm = false;

        $this->dispatch('tasks-updated');
    }

    public function toggleTagForm(): void
    {
        $this->showTagForm = ! $this->showTagForm;
    }

    public function createTag(): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $validated = $this->validate(
            [
                'newTagName' => 'required|string|max:100',
                'newTagColor' => 'nullable|string|max:20',
            ],
            [],
            [
                'newTagName' => 'nome da tag',
                'newTagColor' => 'cor',
            ]
        );

        Tag::updateOrCreate(
            [
                'user_id' => $user->id,
                'name' => trim($validated['newTagName']),
            ],
            [
                'color' => $validated['newTagColor'] ?? null,
            ]
        );

        $this->reset(['newTagName', 'newTagColor']);
        $this->newTagColor = '#7aa2ff';
        $this->showTagForm = false;
    }

    public function render()
    {
        $user = Auth::user();

        if (! $user) {
            return view('livewire.tasks.sidebar', [
                'shortcuts' => [],
                'workspaceExpanded' => true,
                'workspaceBadge' => 0,
                'workspaceTitle' => $this->workspaceTitle,
                'lists' => collect(),
                'tags' => collect(),
                'completedLabel' => 'Completed',
                'completedCount' => 0,
                'showListForm' => $this->showListForm,
                'showTagForm' => $this->showTagForm,
                'currentListId' => $this->currentListId,
            ]);
        }

        $missionQuery = Mission::query()->where('user_id', $user->id);

        $totalTasks = (clone $missionQuery)->count();
        $completedTasks = (clone $missionQuery)->where('status', 'done')->count();

        $timezone = $user->timezone ?? config('app.timezone');

        $todayTasks = MissionShortcutFilter::apply(
            (clone $missionQuery),
            MissionShortcutFilter::TODAY,
            $timezone
        )->count();

        $tomorrowTasks = MissionShortcutFilter::apply(
            (clone $missionQuery),
            MissionShortcutFilter::TOMORROW,
            $timezone
        )->count();

        $nextSevenDaysTasks = MissionShortcutFilter::apply(
            (clone $missionQuery),
            MissionShortcutFilter::NEXT_SEVEN_DAYS,
            $timezone
        )->count();

        $lists = TaskList::query()
            ->withCount('missions')
            ->where('user_id', $user->id)
            ->orderBy('position')
            ->orderBy('name')
            ->get();

        $shortcuts = [
            [
                'icon' => 'infinity',
                'label' => 'All',
                'count' => $totalTasks,
                'href' => route('tasks.index'),
                'active' => $this->currentListId === null && $this->currentShortcut === null,
            ],
            [
                'icon' => 'sun',
                'label' => 'Today',
                'count' => $todayTasks,
                'href' => route('tasks.index', ['shortcut' => MissionShortcutFilter::TODAY]),
                'active' => $this->currentShortcut === MissionShortcutFilter::TODAY,
            ],
            [
                'icon' => 'sunrise',
                'label' => 'Tomorrow',
                'count' => $tomorrowTasks,
                'href' => route('tasks.index', ['shortcut' => MissionShortcutFilter::TOMORROW]),
                'active' => $this->currentShortcut === MissionShortcutFilter::TOMORROW,
            ],
            [
                'icon' => 'calendar-days',
                'label' => 'Next 7 Days',
                'count' => $nextSevenDaysTasks,
                'href' => route('tasks.index', ['shortcut' => MissionShortcutFilter::NEXT_SEVEN_DAYS]),
                'active' => $this->currentShortcut === MissionShortcutFilter::NEXT_SEVEN_DAYS,
            ],
        ];

        $workspaceBadge = $lists->sum('missions_count');

        $tags = Tag::query()
            ->where('user_id', $user->id)
            ->orderBy('name')
            ->get();

        return view('livewire.tasks.sidebar', [
            'shortcuts' => $shortcuts,
            'workspaceExpanded' => true,
            'workspaceBadge' => $workspaceBadge,
            'workspaceTitle' => $this->workspaceTitle,
            'lists' => $lists,
            'tags' => $tags,
            'completedLabel' => 'Completed',
            'completedCount' => $completedTasks,
            'showListForm' => $this->showListForm,
            'showTagForm' => $this->showTagForm,
            'currentListId' => $this->currentListId,
        ]);
    }
}
