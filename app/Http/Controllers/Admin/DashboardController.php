<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\DashboardStatistics;
use App\Models\User;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $statistics = DashboardStatistics::collect();
        $users = User::query()
            ->select(['id', 'name', 'email', 'role', 'created_at'])
            ->withCount(['missions', 'taskLists'])
            ->orderByDesc('created_at')
            ->get();

        return view('admin.dashboard', [
            'statistics' => $statistics,
            'users' => $users,
        ]);
    }
}
