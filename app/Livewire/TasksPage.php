<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\TaskList; // Import the new model
use Livewire\Component;

class TasksPage extends Component
{
    public Project $project;
    public string $newListName = ''; // Property to hold the new list name

    // The mount method already receives the project, which is great.
    public function mount(Project $project)
    {
        $this->project = $project;
    }

    // Method to create a new list
    public function addList()
    {
        $this->validate([
            'newListName' => 'required|string|max:255',
        ]);

        // CORRECT WAY: Create the list through the project's relationship.
        // Eloquent will automatically set the 'project_id' for us.
        $this->project->taskLists()->create([
            'name' => $this->newListName,
            'user_id' => auth()->id(),
            'organization_id' => auth()->user()->organization_id,
        ]);

        $this->newListName = ''; // Reset the input field

        // Refresh the project model instance to load the new list into the collection.
        // This ensures the view updates correctly.
        $this->project->refresh();
    }

    public function render()
    {
        // Eager load the relationships for performance
        $this->project->load('taskLists.tasks');
        return view('livewire.tasks-page');
    }
}