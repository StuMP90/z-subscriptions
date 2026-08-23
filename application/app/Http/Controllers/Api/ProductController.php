<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        return Product::with('productType')->when($request->input('search'), fn($q, $s) => $q->where('name', 'ilike', "%{$s}%")->orWhere('sku', 'ilike', "%{$s}%"))
            ->paginate($this->adminPageSize());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:255|unique:products,sku',
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::when($request->filled('slug'), 'unique:products,slug'),
            ],
            'description' => 'nullable|string',
            'product_type_id' => 'required|integer|exists:product_types,id',
            'is_physical' => 'boolean',
            'is_digital' => 'boolean',
            'has_variants' => 'boolean',
            'track_stock' => 'boolean',
            'stock_quantity' => 'integer',
            'allow_backorders' => 'boolean',
            'download_url' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'is_available_on_web' => 'boolean',
            'global_region_ids' => 'nullable|array',
            'global_region_ids.*' => 'integer',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = $this->generateUniqueSlug($validated['name']);
        }

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
            'is_active' => 'boolean',
            'is_available_on_web' => 'boolean',
            'global_region_ids' => 'nullable|array',
            'global_region_ids.*' => 'integer',
        ]);

        $product->fill($validated);
        $product->save();

        return response()->json($product);
    }

    private function generateUniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $counter = 1;

        while (Product::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->noContent();
    }
}
