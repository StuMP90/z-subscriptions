<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return Product::with('productType')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:255|unique:products,sku',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'description' => 'nullable|string',
            'product_type_id' => 'required|integer|exists:product_types,id',
            'is_physical' => 'boolean',
            'is_digital' => 'boolean',
            'has_variants' => 'boolean',
            'track_stock' => 'boolean',
            'stock_quantity' => 'integer',
            'allow_backorders' => 'boolean',
            'download_url' => 'nullable|string|max:255',
            'status' => 'required|string|max:20',
            'is_available_on_web' => 'boolean',
            'global_region_ids' => 'nullable|array',
            'global_region_ids.*' => 'integer',
        ]);

        $product = Product::create($validated);

        return response()->json($product, 201);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:255|unique:products,sku,'.$product->id,
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug,'.$product->id,
            'description' => 'nullable|string',
            'product_type_id' => 'required|integer|exists:product_types,id',
            'is_physical' => 'boolean',
            'is_digital' => 'boolean',
            'has_variants' => 'boolean',
            'track_stock' => 'boolean',
            'stock_quantity' => 'integer',
            'allow_backorders' => 'boolean',
            'download_url' => 'nullable|string|max:255',
            'status' => 'required|string|max:20',
            'is_available_on_web' => 'boolean',
            'global_region_ids' => 'nullable|array',
            'global_region_ids.*' => 'integer',
        ]);

        $product->fill($validated);
        $product->save();

        return response()->json($product);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->noContent();
    }
}
