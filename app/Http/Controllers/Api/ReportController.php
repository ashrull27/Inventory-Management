<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\TransactionRepositoryInterface;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    protected ProductRepositoryInterface $productRepository;
    protected TransactionRepositoryInterface $transactionRepository;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        TransactionRepositoryInterface $transactionRepository
    ) {
        $this->productRepository = $productRepository;
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * Get inventory summary with stock values.
     */
    public function inventorySummary(): JsonResponse
    {
        $summary = $this->productRepository->getInventorySummary();

        return response()->json([
            'success' => true,
            'data' => $summary['products'],
            'summary' => $summary['summary'],
        ]);
    }

    /**
     * Get transactions grouped by category.
     */
    public function byCategory(): JsonResponse
    {
        $data = $this->transactionRepository->getByCategory();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get transactions grouped by type (IN/OUT).
     */
    public function byType(): JsonResponse
    {
        $data = $this->transactionRepository->getByType();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}