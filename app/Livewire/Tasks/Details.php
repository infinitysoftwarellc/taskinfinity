<?php

namespace App\Livewire\Tasks;

use App\Models\Checkpoint;
use App\Models\Mission;
use App\Models\PomodoroSession;
use App\Models\TaskList;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

class Details extends Component
{
    public const MAX_SUBTASKS = MainPanel::MAX_SUBTASKS;

    public ?int $missionId = null;

    public ?array $mission = null;

    public array $missionTags = [];

    public bool $showDatePicker = false;

    public ?string $pickerCursorDate = null;

    public ?string $pickerSelectedDate = null;

    public ?string $menuDate = null;

    public bool $showMoveListMenu = false;

    public array $availableLists = [];

    public bool $showSubtaskForm = false;

    public string $newSubtaskTitle = '';

    public ?int $newSubtaskParentId = null;

    public ?string $newSubtaskParentLabel = null;

    public ?int $selectedSubtaskId = null;

    public bool $isEditingDescription = false;

    public string $descriptionDraft = '';

    #[On('task-selected')]
    public function loadMission(?int $missionId = null, ?int $checkpointId = null): void
    {
        if (! $missionId) {
            $this->missionId = null;
            $this->mission = null;
            $this->missionTags = [];
            $this->availableLists = [];
            $this->showMoveListMenu = false;
            $this->showSubtaskForm = false;
            $this->newSubtaskTitle = '';
            $this->newSubtaskParentId = null;
            $this->newSubtaskParentLabel = null;
            $this->menuDate = null;
            $this->selectedSubtaskId = null;
            $this->isEditingDescription = false;
            $this->descriptionDraft = '';

            return;
        }

        $user = Auth::user();

        if (! $user) {
            return;
        }

        $mission = Mission::query()
            ->with('list')
            ->with(['checkpoints' => fn ($query) => $query->orderBy('position')->orderBy('created_at')])
            ->withCount([
                'checkpoints',
                'checkpoints as checkpoints_done_count' => fn ($query) => $query->where('is_done', true),
                'attachments',
            ])
            ->where('user_id', $user->id)
            ->find($missionId);

        if (! $mission) {
            $this->missionId = null;
            $this->mission = null;
            $this->missionTags = [];
            $this->isEditingDescription = false;
            $this->descriptionDraft = '';

            return;
        }

        $timezone = $user->timezone ?? config('app.timezone');

        $this->missionId = $mission->id;
        $checkpoints = collect($mission->checkpoints ?? []);

        $subtasks = $this->buildCheckpointTree($checkpoints);

        $this->mission = [
            'id' => $mission->id,
            'title' => $mission->title,
            'description' => $mission->description,
            'status' => $mission->status,
            'list' => $mission->list?->name,
            'list_id' => $mission->list_id,
            'parent_title' => null,
            'created_at' => $mission->created_at?->copy()->setTimezone($timezone),
            'updated_at' => $mission->updated_at?->copy()->setTimezone($timezone),
            'due_at' => $mission->due_at?->copy()->setTimezone($timezone),
            'priority' => $mission->priority,
            'priority_label' => $this->priorityLabel($mission->priority),
            'is_starred' => (bool) $mission->is_starred,
            'xp_reward' => $mission->xp_reward,
            'checkpoints_total' => $mission->checkpoints_count ?? 0,
            'checkpoints_done' => $mission->checkpoints_done_count ?? 0,
            'attachments_count' => $mission->attachments_count ?? 0,
            'subtasks' => $subtasks,
            'active_subtask' => null,
            'active_subtask_path' => [],
        ];

        $labels = $mission->labels_json ?? [];
        if (is_array($labels)) {
            $this->missionTags = $labels;
        } else {
            $this->missionTags = [];
        }

        $this->pickerSelectedDate = $mission->due_at?->copy()->setTimezone($timezone)?->format('Y-m-d');
        $this->pickerCursorDate = $this->pickerSelectedDate
            ? CarbonImmutable::createFromFormat('Y-m-d', $this->pickerSelectedDate, $timezone)->startOfMonth()->format('Y-m-d')
            : CarbonImmutable::now($timezone)->startOfMonth()->format('Y-m-d');
        $this->showDatePicker = false;
        $this->menuDate = $this->pickerSelectedDate;
        $this->showMoveListMenu = false;
        $this->showSubtaskForm = false;
        $this->newSubtaskTitle = '';
        $this->newSubtaskParentId = null;
        $this->newSubtaskParentLabel = null;
        $this->isEditingDescription = false;
        $this->descriptionDraft = (string) ($mission->description ?? '');
        $this->applyActiveSubtaskContext($checkpointId);

        $this->availableLists = TaskList::query()
            ->where('user_id', $user->id)
            ->whereNull('archived_at')
            ->orderByDesc('is_pinned')
            ->orderBy('position')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($list) => [
                'id' => $list->id,
                'name' => $list->name,
                'is_current' => $list->id === $mission->list_id,
            ])
            ->values()
            ->toArray();
    }

    public function startDescriptionEdit(): void
    {
        if (! $this->missionId || ! is_array($this->mission)) {
            return;
        }

        $this->isEditingDescription = true;
        $this->descriptionDraft = (string) ($this->mission['description'] ?? '');
    }

    public function cancelDescriptionEdit(): void
    {
        $this->isEditingDescription = false;
        $this->descriptionDraft = is_array($this->mission)
            ? (string) ($this->mission['description'] ?? '')
            : '';
    }

    public function saveDescription(): void
    {
        if (! $this->missionId) {
            return;
        }

        $user = Auth::user();

        if (! $user) {
            return;
        }

        $mission = Mission::query()
            ->where('user_id', $user->id)
            ->find($this->missionId);

        if (! $mission) {
            return;
        }

        $description = (string) $this->descriptionDraft;
        $normalized = str_replace(["\r\n", "\r"], "\n", $description);

        if (trim($normalized) === '') {
            $mission->description = null;
        } else {
            $mission->description = $normalized;
        }

        $mission->save();

        $this->isEditingDescription = false;
        $this->descriptionDraft = $mission->description ?? '';

        $this->loadMission($mission->id, $this->selectedSubtaskId);
        $this->dispatch('tasks-updated');
    }

    #[On('tasks-updated')]
    public function refreshMission(): void
    {
        if ($this->missionId) {
            $activeSubtask = $this->selectedSubtaskId;
            $this->loadMission($this->missionId, $activeSubtask);
        }
    }

    public function selectCheckpoint(int $checkpointId): void
    {
        if (! $this->missionId) {
            return;
        }

        $exists = Checkpoint::query()
            ->where('id', $checkpointId)
            ->where('mission_id', $this->missionId)
            ->whereHas('mission', fn ($query) => $query->where('user_id', Auth::id()))
            ->exists();

        if (! $exists) {
            return;
        }

        if (! empty($this->mission)) {
            $this->applyActiveSubtaskContext($checkpointId);
        } else {
            $this->selectedSubtaskId = $checkpointId;
        }
    }

    public function toggleCheckpoint(int $checkpointId): void
    {
        if (! $this->missionId) {
            return;
        }

        $checkpoint = Checkpoint::query()
            ->where('id', $checkpointId)
            ->whereHas('mission', fn ($query) => $query
                ->where('id', $this->missionId)
                ->where('user_id', Auth::id()))
            ->first();

        if (! $checkpoint) {
            return;
        }

        $checkpoint->is_done = ! $checkpoint->is_done;
        $checkpoint->save();

        $desiredSubtask = $checkpoint->id;

        $this->loadMission($this->missionId, $desiredSubtask);

        $this->dispatch('tasks-updated');
    }

    public function render()
    {
        return view('livewire.tasks.details', [
            'mission' => $this->mission,
            'missionTags' => $this->missionTags,
            'pickerCalendar' => $this->mission ? $this->buildCalendar() : null,
            'selectedSubtaskId' => $this->selectedSubtaskId,
            'maxSubtasks' => self::MAX_SUBTASKS,
        ]);
    }

    private function priorityLabel(?int $priority): string
    {
        return match ($priority) {
            3 => 'Alta',
            2 => 'Média',
            1 => 'Baixa',
            default => 'Nenhuma',
        };
    }

    public function toggleDatePicker(): void
    {
        if (! $this->missionId) {
            return;
        }

        $this->showDatePicker = ! $this->showDatePicker;

        if ($this->showDatePicker) {
            $this->resolveCursor($this->userTimezone());
        }
    }

    public function closeDatePicker(): void
    {
        $this->showDatePicker = false;
    }

    public function toggleCompletion(): void
    {
        if (! $this->missionId) {
            return;
        }

        $mission = Mission::query()
            ->where('user_id', Auth::id())
            ->find($this->missionId);

        if (! $mission) {
            return;
        }

        $mission->status = $mission->status === 'done' ? 'active' : 'done';
        $mission->save();

        $this->loadMission($mission->id);
        $this->dispatch('tasks-updated');
    }

    public function setPriority(?int $priority): void
    {
        if (! $this->missionId) {
            return;
        }

        $value = in_array($priority, [1, 2, 3], true) ? $priority : 0;

        $mission = Mission::query()
            ->where('user_id', Auth::id())
            ->find($this->missionId);

        if (! $mission) {
            return;
        }

        $mission->priority = $value;
        $mission->save();

        $this->loadMission($mission->id);
        $this->dispatch('tasks-updated');
    }

    public function applyDueShortcut(string $shortcut): void
    {
        if (! $this->missionId) {
            return;
        }

        $timezone = $this->userTimezone();
        $today = CarbonImmutable::now($timezone)->startOfDay();

        $target = match ($shortcut) {
            'today' => $today,
            'tomorrow' => $today->addDay(),
            'next7' => $today->addDays(7),
            'clear' => null,
            default => null,
        };

        if ($target === null) {
            $this->clearDueDate();

            return;
        }

        $this->selectDueDate($target->format('Y-m-d'));
    }

    public function applyMenuDate(): void
    {
        if (! $this->missionId || ! $this->menuDate) {
            return;
        }

        $this->selectDueDate($this->menuDate);
    }

    public function movePicker(int $offset): void
    {
        if (! $this->missionId) {
            return;
        }

        $timezone = $this->userTimezone();
        $cursor = $this->resolveCursor($timezone)->addMonths($offset);
        $this->pickerCursorDate = $cursor->startOfMonth()->format('Y-m-d');
    }

    public function selectDueDate(?string $date): void
    {
        if (! $this->missionId || ! $date) {
            return;
        }

        $timezone = $this->userTimezone();

        try {
            $selectedLocal = CarbonImmutable::createFromFormat('Y-m-d', $date, $timezone);
        } catch (\Throwable) {
            return;
        }

        $mission = Mission::query()
            ->where('user_id', Auth::id())
            ->find($this->missionId);

        if (! $mission) {
            return;
        }

        $mission->due_at = $selectedLocal->setTimezone(config('app.timezone'));
        $mission->save();

        $this->pickerSelectedDate = $selectedLocal->format('Y-m-d');
        $this->pickerCursorDate = $selectedLocal->startOfMonth()->format('Y-m-d');
        $this->menuDate = $this->pickerSelectedDate;
        $this->closeDatePicker();

        $this->loadMission($mission->id);
        $this->dispatch('tasks-updated');
    }

    public function clearDueDate(): void
    {
        if (! $this->missionId) {
            return;
        }

        $mission = Mission::query()
            ->where('user_id', Auth::id())
            ->find($this->missionId);

        if (! $mission) {
            return;
        }

        $mission->due_at = null;
        $mission->save();

        $timezone = $this->userTimezone();
        $this->pickerSelectedDate = null;
        $this->pickerCursorDate = CarbonImmutable::now($timezone)->startOfMonth()->format('Y-m-d');
        $this->menuDate = null;

        $this->closeDatePicker();

        $this->loadMission($mission->id);
        $this->dispatch('tasks-updated');
    }

    public function toggleStar(): void
    {
        if (! $this->missionId) {
            return;
        }

        $mission = Mission::query()
            ->where('user_id', Auth::id())
            ->find($this->missionId);

        if (! $mission) {
            return;
        }

        $mission->is_starred = ! $mission->is_starred;
        $mission->save();

        $this->loadMission($mission->id);
        $this->dispatch('tasks-updated');
    }

    public function toggleMoveListMenu(): void
    {
        if (! $this->missionId) {
            return;
        }

        $this->showMoveListMenu = ! $this->showMoveListMenu;
    }

    public function closeMoveListMenu(): void
    {
        $this->showMoveListMenu = false;
    }

    public function moveToList(?int $listId): void
    {
        if (! $this->missionId) {
            return;
        }

        $user = Auth::user();

        if (! $user) {
            return;
        }

        $mission = Mission::query()
            ->where('user_id', $user->id)
            ->find($this->missionId);

        if (! $mission) {
            return;
        }

        if ($listId !== null) {
            $listExists = TaskList::query()
                ->where('user_id', $user->id)
                ->whereNull('archived_at')
                ->where('id', $listId)
                ->exists();

            if (! $listExists) {
                return;
            }
        }

        $mission->list_id = $listId;
        $mission->save();

        $this->showMoveListMenu = false;

        $this->loadMission($mission->id);
        $this->dispatch('tasks-updated');
    }

    public function openSubtaskForm(?int $parentId = null): void
    {
        if (! $this->missionId) {
            return;
        }

        if ($this->reachedSubtaskLimit($this->missionId, $parentId)) {
            return;
        }

        $this->newSubtaskParentId = $parentId;
        $this->newSubtaskParentLabel = $parentId ? $this->resolveSubtaskTitle($parentId) : null;
        $this->newSubtaskTitle = '';
        $this->showSubtaskForm = true;
    }

    public function cancelSubtaskForm(): void
    {
        $this->showSubtaskForm = false;
        $this->newSubtaskTitle = '';
        $this->newSubtaskParentId = null;
        $this->newSubtaskParentLabel = null;
    }

    public function saveSubtask(): void
    {
        if (! $this->missionId) {
            return;
        }

        $title = trim($this->newSubtaskTitle);

        if ($title === '') {
            return;
        }

        $mission = Mission::query()
            ->where('user_id', Auth::id())
            ->find($this->missionId);

        if (! $mission) {
            return;
        }

        if ($this->reachedSubtaskLimit($mission->id, $this->newSubtaskParentId)) {
            $this->showSubtaskForm = false;
            $this->newSubtaskTitle = '';
            $this->newSubtaskParentId = null;
            $this->newSubtaskParentLabel = null;

            return;
        }

        $payload = [
            'mission_id' => $mission->id,
            'title' => $title,
            'position' => $this->nextCheckpointPosition($mission->id, $this->newSubtaskParentId),
            'is_done' => false,
        ];

        if ($column = $this->checkpointParentColumn()) {
            $payload[$column] = $this->newSubtaskParentId;
        }

        Checkpoint::create($payload);

        $this->newSubtaskTitle = '';
        $this->showSubtaskForm = false;
        $this->newSubtaskParentId = null;
        $this->newSubtaskParentLabel = null;

        $this->loadMission($mission->id);
        $this->dispatch('tasks-updated');
    }

    public function duplicateMission(): void
    {
        if (! $this->missionId) {
            return;
        }

        $user = Auth::user();

        if (! $user) {
            return;
        }

        $mission = Mission::query()
            ->with('checkpoints')
            ->where('user_id', $user->id)
            ->find($this->missionId);

        if (! $mission) {
            return;
        }

        $clone = $mission->replicate();
        $clone->status = 'active';
        $clone->is_starred = false;
        $clone->position = $this->nextMissionPosition($user->id, $mission->list_id);
        $clone->title = $this->duplicatedTitle($mission->title);
        $clone->created_at = now();
        $clone->updated_at = now();
        $clone->save();

        $tree = $this->buildCheckpointTree(collect($mission->checkpoints ?? []));

        if ($tree !== []) {
            $this->replicateCheckpointBranch($tree, $clone->id, null);
        }

        $this->dispatch('tasks-updated');
    }

    public function deleteMission(): void
    {
        if (! $this->missionId) {
            return;
        }

        $mission = Mission::query()
            ->where('user_id', Auth::id())
            ->find($this->missionId);

        if (! $mission) {
            return;
        }

        $mission->delete();

        $this->missionId = null;
        $this->mission = null;
        $this->missionTags = [];
        $this->availableLists = [];
        $this->showMoveListMenu = false;
        $this->showDatePicker = false;
        $this->showSubtaskForm = false;
        $this->newSubtaskTitle = '';
        $this->pickerSelectedDate = null;
        $this->pickerCursorDate = null;
        $this->menuDate = null;

        $this->dispatch('tasks-updated');
        $this->dispatch('task-selected', null, null);
    }

    public function startPomodoro(): void
    {
        if (! $this->missionId) {
            return;
        }

        $user = Auth::user();

        if (! $user) {
            return;
        }

        $mission = Mission::query()
            ->where('user_id', $user->id)
            ->find($this->missionId);

        if (! $mission) {
            return;
        }

        $timezone = $this->userTimezone();
        $nowClient = CarbonImmutable::now($timezone);
        $nowServer = CarbonImmutable::now(config('app.timezone'));

        $session = PomodoroSession::create([
            'user_id' => $user->id,
            'mission_id' => $mission->id,
            'type' => 'focus',
            'started_at_client' => $nowClient,
            'client_timezone' => $timezone,
            'client_utc_offset_minutes' => $nowClient->utcOffset(),
            'started_at_server' => $nowServer,
            'created_at' => $nowServer,
            'duration_seconds' => 0,
            'pause_count' => 0,
            'pause_total_seconds' => 0,
        ]);

        $this->dispatch('pomodoro-started', [
            'sessionId' => $session->id,
            'missionId' => $mission->id,
        ]);
    }

    /**
     * Monta uma árvore de subtarefas respeitando a ordem e o limite de profundidade.
     */
    private function buildCheckpointTree(Collection $checkpoints): array
    {
        if ($checkpoints->isEmpty()) {
            return [];
        }

        $parentColumn = $this->checkpointParentColumn();

        $grouped = $checkpoints->groupBy(function ($checkpoint) use ($parentColumn) {
            $parentId = $parentColumn ? ($checkpoint->{$parentColumn} ?? null) : null;

            return $parentId === null ? '__root__' : (string) $parentId;
        });

        return $this->buildCheckpointBranch($grouped, null, 0);
    }

    private function checkpointParentColumn(): ?string
    {
        static $parentColumn;

        if ($parentColumn === null) {
            if (Schema::hasColumn('checkpoints', 'parent_id')) {
                $parentColumn = 'parent_id';
            } elseif (Schema::hasColumn('checkpoints', 'parent_checkpoint_id')) {
                $parentColumn = 'parent_checkpoint_id';
            } else {
                $parentColumn = '';
            }
        }

        return $parentColumn !== '' ? $parentColumn : null;
    }

    private function buildCheckpointBranch(Collection $grouped, ?int $parentId, int $depth): array
    {
        $key = $parentId === null ? '__root__' : (string) $parentId;

        return $grouped->get($key, collect())->map(function ($checkpoint) use ($grouped, $depth) {
            return [
                'id' => $checkpoint->id,
                'title' => $checkpoint->title,
                'is_done' => (bool) $checkpoint->is_done,
                'position' => $checkpoint->position,
                'xp_reward' => $checkpoint->xp_reward,
                'children' => $depth >= 6
                    ? []
                    : $this->buildCheckpointBranch($grouped, $checkpoint->id, $depth + 1),
            ];
        })->values()->toArray();
    }

    private function applyActiveSubtaskContext(?int $checkpointId): void
    {
        if (! is_array($this->mission)) {
            $this->selectedSubtaskId = null;

            return;
        }

        if ($checkpointId === null) {
            $this->selectedSubtaskId = null;
            $this->mission['active_subtask'] = null;
            $this->mission['active_subtask_path'] = [];
            $this->mission['parent_title'] = null;

            return;
        }

        $trail = $this->findSubtaskTrail($this->mission['subtasks'] ?? [], $checkpointId);

        if ($trail === null) {
            $this->selectedSubtaskId = null;
            $this->mission['active_subtask'] = null;
            $this->mission['active_subtask_path'] = [];
            $this->mission['parent_title'] = null;

            return;
        }

        $this->selectedSubtaskId = $checkpointId;
        $this->mission['active_subtask'] = $trail['node'];
        $this->mission['active_subtask_path'] = $trail['ancestors'];
        $this->mission['parent_title'] = $this->formatParentTitle($this->mission['title'] ?? '', $trail['ancestors']);
    }

    private function findSubtaskTrail(array $nodes, int $checkpointId, array $trail = []): ?array
    {
        foreach ($nodes as $node) {
            $currentTrail = [...$trail, $node];

            if (($node['id'] ?? null) === $checkpointId) {
                return [
                    'node' => $node,
                    'ancestors' => $trail,
                ];
            }

            if (! empty($node['children'])) {
                $found = $this->findSubtaskTrail($node['children'], $checkpointId, $currentTrail);

                if ($found !== null) {
                    return $found;
                }
            }
        }

        return null;
    }

    private function findSubtaskInTree(array $nodes, int $checkpointId): ?array
    {
        $trail = $this->findSubtaskTrail($nodes, $checkpointId);

        return $trail['node'] ?? null;
    }

    private function resolveSubtaskTitle(int $checkpointId): ?string
    {
        if (! is_array($this->mission)) {
            return null;
        }

        $node = $this->findSubtaskInTree($this->mission['subtasks'] ?? [], $checkpointId);

        if (! $node) {
            return null;
        }

        $title = trim((string) ($node['title'] ?? ''));

        return $title !== '' ? $title : 'Sem título';
    }

    private function formatParentTitle(?string $missionTitle, array $ancestors): ?string
    {
        $segments = [];

        $missionTitle = trim((string) $missionTitle);
        if ($missionTitle !== '') {
            $segments[] = $missionTitle;
        }

        foreach ($ancestors as $node) {
            $title = trim((string) ($node['title'] ?? ''));

            if ($title !== '') {
                $segments[] = $title;
            }
        }

        if ($segments === []) {
            return null;
        }

        return implode(' › ', $segments);
    }

    private function replicateCheckpointBranch(array $nodes, int $missionId, ?int $parentId, int $depth = 0): void
    {
        if ($depth >= 7) {
            return;
        }

        foreach ($nodes as $node) {
            $payload = [
                'mission_id' => $missionId,
                'title' => trim((string) ($node['title'] ?? '')),
                'position' => $node['position'] ?? 0,
                'is_done' => false,
                'xp_reward' => $node['xp_reward'] ?? null,
            ];

            if ($column = $this->checkpointParentColumn()) {
                $payload[$column] = $parentId;
            }

            $checkpoint = Checkpoint::create($payload);

            if (! empty($node['children'])) {
                $this->replicateCheckpointBranch($node['children'], $missionId, $checkpoint->id, $depth + 1);
            }
        }
    }

    private function buildCalendar(): array
    {
        $timezone = $this->userTimezone();
        $cursor = $this->resolveCursor($timezone);
        $today = CarbonImmutable::now($timezone)->startOfDay();
        $selected = null;

        if ($this->pickerSelectedDate) {
            try {
                $selected = CarbonImmutable::createFromFormat('Y-m-d', $this->pickerSelectedDate, $timezone);
            } catch (\Throwable) {
                $selected = null;
            }
        }

        $start = $cursor->startOfWeek(CarbonInterface::MONDAY);
        $end = $cursor->endOfMonth()->endOfWeek(CarbonInterface::SUNDAY);

        $date = $start;
        $weeks = [];
        $week = [];

        while ($date <= $end) {
            $week[] = [
                'date' => $date->format('Y-m-d'),
                'label' => $date->format('j'),
                'isCurrentMonth' => $date->month === $cursor->month,
                'isToday' => $date->isSameDay($today),
                'isSelected' => $selected ? $date->isSameDay($selected) : false,
            ];

            if (count($week) === 7) {
                $weeks[] = $week;
                $week = [];
            }

            $date = $date->addDay();
        }

        if ($week !== []) {
            $weeks[] = $week;
        }

        $label = $cursor
            ->locale(app()->getLocale() ?? 'en')
            ->translatedFormat('F Y');

        return [
            'label' => Str::title($label),
            'weeks' => $weeks,
            'weekDays' => $this->weekDays(),
            'hasSelected' => (bool) $this->pickerSelectedDate,
        ];
    }

    private function resolveCursor(string $timezone): CarbonImmutable
    {
        if ($this->pickerCursorDate) {
            try {
                return CarbonImmutable::createFromFormat('Y-m-d', $this->pickerCursorDate, $timezone)->startOfMonth();
            } catch (\Throwable) {
                // fallback below
            }
        }

        if ($this->pickerSelectedDate) {
            try {
                $cursor = CarbonImmutable::createFromFormat('Y-m-d', $this->pickerSelectedDate, $timezone)->startOfMonth();
                $this->pickerCursorDate = $cursor->format('Y-m-d');

                return $cursor;
            } catch (\Throwable) {
                // fallback below
            }
        }

        $cursor = CarbonImmutable::now($timezone)->startOfMonth();
        $this->pickerCursorDate = $cursor->format('Y-m-d');

        return $cursor;
    }

    private function userTimezone(): string
    {
        $user = Auth::user();

        return $user?->timezone ?? config('app.timezone');
    }

    private function weekDays(): array
    {
        return ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'];
    }

    private function nextMissionPosition(int $userId, ?int $listId): int
    {
        return (int) Mission::query()
            ->where('user_id', $userId)
            ->when(
                $listId,
                fn ($query) => $query->where('list_id', $listId),
                fn ($query) => $query->whereNull('list_id')
            )
            ->max('position') + 1;
    }

    private function nextCheckpointPosition(int $missionId, ?int $parentId = null): int
    {
        $query = Checkpoint::query()
            ->where('mission_id', $missionId);

        if ($column = $this->checkpointParentColumn()) {
            if ($parentId === null) {
                $query->whereNull($column);
            } else {
                $query->where($column, $parentId);
            }
        }

        $position = (int) $query->max('position');

        return $position + 1;
    }

    private function reachedSubtaskLimit(int $missionId, ?int $parentId = null): bool
    {
        $query = Checkpoint::query()->where('mission_id', $missionId);

        if ($column = $this->checkpointParentColumn()) {
            if ($parentId === null) {
                $query->whereNull($column);
            } else {
                $query->where($column, $parentId);
            }
        }

        return $query->count() >= self::MAX_SUBTASKS;
    }

    private function duplicatedTitle(?string $title): string
    {
        $base = trim((string) $title);

        if ($base === '') {
            $base = 'Sem título';
        }

        return sprintf('%s (Cópia)', $base);
    }
}
