<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Habit;
use Carbon\Carbon;

class HabitTracker extends Component
{
    public $habits;
    public $dates;
    public $newHabitName;
    public $newHabitFrequency = 'daily';
    public $newHabitStartDate;
    public $newHabitGoalDays = 30;

    public function mount()
    {
        $this->loadHabits();
        $this->dates = collect();
        for ($i = 0; $i < 7; $i++) {
            $this->dates->push(Carbon::today()->subDays($i));
        }
        $this->newHabitStartDate = Carbon::today()->toDateString();
    }

    public function loadHabits()
    {
        $this->habits = auth()->user()->habits()->with('completions')->get();
    }

    public function addHabit()
    {
        $this->validate([
            'newHabitName' => 'required|string|max:255',
            'newHabitFrequency' => 'required|string',
            'newHabitStartDate' => 'required|date',
            'newHabitGoalDays' => 'required|integer|min:1',
        ]);

        auth()->user()->habits()->create([
            'name' => $this->newHabitName,
            'frequency' => $this->newHabitFrequency,
            'start_date' => $this->newHabitStartDate,
            'goal_days' => $this->newHabitGoalDays,
        ]);

        $this->reset(['newHabitName', 'newHabitFrequency', 'newHabitStartDate', 'newHabitGoalDays']);
        $this->loadHabits();
    }

    public function toggleCompletion($habitId, $date)
    {
        $habit = Habit::find($habitId);
        $completion = $habit->completions()->where('completed_at', $date)->first();

        if ($completion) {
            $completion->delete();
        } else {
            $habit->completions()->create(['completed_at' => $date]);
        }

        $this->loadHabits();
    }

    public function render()
    {
        return view('livewire.habit-tracker');
    }
}