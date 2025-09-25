<?php

namespace App\Http\Controllers\WebApp;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class GamerController extends Controller
{
    public function index(): View
    {
        return view('webapp.gamer.index');
    }
}
