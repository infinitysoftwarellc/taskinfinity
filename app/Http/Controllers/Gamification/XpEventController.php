<?php

// This controller orchestrates HTTP requests for the gamification area related to xp event.
namespace App\Http\Controllers\Gamification;

use App\Http\Controllers\Concerns\InteractsWithUserModels;
use App\Http\Controllers\Controller;
use App\Models\XpEvent;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class XpEventController extends Controller
{
    use InteractsWithUserModels;

    public function index(Request $request)
    {
        $query = XpEvent::where('user_id', $request->user()->id)
            ->orderByDesc('created_at');

        if ($request->filled('source')) {
            $query->where('source', $request->input('source'));
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'source' => ['required', Rule::in(['mission_complete', 'checkpoint_complete', 'pomodoro_complete', 'achievement', 'purchase_refund'])],
            'reference_id' => 'nullable|integer',
            'delta_xp' => 'required|integer',
            'note' => 'nullable|string',
            'created_at' => 'nullable|date',
        ]);

        $data['user_id'] = $request->user()->id;

        $event = XpEvent::create($data);

        return response()->json($event, 201);
    }

    public function show(Request $request, XpEvent $xpEvent)
    {
        $this->guardUserModel($xpEvent, $request);

        return response()->json($xpEvent);
    }
}
