<?php

namespace App\Http\Controllers\WebApp;

use App\Http\Controllers\Controller;
use App\Models\TaskList;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $view = (string) $request->query('view', 'all');

        $availableViews = ['all', 'today', 'next-7-days'];

        if (! in_array($view, $availableViews, true)) {
            $view = 'all';
        }

        return view('webapp.tasks.index', [
            'list' => null,
            'view' => $view,
        ]);
    }

    public function show(Request $request, TaskList $list): View
    {
        abort_if($list->user_id !== $request->user()->id, 404);

        return view('webapp.tasks.index', [
            'list' => $list,
            'view' => null,
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
