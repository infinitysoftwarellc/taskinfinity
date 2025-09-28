<?php

namespace App\Http\Controllers\Pomodoro;

use App\Http\Controllers\Concerns\InteractsWithUserModels;
use App\Http\Controllers\Controller;
use App\Models\PomodoroDailyStat;
use Illuminate\Http\Request;

class PomodoroDailyStatController extends Controller
{
    use InteractsWithUserModels;

    public function index(Request $request)
    {
        $query = PomodoroDailyStat::where('user_id', $request->user()->id)
            ->orderByDesc('date_local');

        if ($request->filled('from')) {
            $query->whereDate('date_local', '>=', $request->date('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('date_local', '<=', $request->date('to'));
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date_local' => 'required|date',
            'sessions_count' => 'nullable|integer',
            'work_seconds' => 'nullable|integer',
            'break_seconds' => 'nullable|integer',
        ]);

        $data['user_id'] = $request->user()->id;

        $stat = PomodoroDailyStat::create($data);

        return response()->json($stat, 201);
    }

    public function show(Request $request, PomodoroDailyStat $pomodoroDailyStat)
    {
        $this->guardUserModel($pomodoroDailyStat, $request);

        return response()->json($pomodoroDailyStat);
    }

    public function update(Request $request, PomodoroDailyStat $pomodoroDailyStat)
    {
        $this->guardUserModel($pomodoroDailyStat, $request);

        $data = $request->validate([
            'sessions_count' => 'nullable|integer',
            'work_seconds' => 'nullable|integer',
            'break_seconds' => 'nullable|integer',
        ]);

        $pomodoroDailyStat->update($data);

        return response()->json($pomodoroDailyStat);
    }

    public function destroy(Request $request, PomodoroDailyStat $pomodoroDailyStat)
    {
        $this->guardUserModel($pomodoroDailyStat, $request);

        $pomodoroDailyStat->delete();

        return response()->noContent();
    }
}
