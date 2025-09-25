<?php

namespace App\Http\Controllers\WebApp;

use App\Http\Controllers\Controller;
use App\Models\TaskList;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $firstList = $user?->taskLists()
            ->orderBy('position')
            ->orderBy('name')
            ->first();

        if ($firstList) {
            return redirect()->route('tasks.lists.show', $firstList);
        }

        return view('webapp.tasks.index', [
            'list' => null,
        ]);
    }

    public function show(Request $request, TaskList $list): View
    {
        abort_if($list->user_id !== $request->user()->id, 404);

        return view('webapp.tasks.index', [
            'list' => $list,
        ]);
    }

    public function board(): View
    {
        return view('webapp.tasks.board');
    }

    public function timeline(): View
    {
        return view('webapp.tasks.timeline');
    }
}
