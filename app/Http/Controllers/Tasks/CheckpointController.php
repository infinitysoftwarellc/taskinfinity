<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Concerns\InteractsWithUserModels;
use App\Http\Controllers\Controller;
use App\Models\Checkpoint;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CheckpointController extends Controller
{
    use InteractsWithUserModels;

    public function index(Request $request)
    {
        $query = Checkpoint::query()
            ->whereHas('mission', fn ($q) => $q->where('user_id', $request->user()->id))
            ->orderBy('position');

        if ($request->filled('mission_id')) {
            $query->where('mission_id', $request->integer('mission_id'));
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'mission_id' => ['required', 'integer', Rule::exists('missions', 'id')->where(fn ($query) => $query->where('user_id', $request->user()->id))],
            'title' => 'required|string|max:255',
            'is_done' => 'nullable|boolean',
            'position' => 'nullable|integer',
            'xp_reward' => 'nullable|integer',
        ]);

        $checkpoint = Checkpoint::create($data);

        return response()->json($checkpoint, 201);
    }

    public function show(Request $request, Checkpoint $checkpoint)
    {
        $this->guardUserModel($checkpoint->mission, $request);

        return response()->json($checkpoint);
    }

    public function update(Request $request, Checkpoint $checkpoint)
    {
        $this->guardUserModel($checkpoint->mission, $request);

        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'is_done' => 'nullable|boolean',
            'position' => 'nullable|integer',
            'xp_reward' => 'nullable|integer',
        ]);

        $checkpoint->update($data);

        return response()->json($checkpoint);
    }

    public function destroy(Request $request, Checkpoint $checkpoint)
    {
        $this->guardUserModel($checkpoint->mission, $request);

        $checkpoint->delete();

        return response()->noContent();
    }
}
