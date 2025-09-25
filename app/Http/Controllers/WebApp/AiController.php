<?php

namespace App\Http\Controllers\WebApp;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AiController extends Controller
{
    public function plan(): View
    {
        return view('webapp.ai.plan');
    }

    public function autolabel(): View
    {
        return view('webapp.ai.autolabel');
    }

    public function split(): View
    {
        return view('webapp.ai.split');
    }
}
