<?php

namespace App\Livewire\Habits;

use App\Models\Habit;
use App\Models\HabitCheckin;
use App\Models\HabitMonthlyStat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Livewire\Component;

class HabitTracker extends Component
{
    public int $year;

    public int $month;

    public ?int $selectedHabitId = null;

    /** @var array<int, string> */
    protected $listeners = [
        'habitCreated' => '$refresh',
        'habitUpdated' => '$refresh',
        'habitDeleted' => '$refresh',
        'habitCheckinStored' => '$refresh',
    ];

    public function mount(): void
    {
        $now = now();

        $this->year = (int) $now->year;
        $this->month = (int) $now->month;
    }

    public function selectHabit(int $habitId): void
    {
        $this->selectedHabitId = $habitId;
    }

    public function goToPreviousMonth(): void
    {
        $date = Carbon::create($this->year, $this->month, 1)->subMonth();

        $this->year = (int) $date->year;
        $this->month = (int) $date->month;
    }

    public function goToNextMonth(): void
    {
        $date = Carbon::create($this->year, $this->month, 1)->addMonth();

        $this->year = (int) $date->year;
        $this->month = (int) $date->month;
    }

    public function render()
    {
        $userId = auth()->id();

        if ($userId === null) {
            return view('livewire.habits.habit-tracker', [
                'habits' => collect(),
                'selectedHabit' => null,
                'monthStart' => Carbon::create($this->year, $this->month, 1),
                'monthEnd' => Carbon::create($this->year, $this->month, 1)->endOfMonth(),
                'monthlyCheckins' => collect(),
                'recentCheckins' => collect(),
                'monthStat' => null,
                'totalCheckins' => 0,
            ]);
        }

        $monthStart = Carbon::create($this->year, $this->month, 1)->startOfDay();
        $monthEnd = (clone $monthStart)->endOfMonth();

        /** @var Collection<int, Habit> $habits */
        $habits = Habit::query()
            ->where('user_id', $userId)
            ->orderBy('status')
            ->orderBy('name')
            ->with('streakCache')
            ->withCount(['checkins as checkins_this_month' => function ($query) use ($monthStart, $monthEnd) {
                $query->whereBetween('checked_on_local', [$monthStart->toDateString(), $monthEnd->toDateString()]);
            }])
            ->get();

        if ($habits->isNotEmpty() && (! $this->selectedHabitId || ! $habits->contains('id', $this->selectedHabitId))) {
            $this->selectedHabitId = $habits->first()->id;
        }

        $selectedHabit = $habits->firstWhere('id', $this->selectedHabitId);

        $monthlyCheckins = collect();
        $recentCheckins = collect();
        $monthStat = null;
        $totalCheckins = 0;

        if ($selectedHabit) {
            /** @var Collection<int, HabitCheckin> $monthlyCheckins */
            $monthlyCheckins = HabitCheckin::query()
                ->where('habit_id', $selectedHabit->id)
                ->whereBetween('checked_on_local', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->orderBy('checked_on_local')
                ->get();

            /** @var Collection<int, HabitCheckin> $recentCheckins */
            $recentCheckins = HabitCheckin::query()
                ->where('habit_id', $selectedHabit->id)
                ->orderByDesc('checked_on_local')
                ->limit(12)
                ->get();

            $monthStat = HabitMonthlyStat::query()
                ->where('habit_id', $selectedHabit->id)
                ->where('year', $this->year)
                ->where('month', $this->month)
                ->first();

            $totalCheckins = HabitCheckin::query()
                ->where('habit_id', $selectedHabit->id)
                ->count();
        }

        return view('livewire.habits.habit-tracker', [
            'habits' => $habits,
            'selectedHabit' => $selectedHabit,
            'monthStart' => $monthStart,
            'monthEnd' => $monthEnd,
            'monthlyCheckins' => $monthlyCheckins,
            'recentCheckins' => $recentCheckins,
            'monthStat' => $monthStat,
            'totalCheckins' => $totalCheckins,
        ]);
    }
}
