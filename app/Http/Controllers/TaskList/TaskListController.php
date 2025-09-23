<?php

namespace App\Http\Controllers\TaskList;

use App\Http\Controllers\Controller;
use App\Models\Folder;
use App\Models\TaskList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskListController extends Controller
{
    /**
     * Display a listing of the resource. 
     * NOTE: Usually, task lists are shown within the context of a folder.
     * This method can be used for an "all lists" view if needed.
     */
    public function index()
    {
        $taskLists = Auth::user()->taskLists()->with('folder')->latest()->get();

        // View: resources/views/tasklists/index.blade.php
        return view('tasklists.index', compact('taskLists'));
    }

    /**
     * Show the form for creating a new task list within a specific folder.
     */
    public function create(Request $request)
    {
        // We need to know which folder this list will belong to.
        $folder = Folder::findOrFail($request->query('folder_id'));

        // Authorize that the user can update the folder (which implies they can add lists to it)
        $this->authorize('update', $folder);

        // View: resources/views/tasklists/create.blade.php
        return view('tasklists.create', compact('folder'));
    }

    /**
     * Store a newly created task list in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'folder_id' => 'required|exists:folders,id',
        ]);

        $folder = Folder::findOrFail($validated['folder_id']);
        $this->authorize('update', $folder);

        auth()->user()->taskLists()->create([
            'name' => $validated['name'],
            'folder_id' => $folder->id,
        ]);

        return redirect()->route('webapp.folders.show', $folder)->with('success', 'Lista de tarefas criada com sucesso!');
    }

    /**
     * Display the specified task list.
     */
    public function show(TaskList $tasklist) // <-- CORRIGIDO AQUI
    {
        $this->authorize('view', $tasklist); // <-- CORRIGIDO AQUI

        $tasklist->load('tasks'); // <-- CORRIGIDO AQUI

        return view('tasklists.show', compact('tasklist')); // <-- CORRIGIDO AQUI
    }

    /**
     * Show the form for editing the specified task list.
     */
    public function edit(TaskList $tasklist) // <-- CORRIGIDO AQUI
    {
        $this->authorize('update', $tasklist); // <-- CORRIGIDO AQUI

        // View: resources/views/tasklists/edit.blade.php
        return view('tasklists.edit', compact('tasklist')); // <-- CORRIGIDO AQUI
    }

    /**
     * Update the specified task list in storage.
     */
    public function update(Request $request, TaskList $tasklist) // <-- CORRIGIDO AQUI
    {
        $this->authorize('update', $tasklist); // <-- CORRIGIDO AQUI

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $tasklist->update($validated); // <-- CORRIGIDO AQUI

        return redirect()->route('webapp.folders.show', $tasklist->folder_id)->with('success', 'Lista de tarefas atualizada com sucesso!'); // <-- CORRIGIDO AQUI
    }

    /**
     * Remove the specified task list from storage.
     */
    public function destroy(TaskList $tasklist) // <-- CORRIGIDO AQUI
    {
        $this->authorize('delete', $tasklist); // <-- CORRIGIDO AQUI

        $folderId = $tasklist->folder_id; // <-- CORRIGIDO AQUI
        $tasklist->delete(); // <-- CORRIGIDO AQUI

        return redirect()->route('webapp.folders.show', $folderId)->with('success', 'Lista de tarefas deletada com sucesso!');
    }
}