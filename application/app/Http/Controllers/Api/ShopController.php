<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        return Shop::with(['defaultCurrency', 'globalRegion'])->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:shops,slug',
            'domain' => 'required|string|max:255|unique:shops,domain',
            'default_currency_id' => 'required|integer|exists:currencies,id',
            'global_region_id' => 'nullable|integer|exists:global_regions,id',
            'is_active' => 'boolean',
        ]);

        $shop = Shop::create($validated);

        return response()->json($shop, 201);
    }

    public function update(Request $request, Shop $shop)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:shops,slug,'.$shop->id,
            'domain' => 'required|string|max:255|unique:shops,domain,'.$shop->id,
            'default_currency_id' => 'required|integer|exists:currencies,id',
            'global_region_id' => 'nullable|integer|exists:global_regions,id',
            'is_active' => 'boolean',
        ]);

        $shop->fill($validated);
        $shop->save();

        return response()->json($shop);
    }

    public function destroy(Shop $shop)
    {
        $shop->delete();

        return response()->noContent();
    }
}
