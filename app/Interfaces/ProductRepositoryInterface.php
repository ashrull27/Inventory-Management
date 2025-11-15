<?php

namespace App\Interfaces;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    /**
     * Get all products.
     */
    public function all(): Collection;

    /**
     * Find a product by ID.
     */
    public function find(int $id): ?Product;

    /**
     * Create a new product.
     */
    public function create(array $data): Product;

    /**
     * Update a product.
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete a product.
     */
    public function delete(int $id): bool;

    /**
     * Get inventory summary with stock values.
     */
    public function getInventorySummary(): array;
    
}