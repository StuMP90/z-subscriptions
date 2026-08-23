<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function index(Request $request)
    {
        return Offer::with(['product', 'currency', 'frequency'])->when($request->input('search'), fn($q, $s) => $q->whereHas('product', fn($pq) => $pq->where('name', 'ilike', "%{$s}%")))
            ->paginate($this->adminPageSize());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'product_variant_id' => 'nullable|integer|exists:product_variants,id',
            'shop_id' => 'required|integer|exists:shops,id',
            'currency_id' => 'required|integer|exists:currencies,id',
            'frequency_id' => 'required|integer|exists:publication_frequencies,id',
            'base_price' => 'required|numeric',
            'price' => 'required|numeric',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date',
            'is_setup_offer' => 'boolean',
            'setup_config' => 'nullable|string',
            'is_active' => 'boolean',
            'is_available_on_web' => 'boolean',
            'global_region_ids' => 'nullable|array',
            'global_region_ids.*' => 'integer',
        ]);

        $validated['setup_config'] = $validated['setup_config'] ? json_decode($validated['setup_config'], true) : null;

        $offer = Offer::create($validated);

        return response()->json($offer, 201);
    }

    public function update(Request $request, Offer $offer)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'product_variant_id' => 'nullable|integer|exists:product_variants,id',
            'shop_id' => 'required|integer|exists:shops,id',
            'currency_id' => 'required|integer|exists:currencies,id',
            'frequency_id' => 'required|integer|exists:publication_frequencies,id',
            'base_price' => 'required|numeric',
            'price' => 'required|numeric',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date',
            'is_setup_offer' => 'boolean',
            'setup_config' => 'nullable|string',
            'is_active' => 'boolean',
            'is_available_on_web' => 'boolean',
            'global_region_ids' => 'nullable|array',
            'global_region_ids.*' => 'integer',
        ]);

        $validated['setup_config'] = $validated['setup_config'] ? json_decode($validated['setup_config'], true) : null;

        $offer->fill($validated);
        $offer->save();

        return response()->json($offer);
    }

}
