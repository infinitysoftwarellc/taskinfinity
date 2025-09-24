<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TasksPage extends Component
{
    public string $pageTitle = '';
    public Collection $tasks;
    public $identifier;

    // Property for the new task form
    public string $newTaskDescription = '';

    /**
     * Mount the component and load initial data based on the route parameter.
     *
     * @param string|Project $identifier
     */
    public function mount($identifier): void
    {
        $this->identifier = $identifier;
        $this->loadTasks();
    }

    /**
     * Load tasks based on the identifier (filter string or Project model).
     */
    public function loadTasks(): void
    {
        if ($this->identifier instanceof Project) {
            $this->pageTitle = $this->identifier->name;
            $this->tasks = Task::where('project_id', $this->identifier->id)
                               ->where('user_id', Auth::id())
                               ->where('organization_id', auth()->user()->organization_id)
                               ->get();
        } else {
            $this->loadTasksByFilter($this->identifier);
        }
    }

    /**
     * Load tasks for static filters like 'inbox', 'today', etc.
     *
     * @param string $filter
     */
    public function loadTasksByFilter(string $filter): void
    {
        $this->pageTitle = ucfirst($filter); // Ex: "Inbox" or "Today"

        $query = Task::where('user_id', Auth::id())
                     ->where('organization_id', auth()->user()->organization_id);

        $this->tasks = match ($filter) {
            'today' => $query->whereDate('due_date', today())->get(),
            'upcoming' => $query->where('due_date', '>', today())->get(),
            'inbox' => $query->whereNull('project_id')->get(),
            default => collect(), // Returns an empty collection if the filter is invalid
        };
    }

    /**
     * Save a new task to the database.
     */
    public function saveTask(): void
    {
        $this->validate([
            'newTaskDescription' => 'required|string|max:1000',
        ]);

        Task::create([
            'description' => $this->newTaskDescription,
            'user_id' => Auth::id(),
            'organization_id' => auth()->user()->organization_id,
            // Assign project_id if the current page is a project
            'project_id' => ($this->identifier instanceof Project) ? $this->identifier->id : null,
        ]);

        // Reset the input field
        $this->reset('newTaskDescription');

        // Refresh the task list
        $this->loadTasks();
    }

    public function render()
    {
        return view('livewire.tasks-page');
    }
}