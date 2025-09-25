<?php

namespace App\Livewire\Habits;

use App\Models\Habit;
use App\Models\HabitEntry;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Grid extends Component
{
    public string $activeDate;

    public ?int $activeHabitId = null;

    public bool $showCreateForm = false;

    public array $form = [
        'name' => '',
        'schedule' => 'daily',
        'frequency' => 'daily_check_in',
        'goal' => 'achieve_it_all',
        'start_date' => '',
        'goal_days' => 'forever',
        'reminder' => 'others',
        'auto_popup' => false,
        'custom_days' => [],
        'goal_per_period' => null,
        'color' => '#22c55e',
    ];

    public array $frequencyOptions = [
        'daily_check_in' => 'Daily Check-in',
        'build_momentum' => 'Build momentum',
        'weekly_focus' => 'Weekly focus',
    ];

    public array $goalOptions = [
        'achieve_it_all' => 'Achieve it all',
        'stay_consistent' => 'Stay consistent',
        'feel_amazing' => 'Feel amazing',
    ];

    public array $goalDaysOptions = [
        'forever' => 'Forever',
        '30_days' => '30 days',
        '90_days' => '90 days',
    ];

    public array $reminderOptions = [
        'none' => 'Nenhum',
        'morning' => 'Morning',
        'afternoon' => 'Afternoon',
        'evening' => 'Evening',
        'others' => 'Others',
    ];

    public array $weekdays = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];

    public function mount(): void
    {
        $this->activeDate = now()->toDateString();
        $this->resetForm();
    }

    public function toggleCreateForm(): void
    {
        $this->showCreateForm = ! $this->showCreateForm;

        if ($this->showCreateForm === false) {
            $this->resetForm();
        }
    }

    public function selectHabit(int $habitId): void
    {
        $this->activeHabitId = $habitId;
    }

    public function goToPreviousMonth(): void
    {
        $this->activeDate = Carbon::parse($this->activeDate)->subMonth()->toDateString();
    }

    public function goToNextMonth(): void
    {
        $this->activeDate = Carbon::parse($this->activeDate)->addMonth()->toDateString();
    }

    public function goToCurrentMonth(): void
    {
        $this->activeDate = now()->toDateString();
    }

    public function createHabit(): void
    {
        $userId = Auth::id();

        $data = $this->validate($this->rules($userId));

        $habit = Habit::create([
            'user_id' => $userId,
            'name' => $data['form']['name'],
            'schedule' => $data['form']['schedule'],
            'frequency' => $data['form']['frequency'],
            'goal' => $data['form']['goal'],
            'start_date' => $data['form']['start_date'],
            'goal_days' => $data['form']['goal_days'],
            'reminder' => $data['form']['reminder'],
            'auto_popup' => (bool) ($data['form']['auto_popup'] ?? false),
            'custom_days' => $data['form']['schedule'] === 'custom'
                ? array_values(array_unique(array_map('intval', $data['form']['custom_days'])))
                : null,
            'goal_per_period' => $data['form']['schedule'] === 'weekly'
                ? ($data['form']['goal_per_period'] ?? null)
                : null,
            'color' => $data['form']['color'],
        ]);

        $this->activeHabitId = $habit->id;
        $this->toggleCreateForm();
        $this->dispatch('notify', __('Hábito criado com sucesso.'));
        $this->dispatch('$refresh');
    }

    public function deleteHabit(int $habitId): void
    {
        $userId = Auth::id();

        $habit = Habit::query()
            ->forUser($userId)
            ->findOrFail($habitId);

        $habit->delete();

        if ($this->activeHabitId === $habitId) {
            $this->activeHabitId = null;
        }

        $this->dispatch('notify', __('Hábito removido.'));
        $this->dispatch('$refresh');
    }

    public function toggleEntry(int $habitId, string $date): void
    {
        $userId = Auth::id();
        $habit = Habit::query()
            ->forUser($userId)
            ->findOrFail($habitId);

        $targetDate = Carbon::parse($date)->startOfDay();
        $today = now()->startOfDay();

        if ($habit->start_date instanceof Carbon && $targetDate->lt($habit->start_date->startOfDay())) {
            return;
        }

        if ($targetDate->greaterThan($today)) {
            return;
        }

        if (! $habit->isDueOn($targetDate)) {
            return;
        }

        $entry = HabitEntry::query()
            ->firstOrNew([
                'habit_id' => $habit->id,
                'entry_date' => $targetDate->toDateString(),
            ]);

        $entry->completed = ! $entry->completed;
        $entry->value = $entry->completed ? max(1, (int) $entry->value) : 0;
        $entry->save();

        $this->dispatch('$refresh');
    }

    public function render()
    {
        $userId = Auth::id();
        $today = now()->startOfDay();
        $activeDate = Carbon::parse($this->activeDate)->startOfDay();
        $recentStart = $today->copy()->subDays(6);
        $monthStart = $activeDate->copy()->startOfMonth();
        $monthEnd = $activeDate->copy()->endOfMonth();
        $calendarStart = $monthStart->copy()->startOfWeek(Carbon::SUNDAY);
        $calendarEnd = $monthEnd->copy()->endOfWeek(Carbon::SATURDAY);

        $habits = Habit::query()
            ->forUser($userId)
            ->orderByDesc('created_at')
            ->orderBy('name')
            ->get();

        if ($habits->isNotEmpty()) {
            if (! $this->activeHabitId || ! $habits->contains('id', $this->activeHabitId)) {
                $this->activeHabitId = $habits->first()->id;
            }
        } else {
            $this->activeHabitId = null;
        }

        $habitIds = $habits->pluck('id');

        $entriesByHabit = HabitEntry::query()
            ->whereIn('habit_id', $habitIds)
            ->orderBy('entry_date')
            ->get()
            ->groupBy('habit_id');

        $recentEntriesByHabit = $entriesByHabit->map(function (Collection $entries) use ($recentStart, $today) {
            return $entries
                ->filter(fn (HabitEntry $entry) => $entry->entry_date->betweenIncluded($recentStart, $today))
                ->keyBy(fn (HabitEntry $entry) => $entry->entry_date->toDateString());
        });

        $monthlyEntriesByHabit = $entriesByHabit->map(function (Collection $entries) use ($monthStart, $monthEnd) {
            return $entries
                ->filter(fn (HabitEntry $entry) => $entry->entry_date->betweenIncluded($monthStart, $monthEnd));
        });

        $activeHabit = $this->activeHabitId
            ? $habits->firstWhere('id', $this->activeHabitId)
            : null;

        $activeHabitEntries = $activeHabit
            ? ($entriesByHabit[$activeHabit->id] ?? collect())
            : collect();

        $calendarEntries = $activeHabitEntries
            ->filter(fn (HabitEntry $entry) => $entry->entry_date->betweenIncluded($calendarStart, $calendarEnd))
            ->keyBy(fn (HabitEntry $entry) => $entry->entry_date->toDateString());

        $calendarDays = collect(CarbonPeriod::create($calendarStart, $calendarEnd))
            ->map(fn (Carbon $date) => $date->copy());

        $calendarRows = $calendarDays->chunk(7);

        $recentDays = collect(range(0, 6))
            ->map(fn (int $offset) => $today->copy()->subDays(6 - $offset));

        $summaries = $habits->mapWithKeys(function (Habit $habit) use ($entriesByHabit, $monthlyEntriesByHabit) {
            $entries = $entriesByHabit[$habit->id] ?? collect();
            $monthEntries = $monthlyEntriesByHabit[$habit->id] ?? collect();

            return [$habit->id => [
                'current_streak' => $this->calculateCurrentStreak($habit, $entries),
                'monthly_completed' => $monthEntries->where('completed', true)->count(),
            ]];
        });

        $stats = $activeHabit
            ? $this->buildStats($activeHabit, $monthStart, $monthEnd, $activeHabitEntries)
            : null;

        return view('livewire.habits.grid', [
            'habits' => $habits,
            'recentEntriesByHabit' => $recentEntriesByHabit,
            'monthlyEntriesByHabit' => $monthlyEntriesByHabit,
            'calendarRows' => $calendarRows,
            'calendarEntries' => $calendarEntries,
            'recentDays' => $recentDays,
            'activeHabit' => $activeHabit,
            'stats' => $stats,
            'monthLabel' => $monthStart->translatedFormat('F Y'),
            'today' => $today,
            'summaries' => $summaries,
            'activeDate' => $activeDate,
            'frequencyOptions' => $this->frequencyOptions,
            'goalOptions' => $this->goalOptions,
            'goalDaysOptions' => $this->goalDaysOptions,
            'reminderOptions' => $this->reminderOptions,
        ]);
    }

    protected function rules(int $userId): array
    {
        $frequencyKeys = array_keys($this->frequencyOptions);
        $goalKeys = array_keys($this->goalOptions);
        $goalDaysKeys = array_keys($this->goalDaysOptions);
        $reminderKeys = array_keys($this->reminderOptions);

        return [
            'form.name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('habits', 'name')->where(fn ($query) => $query->where('user_id', $userId)),
            ],
            'form.schedule' => ['required', Rule::in(['daily', 'weekly', 'custom'])],
            'form.frequency' => ['required', Rule::in($frequencyKeys)],
            'form.goal' => ['required', Rule::in($goalKeys)],
            'form.start_date' => ['required', 'date'],
            'form.goal_days' => ['required', Rule::in($goalDaysKeys)],
            'form.reminder' => ['required', Rule::in($reminderKeys)],
            'form.auto_popup' => ['boolean'],
            'form.custom_days' => [
                Rule::requiredIf(fn () => $this->form['schedule'] === 'custom'),
                'array',
            ],
            'form.custom_days.*' => [
                'integer',
                Rule::in(range(0, 6)),
            ],
            'form.goal_per_period' => ['nullable', 'integer', 'min:1', 'max:31'],
            'form.color' => ['nullable', 'string', 'max:32'],
        ];
    }

    protected function resetForm(): void
    {
        $this->form = [
            'name' => '',
            'schedule' => 'daily',
            'frequency' => 'daily_check_in',
            'goal' => 'achieve_it_all',
            'start_date' => now()->toDateString(),
            'goal_days' => 'forever',
            'reminder' => 'others',
            'auto_popup' => false,
            'custom_days' => [],
            'goal_per_period' => null,
            'color' => '#22c55e',
        ];
    }

    protected function buildStats(Habit $habit, Carbon $monthStart, Carbon $monthEnd, Collection $entries): array
    {
        $completedThisMonth = $entries
            ->filter(fn (HabitEntry $entry) => $entry->completed && $entry->entry_date->betweenIncluded($monthStart, $monthEnd))
            ->count();

        $expected = $this->expectedCompletionsForMonth($habit, $monthStart, $monthEnd);
        $rate = $expected > 0 ? (int) round(($completedThisMonth / $expected) * 100) : null;

        $streaks = $this->calculateStreaks($habit, $entries);

        return [
            'monthly_completed' => $completedThisMonth,
            'monthly_rate' => $rate,
            'current_streak' => $streaks['current'],
            'longest_streak' => $streaks['longest'],
            'total_completed' => $entries->where('completed', true)->count(),
        ];
    }

    protected function expectedCompletionsForMonth(Habit $habit, Carbon $monthStart, Carbon $monthEnd): int
    {
        $startDate = $habit->start_date?->copy()->startOfDay();

        if ($startDate && $startDate->greaterThan($monthEnd)) {
            return 0;
        }

        $effectiveStart = $startDate && $startDate->greaterThan($monthStart)
            ? $startDate
            : $monthStart;

        $days = max(0, $effectiveStart->diffInDays($monthEnd) + 1);

        return match ($habit->schedule) {
            'daily' => $days,
            'weekly' => $habit->goal_per_period
                ? (int) ceil($days / 7) * (int) $habit->goal_per_period
                : $days,
            'custom' => collect(CarbonPeriod::create($monthStart, $monthEnd))
                ->filter(fn (Carbon $date) => $habit->isDueOn($date))
                ->count(),
            default => $days,
        };
    }

    protected function calculateStreaks(Habit $habit, Collection $entries): array
    {
        $startDate = $habit->start_date?->copy()->startOfDay()
            ?? $habit->created_at?->copy()->startOfDay()
            ?? now()->startOfDay();
        $today = now()->startOfDay();

        if ($startDate->greaterThan($today)) {
            return [
                'current' => 0,
                'longest' => 0,
            ];
        }

        $entryMap = $entries->keyBy(fn (HabitEntry $entry) => $entry->entry_date->toDateString());

        $current = 0;
        $longest = 0;

        $period = CarbonPeriod::create($startDate, $today);

        foreach ($period as $date) {
            if (! $habit->isDueOn($date)) {
                continue;
            }

            $entry = $entryMap[$date->toDateString()] ?? null;
            $completed = $entry?->completed ?? false;

            if ($completed) {
                $current++;
                $longest = max($longest, $current);
            } else {
                $current = 0;
            }
        }

        return [
            'current' => $current,
            'longest' => $longest,
        ];
    }

    protected function calculateCurrentStreak(Habit $habit, Collection $entries): int
    {
        $entryMap = $entries->keyBy(fn (HabitEntry $entry) => $entry->entry_date->toDateString());
        $currentDate = now()->startOfDay();
        $streak = 0;

        $lowerBound = $habit->start_date?->copy()->startOfDay()
            ?? $habit->created_at?->copy()->startOfDay()
            ?? $currentDate->copy()->subDays(30);

        while ($currentDate->greaterThanOrEqualTo($lowerBound)) {
            if (! $habit->isDueOn($currentDate)) {
                $currentDate->subDay();
                continue;
            }

            $entry = $entryMap[$currentDate->toDateString()] ?? null;

            if ($entry && $entry->completed) {
                $streak++;
                $currentDate->subDay();
                continue;
            }

            break;
        }

        return $streak;
    }
}
