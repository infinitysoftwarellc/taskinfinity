<?php

// This controller orchestrates HTTP requests for the gamification area related to user ability.
namespace App\Http\Controllers\Gamification;

use App\Http\Controllers\Concerns\InteractsWithUserModels;
use App\Http\Controllers\Controller;
use App\Models\UserAbility;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserAbilityController extends Controller
{
    use InteractsWithUserModels;

    public function index(Request $request)
    {
        $abilities = UserAbility::where('user_id', $request->user()->id)
            ->with('ability')
            ->get();

        return response()->json($abilities);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'ability_id' => ['required', 'integer', Rule::exists('abilities', 'id')],
            'level' => 'nullable|integer|min:1',
            'is_equipped' => 'nullable|boolean',
            'unlocked_at' => 'required|date',
        ]);

        $data['user_id'] = $request->user()->id;

        $userAbility = UserAbility::updateOrCreate(
            ['user_id' => $data['user_id'], 'ability_id' => $data['ability_id']],
            [
                'level' => $data['level'] ?? 1,
                'is_equipped' => $data['is_equipped'] ?? false,
                'unlocked_at' => $data['unlocked_at'],
            ]
        );

        return response()->json($userAbility, 201);
    }

    public function show(Request $request, UserAbility $userAbility)
    {
        $this->guardUserModel($userAbility, $request);

        return response()->json($userAbility->load('ability'));
    }

    public function update(Request $request, UserAbility $userAbility)
    {
        $this->guardUserModel($userAbility, $request);

        $data = $request->validate([
            'level' => 'nullable|integer|min:1',
            'is_equipped' => 'nullable|boolean',
            'unlocked_at' => 'nullable|date',
        ]);

        $userAbility->update($data);

        return response()->json($userAbility);
    }

    public function destroy(Request $request, UserAbility $userAbility)
    {
        $this->guardUserModel($userAbility, $request);

        $userAbility->delete();

        return response()->noContent();
    }
}
