<?php

namespace App\Http\Controllers\Pomodoro;

use App\Http\Controllers\Concerns\InteractsWithUserModels;
use App\Http\Controllers\Controller;
use App\Models\PomodoroSession;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PomodoroSessionController extends Controller
{
    use InteractsWithUserModels;

    public function index(Request $request)
    {
        $query = PomodoroSession::where('user_id', $request->user()->id)
            ->orderByDesc('started_at_client');

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'mission_id' => ['nullable', 'integer', Rule::exists('missions', 'id')->where(fn ($query) => $query->where('user_id', $request->user()->id))],
            'type' => ['required', Rule::in(['work', 'break'])],
            'started_at_client' => 'required|date',
            'ended_at_client' => 'nullable|date',
            'client_timezone' => 'required|string|max:64',
            'client_utc_offset_minutes' => 'required|integer',
            'started_at_server' => 'required|date',
            'ended_at_server' => 'nullable|date',
            'duration_seconds' => 'nullable|integer',
            'pause_count' => 'nullable|integer',
            'pause_total_seconds' => 'nullable|integer',
            'notes' => 'nullable|string',
            'created_at' => 'nullable|date',
        ]);

        $data['user_id'] = $request->user()->id;

        $session = PomodoroSession::create($data);

        return response()->json($session, 201);
    }

    public function show(Request $request, PomodoroSession $pomodoroSession)
    {
        $this->guardUserModel($pomodoroSession, $request);

        return response()->json($pomodoroSession->load('pauses'));
    }

    public function update(Request $request, PomodoroSession $pomodoroSession)
    {
        $this->guardUserModel($pomodoroSession, $request);

        $data = $request->validate([
            'mission_id' => ['sometimes', 'nullable', 'integer', Rule::exists('missions', 'id')->where(fn ($query) => $query->where('user_id', $request->user()->id))],
            'type' => ['sometimes', Rule::in(['work', 'break'])],
            'started_at_client' => 'sometimes|date',
            'ended_at_client' => 'nullable|date',
            'client_timezone' => 'sometimes|string|max:64',
            'client_utc_offset_minutes' => 'sometimes|integer',
            'started_at_server' => 'sometimes|date',
            'ended_at_server' => 'nullable|date',
            'duration_seconds' => 'nullable|integer',
            'pause_count' => 'nullable|integer',
            'pause_total_seconds' => 'nullable|integer',
            'notes' => 'nullable|string',
            'created_at' => 'nullable|date',
        ]);

        $pomodoroSession->update($data);

        return response()->json($pomodoroSession);
    }

    public function destroy(Request $request, PomodoroSession $pomodoroSession)
    {
        $this->guardUserModel($pomodoroSession, $request);

        $pomodoroSession->delete();

        return response()->noContent();
    }
}
