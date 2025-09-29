<?php

namespace App\Livewire\Tasks;

use App\Models\Checkpoint;
use App\Models\Mission;
use App\Models\PomodoroSession;
use App\Models\TaskList;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

class Details extends Component
{
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

    #[On('task-selected')]
    public function loadMission(?int $missionId = null): void
    {
        if (! $missionId) {
            $this->missionId = null;
            $this->mission = null;
            $this->missionTags = [];
            $this->availableLists = [];
            $this->showMoveListMenu = false;
            $this->showSubtaskForm = false;
            $this->newSubtaskTitle = '';
            $this->menuDate = null;

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

            return;
        }

        $timezone = $user->timezone ?? config('app.timezone');

        $this->missionId = $mission->id;
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
            'subtasks' => ($mission->checkpoints ?? collect())->map(fn ($checkpoint) => [
                'id' => $checkpoint->id,
                'title' => $checkpoint->title,
                'is_done' => (bool) $checkpoint->is_done,
                'position' => $checkpoint->position,
                'xp_reward' => $checkpoint->xp_reward,
                'children' => [],
            ])->values()->toArray(),
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

    #[On('tasks-updated')]
    public function refreshMission(): void
    {
        if ($this->missionId) {
            $this->loadMission($this->missionId);
        }
    }

    public function render()
    {
        return view('livewire.tasks.details', [
            'mission' => $this->mission,
            'missionTags' => $this->missionTags,
            'pickerCalendar' => $this->mission ? $this->buildCalendar() : null,
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

    public function openSubtaskForm(): void
    {
        if (! $this->missionId) {
            return;
        }

        $this->showSubtaskForm = true;
    }

    public function cancelSubtaskForm(): void
    {
        $this->showSubtaskForm = false;
        $this->newSubtaskTitle = '';
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

        Checkpoint::create([
            'mission_id' => $mission->id,
            'title' => $title,
            'position' => $this->nextCheckpointPosition($mission->id),
            'is_done' => false,
        ]);

        $this->newSubtaskTitle = '';
        $this->showSubtaskForm = false;

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

        foreach ($mission->checkpoints as $checkpoint) {
            Checkpoint::create([
                'mission_id' => $clone->id,
                'title' => $checkpoint->title,
                'position' => $checkpoint->position,
                'is_done' => false,
                'xp_reward' => $checkpoint->xp_reward,
            ]);
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
        $this->dispatch('task-selected', null);
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

    private function nextCheckpointPosition(int $missionId): int
    {
        return (int) Checkpoint::query()
            ->where('mission_id', $missionId)
            ->max('position') + 1;
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
