<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     * Only show products summary.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Assuming you want to show only the products summary
        // and not the full details of each product.
        return Product::with(['images', 'discount'])->get()->map(function ($product) {
            return [
                'id' => (string) $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => [
                    'full' => $product->price,
                    'discounted' => $product->discounted_price,
                ],
                'thumbnail' => optional($product->images->first())->path,
            ];
        });
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|integer|min:0',
            'active' => 'boolean',
            'images' => 'array',
            'images.*' => 'string',
            'discount.type' => 'in:percent,amount',
            'discount.amount' => 'integer|min:0',
        ]);

        // Generate a unique slug (Can be improved with a more complex slug generation logic)
        $slug = Str::slug($validated['name']);

        $originalSlug = $slug;
        $i = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $i++;
        }

        $product = Product::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'slug' => $slug,
            'price' => $validated['price'],
            'active' => $validated['active'] ?? true,
        ]);

        if (!empty($validated['images'])) {
            foreach ($validated['images'] as $path) {
                $product->images()->create(['path' => $path]);
            }
        }

        if (!empty($validated['discount'])) {
            $product->discount()->create([
                'type' => $validated['discount']['type'],
                'discount' => $validated['discount']['amount'],
            ]);
        }

        $product->load(['images', 'discount']);

        return response()->json([
            'id' => (string) $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'slug' => $product->slug,
            'price' => [
                'full' => (int) $product->price,
                'discounted' => (int) $product->discounted_price,
            ],
            'discount' => $product->discount ? [
                'type' => $product->discount->type,
                'amount' => (int) $product->discount->discount,
            ] : null,
            'images' => $product->images->pluck('path'),
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        $product->load(['images', 'discount']);

        return response()->json([
            'id' => (string) $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'slug' => $product->slug,
            'price' => [
                'full' => $product->price,
                'discounted' => $product->discounted_price,
            ],
            'discount' => $product->discount ? [
                'type' => $product->discount->type,
                'amount' => $product->discount->discount,
            ] : null,
            'images' => $product->images->pluck('path'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     * Assuming that slug is not updated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'price' => 'sometimes|integer|min:0',
            'active' => 'boolean',
            'images' => 'array',
            'images.*' => 'string',
            'discount' => 'nullable',
            'discount.type' => 'in:percent,amount',
            'discount.amount' => 'integer|min:0',
        ]);

        $product->update($request->only(['name', 'description', 'price', 'active']));

        if ($request->has('images')) {
            $product->images()->delete();
            foreach ($validated['images'] as $path) {
                $product->images()->create(['path' => $path]);
            }
        }
        if ($request->has('discount')) {
            $product->discount()->delete();
            if (!is_null($request->input('discount'))) {
                $product->discount()->create([
                    'type' => $validated['discount']['type'],
                    'discount' => $validated['discount']['amount'],
                ]);
            }
        }

        $product->load(['images', 'discount']);

        return response()->json([
            'id' => (string) $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'slug' => $product->slug,
            'price' => [
                'full' => $product->price,
                'discounted' => $product->discounted_price,
            ],
            'discount' => $product->discount ? [
                'type' => $product->discount->type,
                'amount' => $product->discount->discount,
            ] : null,
            'images' => $product->images->pluck('path'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully.']);
    }

    /**
     * Display the specified product by slug.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function showBySlug($slug)
    {
        $product = Product::with(['images', 'discount'])->where('slug', $slug)->firstOrFail();

        return response()->json([
            'id' => (string) $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'slug' => $product->slug,
            'price' => [
                'full' => $product->price,
                'discounted' => $product->discounted_price,
            ],
            'discount' => $product->discount ? [
                'type' => $product->discount->type,
                'amount' => $product->discount->discount,
            ] : null,
            'images' => $product->images->pluck('path'),
        ]);
    }
}
