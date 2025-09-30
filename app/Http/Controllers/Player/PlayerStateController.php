<?php

// This controller orchestrates HTTP requests for the player area related to player state.
namespace App\Http\Controllers\Player;

use App\Http\Controllers\Concerns\InteractsWithUserModels;
use App\Http\Controllers\Controller;
use App\Models\PlayerState;
use Illuminate\Http\Request;

class PlayerStateController extends Controller
{
    use InteractsWithUserModels;

    public function show(Request $request)
    {
        $state = PlayerState::firstOrCreate(
            ['user_id' => $request->user()->id],
            []
        );

        return response()->json($state);
    }

    public function update(Request $request)
    {
        $state = PlayerState::firstOrCreate(
            ['user_id' => $request->user()->id],
            []
        );

        $data = $request->validate([
            'level' => 'nullable|integer|min:1',
            'xp_total' => 'nullable|integer|min:0',
            'life_current' => 'nullable|integer|min:0',
            'life_max' => 'nullable|integer|min:0',
            'energy_current' => 'nullable|integer|min:0',
            'energy_max' => 'nullable|integer|min:0',
            'last_energy_calc_at' => 'nullable|date',
            'last_life_calc_at' => 'nullable|date',
            'last_daily_reset_at' => 'nullable|date',
        ]);

        $state->update($data);

        return response()->json($state);
    }
}
