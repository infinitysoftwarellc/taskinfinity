<?php

namespace App\Livewire\Tasks;

use App\Models\Mission;
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

    #[On('task-selected')]
    public function loadMission(?int $missionId = null): void
    {
        if (! $missionId) {
            $this->missionId = null;
            $this->mission = null;
            $this->missionTags = [];

            return;
        }

        $user = Auth::user();

        if (! $user) {
            return;
        }

        $mission = Mission::query()
            ->with('list')
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
            'title' => $mission->title,
            'description' => $mission->description,
            'status' => $mission->status,
            'list' => $mission->list?->name,
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

        $this->closeDatePicker();

        $this->loadMission($mission->id);
        $this->dispatch('tasks-updated');
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
}
