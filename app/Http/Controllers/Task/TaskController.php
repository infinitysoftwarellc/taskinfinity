<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskList;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Show the form for creating a new task in a specific task list.
     */
    public function create(Request $request)
    {
        $taskList = TaskList::findOrFail($request->query('task_list_id'));
        $this->authorize('update', $taskList); // Can the user add tasks to this list?

        // View: resources/views/tasks/create.blade.php
        return view('task.create', compact('taskList'));
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high',
            'task_list_id' => 'required|exists:task_lists,id',
        ]);

        $taskList = TaskList::findOrFail($validated['task_list_id']);
        $this->authorize('update', $taskList);

        $taskList->tasks()->create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'due_date' => $validated['due_date'],
            'priority' => $validated['priority'],
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('webapp.tasklists.show', $taskList)->with('success', 'Tarefa criada com sucesso!');
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task)
    {
        $this->authorize('view', $task);
        
        // View: resources/views/tasks/show.blade.php
        return view('task.show', compact('task'));
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(Task $task)
    {
        $this->authorize('update', $task);

        // View: resources/views/tasks/edit.blade.php
        return view('task.edit', compact('task'));
    }

    /**
     * Update the specified task in storage.
     */
    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|string',
        ]);

        $task->update($validated);

        return redirect()->route('webapp.tasklists.show', $task->task_list_id)->with('success', 'Tarefa atualizada com sucesso!');
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $taskListId = $task->task_list_id;
        $task->delete();

        return redirect()->route('webapp.tasklists.show', $taskListId)->with('success', 'Tarefa deletada com sucesso!');
    }
}