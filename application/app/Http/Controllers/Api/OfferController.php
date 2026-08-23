<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function index()
    {
        return Offer::with(['product', 'currency', 'frequency'])->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'shop_id' => 'required|integer|exists:shops,id',
            'currency_id' => 'required|integer|exists:currencies,id',
            'frequency_id' => 'required|integer|exists:subscription_frequencies,id',
            'base_price' => 'required|numeric',
            'price' => 'required|numeric',
            'is_active' => 'boolean',
        ]);

        $offer = Offer::create($validated);

        return response()->json($offer, 201);
    }

    public function update(Request $request, Offer $offer)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'shop_id' => 'required|integer|exists:shops,id',
            'currency_id' => 'required|integer|exists:currencies,id',
            'frequency_id' => 'required|integer|exists:subscription_frequencies,id',
            'base_price' => 'required|numeric',
            'price' => 'required|numeric',
            'is_active' => 'boolean',
        ]);

        $offer->fill($validated);
        $offer->save();

        return response()->json($offer);
    }

    public function destroy(Offer $offer)
    {
        $offer->delete();

        return response()->noContent();
    }
}
