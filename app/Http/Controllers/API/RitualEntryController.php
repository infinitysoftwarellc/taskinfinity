<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Concerns\InteractsWithUserModels;
use App\Http\Controllers\Controller;
use App\Models\RitualEntry;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RitualEntryController extends Controller
{
    use InteractsWithUserModels;

    public function index(Request $request)
    {
        $query = RitualEntry::query()
            ->whereHas('ritual', fn ($q) => $q->where('user_id', $request->user()->id))
            ->orderByDesc('date_local');

        if ($request->filled('ritual_id')) {
            $query->where('ritual_id', $request->integer('ritual_id'));
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'ritual_id' => ['required', 'integer', Rule::exists('rituals', 'id')->where(fn ($query) => $query->where('user_id', $request->user()->id))],
            'date_local' => 'required|date',
            'completed' => 'nullable|boolean',
            'created_at' => 'nullable|date',
        ]);

        $entry = RitualEntry::create($data);

        return response()->json($entry, 201);
    }

    public function show(Request $request, RitualEntry $ritualEntry)
    {
        $this->guardUserModel($ritualEntry->ritual, $request);

        return response()->json($ritualEntry);
    }

    public function update(Request $request, RitualEntry $ritualEntry)
    {
        $this->guardUserModel($ritualEntry->ritual, $request);

        $data = $request->validate([
            'date_local' => 'sometimes|date',
            'completed' => 'nullable|boolean',
            'created_at' => 'nullable|date',
        ]);

        $ritualEntry->update($data);

        return response()->json($ritualEntry);
    }

    public function destroy(Request $request, RitualEntry $ritualEntry)
    {
        $this->guardUserModel($ritualEntry->ritual, $request);

        $ritualEntry->delete();

        return response()->noContent();
    }
}
