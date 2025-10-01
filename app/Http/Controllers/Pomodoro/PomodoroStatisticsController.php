<?php

namespace App\Http\Controllers\Pomodoro;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class PomodoroStatisticsController extends Controller
{
    public function __invoke(): View
    {
        return view('app.pomodoro.stats');
    }
}
