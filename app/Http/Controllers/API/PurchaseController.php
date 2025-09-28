<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Concerns\InteractsWithUserModels;
use App\Http\Controllers\Controller;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PurchaseController extends Controller
{
    use InteractsWithUserModels;

    public function index(Request $request)
    {
        $purchases = Purchase::where('user_id', $request->user()->id)
            ->orderByDesc('acquired_at')
            ->get();

        return response()->json($purchases);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'store_item_id' => ['required', 'integer', Rule::exists('store_items', 'id')],
            'spent_xp' => 'nullable|integer',
            'spent_coins' => 'nullable|integer',
            'acquired_at' => 'required|date',
        ]);

        $data['user_id'] = $request->user()->id;

        $purchase = Purchase::create($data);

        return response()->json($purchase, 201);
    }

    public function show(Request $request, Purchase $purchase)
    {
        $this->guardUserModel($purchase, $request);

        return response()->json($purchase);
    }

    public function update(Request $request, Purchase $purchase)
    {
        $this->guardUserModel($purchase, $request);

        $data = $request->validate([
            'spent_xp' => 'nullable|integer',
            'spent_coins' => 'nullable|integer',
            'acquired_at' => 'nullable|date',
        ]);

        $purchase->update($data);

        return response()->json($purchase);
    }

    public function destroy(Request $request, Purchase $purchase)
    {
        $this->guardUserModel($purchase, $request);

        $purchase->delete();

        return response()->noContent();
    }
}
