<?php

namespace App\Repositories;

use App\Interfaces\TransactionRepositoryInterface;
use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;

class TransactionRepository implements TransactionRepositoryInterface
{
    /**
     * Get all transactions with product and category.
     */
    public function all(): Collection
    {
        return Transaction::with('product.category')
            ->orderBy('transaction_time', 'desc')
            ->get();
    }

    /**
     * Find a transaction by ID.
     */
    public function find(int $id): ?Transaction
    {
        return Transaction::with('product.category')->find($id);
    }

    /**
     * Create a new transaction and update product stock.
     */
    public function create(array $data): Transaction
    {
        return DB::transaction(function () use ($data) {
            // Get product to capture current price
            $product = Product::findOrFail($data['product_id']);
            
            // Set transaction time if not provided
            if (!isset($data['transaction_time'])) {
                $data['transaction_time'] = now();
            }
            
            // Store price at transaction time
            $data['unit_price'] = $product->unit_price;
            
            // Create transaction
            $transaction = Transaction::create($data);
            
            // Update product stock based on transaction type
            if ($data['transaction_type'] === 'IN') {
                $product->increment('stock_quantity', $data['quantity']);
            } elseif ($data['transaction_type'] === 'OUT') {
                // Prevent negative stock
                if ($product->stock_quantity < $data['quantity']) {
                    throw new \Exception('Insufficient stock. Available: ' . $product->stock_quantity);
                }
                $product->decrement('stock_quantity', $data['quantity']);
            }
            
            return $transaction->load('product.category');
        });
    }

    /**
     * Get transactions grouped by category.
     */
    public function getByCategory(): SupportCollection
    {
        $results = DB::table('transactions')
            ->join('products', 'transactions.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'categories.name as category',
                DB::raw('COALESCE(SUM(CASE WHEN transactions.transaction_type = "IN" THEN transactions.quantity ELSE 0 END), 0) as total_in'),
                DB::raw('COALESCE(SUM(CASE WHEN transactions.transaction_type = "OUT" THEN transactions.quantity ELSE 0 END), 0) as total_out'),
                DB::raw('COALESCE(SUM(CASE WHEN transactions.transaction_type = "IN" THEN transactions.quantity ELSE -transactions.quantity END), 0) as net_movement'),
                DB::raw('COUNT(*) as transaction_count')
            )
            ->whereNull('products.deleted_at')
            ->whereNull('categories.deleted_at')
            ->groupBy('categories.id', 'categories.name')
            ->get();

        return $results;
    }

    /**
     * Get transactions grouped by type.
     */
    public function getByType(): SupportCollection
    {
        $results = DB::table('transactions')
            ->select(
                'transaction_type',
                DB::raw('COALESCE(SUM(quantity), 0) as total_quantity'),
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('COALESCE(SUM(quantity * unit_price), 0) as total_value')
            )
            ->groupBy('transaction_type')
            ->get();

        return $results->map(function ($item) {
            $item->total_value = number_format((float)$item->total_value, 2, '.', '');
            return $item;
        });
    }
}