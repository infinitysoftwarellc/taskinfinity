<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Concerns\InteractsWithUserModels;
use App\Http\Controllers\Controller;
use App\Models\Mission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MissionController extends Controller
{
    use InteractsWithUserModels;

    public function index(Request $request)
    {
        $missions = Mission::where('user_id', $request->user()->id)
            ->orderBy('position')
            ->get();

        return response()->json($missions);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'list_id' => ['required', 'integer', Rule::exists('lists', 'id')->where(fn ($query) => $query->where('user_id', $request->user()->id))],
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'nullable|integer|min:0|max:2',
            'labels_json' => 'nullable|array',
            'labels_json.*' => 'string',
            'is_starred' => 'nullable|boolean',
            'status' => ['nullable', Rule::in(['active', 'done', 'archived'])],
            'position' => 'nullable|integer',
            'xp_reward' => 'nullable|integer',
        ]);

        $data['user_id'] = $request->user()->id;

        $mission = Mission::create($data);

        return response()->json($mission, 201);
    }

    public function show(Request $request, Mission $mission)
    {
        $this->guardUserModel($mission, $request);

        return response()->json($mission->load(['checkpoints', 'attachments']));
    }

    public function update(Request $request, Mission $mission)
    {
        $this->guardUserModel($mission, $request);

        $data = $request->validate([
            'list_id' => ['sometimes', 'integer', Rule::exists('lists', 'id')->where(fn ($query) => $query->where('user_id', $request->user()->id))],
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'nullable|integer|min:0|max:2',
            'labels_json' => 'nullable|array',
            'labels_json.*' => 'string',
            'is_starred' => 'nullable|boolean',
            'status' => ['nullable', Rule::in(['active', 'done', 'archived'])],
            'position' => 'nullable|integer',
            'xp_reward' => 'nullable|integer',
        ]);

        $mission->update($data);

        return response()->json($mission);
    }

    public function destroy(Request $request, Mission $mission)
    {
        $this->guardUserModel($mission, $request);

        $mission->delete();

        return response()->noContent();
    }
}
