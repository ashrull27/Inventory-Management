<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\ProductRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    protected ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Display a listing of products.
     */
    public function index(): JsonResponse
    {
        $products = $this->productRepository->all();

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
        ]);

        $product = $this->productRepository->create($validated);

        return response()->json([
            'success' => true,
            'data' => $product->load('category'),
            'message' => 'Product created successfully',
        ], 201);
    }

    /**
     * Display the specified product.
     */
    public function show(int $id): JsonResponse
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $product,
        ]);
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'category_id' => 'sometimes|exists:categories,id',
            'unit_price' => 'sometimes|numeric|min:0',
            'stock_quantity' => 'sometimes|integer|min:0',
        ]);

        $updated = $this->productRepository->update($id, $validated);

        if (!$updated) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        $product = $this->productRepository->find($id);

        return response()->json([
            'success' => true,
            'data' => $product,
            'message' => 'Product updated successfully',
        ]);
    }

    /**
     * Remove the specified product.
     */
    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->productRepository->delete($id);

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully',
        ]);
    }
}