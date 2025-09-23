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

    $folder->taskLists()->create([
        'name' => $validated['name'],
        'user_id' => auth()->id(), // <-- Esta linha é a mais importante
    ]);

    return redirect()->route('webapp.folders.show', $folder)->with('success', 'Lista de tarefas criada com sucesso!');
}

    /**
     * Display the specified task list.
     */
    public function show(TaskList $taskList)
    {
        // ADICIONE ESTA LINHA PARA VER OS DADOS
        dd('ID do dono da lista:', $taskList->user_id, 'ID do usuário logado:', auth()->id());

        $this->authorize('view', $taskList);

        $taskList->load('tasks');

        return view('webapp.tasklists.show', compact('taskList'));
    }

    /**
     * Show the form for editing the specified task list.
     */
    public function edit(TaskList $taskList)
    {
        $this->authorize('update', $taskList);

        // View: resources/views/tasklists/edit.blade.php
        return view('tasklists.edit', compact('taskList'));
    }

    /**
     * Update the specified task list in storage.
     */
    public function update(Request $request, TaskList $taskList)
    {
        $this->authorize('update', $taskList);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $taskList->update($validated);

        return redirect()->route('webapp.folders.show', $taskList->folder_id)->with('success', 'Lista de tarefas atualizada com sucesso!');
    }

    /**
     * Remove the specified task list from storage.
     */
    public function destroy(TaskList $taskList)
    {
        $this->authorize('delete', $taskList);

        $folderId = $taskList->folder_id;
        $taskList->delete();

        return redirect()->route('webapp.folders.show', $folderId)->with('success', 'Lista de tarefas deletada com sucesso!');
    }
}
