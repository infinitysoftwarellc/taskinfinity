<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\EconomyWallet;
use Illuminate\Http\Request;

class EconomyWalletController extends Controller
{
    public function show(Request $request)
    {
        $wallet = EconomyWallet::firstOrCreate(
            ['user_id' => $request->user()->id],
            []
        );

        return response()->json($wallet);
    }

    public function update(Request $request)
    {
        $wallet = EconomyWallet::firstOrCreate(
            ['user_id' => $request->user()->id],
            []
        );

        $data = $request->validate([
            'coins_balance' => 'required|integer',
            'updated_at' => 'nullable|date',
        ]);

        $wallet->update($data);

        return response()->json($wallet);
    }
}
