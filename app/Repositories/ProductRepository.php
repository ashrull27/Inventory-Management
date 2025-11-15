<?php

namespace App\Repositories;

use App\Interfaces\ProductRepositoryInterface;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository implements ProductRepositoryInterface
{
    /**
     * Get all products with category relationship.
     */
    public function all(): Collection
    {
        return Product::with('category')->get();
    }

    /**
     * Find a product by ID with category.
     */
    public function find(int $id): ?Product
    {
        return Product::with('category')->find($id);
    }

    /**
     * Create a new product.
     */
    public function create(array $data): Product
    {
        return Product::create($data);
    }

    /**
     * Update a product.
     */
    public function update(int $id, array $data): bool
    {
        $product = Product::find($id);
        
        if (!$product) {
            return false;
        }

        return $product->update($data);
    }

    /**
     * Delete a product (soft delete).
     */
    public function delete(int $id): bool
    {
        $product = Product::find($id);
        
        if (!$product) {
            return false;
        }

        return $product->delete();
    }

    /**
     * Get inventory summary with calculations.
     */
    public function getInventorySummary(): array
    {
        $products = Product::with('category')
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category' => $product->category->name,
                    'unit_price' => $product->unit_price,
                    'stock_quantity' => $product->stock_quantity,
                    'total_value' => $product->total_value,
                ];
            });

        $totalValue = $products->sum(function ($product) {
            return (float) $product['total_value'];
        });

        return [
            'products' => $products,
            'summary' => [
                'total_items' => $products->count(),
                'total_stock_value' => number_format($totalValue, 2, '.', ''),
            ],
        ];
    }
}