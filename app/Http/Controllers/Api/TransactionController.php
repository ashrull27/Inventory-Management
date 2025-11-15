<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Interfaces\TransactionRepositoryInterface;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    protected TransactionRepositoryInterface $transactionRepository;

    public function __construct(TransactionRepositoryInterface $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * Display a listing of transactions.
     */
    public function index(): JsonResponse
    {
        $transactions = $this->transactionRepository->all();

        return response()->json([
            'success' => true,
            'data' => $transactions,
        ]);
    }

    /**
     * Store a newly created transaction.
     */
    public function store(StoreTransactionRequest $request): JsonResponse
    {
        try {
            $transaction = $this->transactionRepository->create($request->validated());

            return response()->json([
                'success' => true,
                'data' => $transaction,
                'message' => 'Transaction created successfully',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Display the specified transaction.
     */
    public function show(int $id): JsonResponse
    {
        $transaction = $this->transactionRepository->find($id);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $transaction,
        ]);
    }
}