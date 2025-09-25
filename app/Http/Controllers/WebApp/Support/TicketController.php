<?php

namespace App\Http\Controllers\WebApp\Support;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(): View
    {
        return view('webapp.support.tickets.index');
    }

    public function show(string $ticket): View
    {
        return view('webapp.support.tickets.show');
    }
}
