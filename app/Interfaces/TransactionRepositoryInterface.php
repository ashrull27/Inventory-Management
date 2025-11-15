<?php

namespace App\Interfaces;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

interface TransactionRepositoryInterface
{
    /**
     * Get all transactions.
     */
    public function all(): Collection;

    /**
     * Find a transaction by ID.
     */
    public function find(int $id): ?Transaction;

    /**
     * Create a new transaction.
     */
    public function create(array $data): Transaction;

    /**
     * Get transactions grouped by category.
     */
    public function getByCategory(): SupportCollection;

    /**
     * Get transactions grouped by type.
     */
    public function getByType(): SupportCollection;
}