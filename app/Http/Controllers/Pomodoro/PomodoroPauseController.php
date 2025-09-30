<?php

// This controller orchestrates HTTP requests for the pomodoro area related to pomodoro pause.
namespace App\Http\Controllers\Pomodoro;

use App\Http\Controllers\Concerns\InteractsWithUserModels;
use App\Http\Controllers\Controller;
use App\Models\PomodoroPause;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PomodoroPauseController extends Controller
{
    use InteractsWithUserModels;

    public function index(Request $request)
    {
        $query = PomodoroPause::query()
            ->whereHas('session', fn ($q) => $q->where('user_id', $request->user()->id))
            ->orderBy('paused_at_client');

        if ($request->filled('session_id')) {
            $query->where('session_id', $request->integer('session_id'));
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'session_id' => ['required', 'integer', Rule::exists('pomodoro_sessions', 'id')->where(fn ($query) => $query->where('user_id', $request->user()->id))],
            'paused_at_client' => 'required|date',
            'resumed_at_client' => 'nullable|date',
            'duration_seconds' => 'nullable|integer',
        ]);

        $pause = PomodoroPause::create($data);

        return response()->json($pause, 201);
    }

    public function show(Request $request, PomodoroPause $pomodoroPause)
    {
        $this->guardUserModel($pomodoroPause->session, $request);

        return response()->json($pomodoroPause);
    }

    public function update(Request $request, PomodoroPause $pomodoroPause)
    {
        $this->guardUserModel($pomodoroPause->session, $request);

        $data = $request->validate([
            'paused_at_client' => 'sometimes|date',
            'resumed_at_client' => 'nullable|date',
            'duration_seconds' => 'nullable|integer',
        ]);

        $pomodoroPause->update($data);

        return response()->json($pomodoroPause);
    }

    public function destroy(Request $request, PomodoroPause $pomodoroPause)
    {
        $this->guardUserModel($pomodoroPause->session, $request);

        $pomodoroPause->delete();

        return response()->noContent();
    }
}
