<?php

namespace App\Http\Controllers\WebApp\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function index(): View
    {
        return view('webapp.admin.analytics.index');
    }
}
