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

    // Novas propriedades para edição
    public $editingHabitId = null;
    public $editingHabitName;
    public $editingHabitFrequency;
    public $editingHabitStartDate;
    public $editingHabitGoalDays;


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

    // --- NOVOS MÉTODOS ---

    /**
     * Prepara o componente para editar um hábito.
     */
    public function editHabit($habitId)
    {
        $habit = Habit::findOrFail($habitId);
        $this->editingHabitId = $habit->id;
        $this->editingHabitName = $habit->name;
        $this->editingHabitFrequency = $habit->frequency;
        $this->editingHabitStartDate = Carbon::parse($habit->start_date)->format('Y-m-d');
        $this->editingHabitGoalDays = $habit->goal_days;
    }

    /**
     * Atualiza o hábito no banco de dados.
     */
    public function updateHabit()
    {
        $this->validate([
            'editingHabitName' => 'required|string|max:255',
            'editingHabitFrequency' => 'required|string',
            'editingHabitStartDate' => 'required|date',
            'editingHabitGoalDays' => 'required|integer|min:1',
        ]);

        $habit = Habit::findOrFail($this->editingHabitId);
        $habit->update([
            'name' => $this->editingHabitName,
            'frequency' => $this->editingHabitFrequency,
            'start_date' => $this->editingHabitStartDate,
            'goal_days' => $this->editingHabitGoalDays,
        ]);

        $this->cancelEdit(); // Reseta o estado de edição
        $this->loadHabits();
    }

    /**
     * Cancela o modo de edição.
     */
    public function cancelEdit()
    {
        $this->reset(['editingHabitId', 'editingHabitName', 'editingHabitFrequency', 'editingHabitStartDate', 'editingHabitGoalDays']);
    }

    /**
     * Remove um hábito.
     */
    public function deleteHabit($habitId)
    {
        $habit = Habit::findOrFail($habitId);
        $habit->delete();
        $this->loadHabits();
    }


    public function render()
    {
        return view('livewire.habit-tracker');
    }
}