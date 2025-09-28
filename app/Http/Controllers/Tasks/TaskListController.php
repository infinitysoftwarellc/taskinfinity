<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Concerns\InteractsWithUserModels;
use App\Http\Controllers\Controller;
use App\Models\TaskList;
use Illuminate\Http\Request;

class TaskListController extends Controller
{
    use InteractsWithUserModels;

    public function index(Request $request)
    {
        $lists = TaskList::where('user_id', $request->user()->id)
            ->orderBy('position')
            ->get();

        return response()->json($lists);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:20',
            'icon' => 'nullable|string|max:100',
            'position' => 'nullable|integer',
            'archived_at' => 'nullable|date',
        ]);

        $data['user_id'] = $request->user()->id;

        $list = TaskList::create($data);

        return response()->json($list, 201);
    }

    public function show(Request $request, TaskList $taskList)
    {
        $this->guardUserModel($taskList, $request);

        return response()->json($taskList);
    }

    public function update(Request $request, TaskList $taskList)
    {
        $this->guardUserModel($taskList, $request);

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'color' => 'nullable|string|max:20',
            'icon' => 'nullable|string|max:100',
            'position' => 'nullable|integer',
            'archived_at' => 'nullable|date',
        ]);

        $taskList->update($data);

        return response()->json($taskList);
    }

    public function destroy(Request $request, TaskList $taskList)
    {
        $this->guardUserModel($taskList, $request);

        $taskList->delete();

        return response()->noContent();
    }
}
