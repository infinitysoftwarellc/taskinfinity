<?php

namespace App\Http\Controllers\WebApp;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(): View
    {
        return view('webapp.tasks.index');
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
