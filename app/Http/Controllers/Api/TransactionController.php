<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Services\ProviderRouterService;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class TransactionController extends Controller
{
    protected TransactionService $transactionService;
    protected ProviderRouterService $providerRouterService;

    public function __construct(
        TransactionService $transactionService,
        ProviderRouterService $providerRouterService
    ) {
        $this->transactionService = $transactionService;
        $this->providerRouterService = $providerRouterService;
    }

    /**
     * Store a new transaction and process it via providers.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $rules = [
            'product_id' => 'required|exists:products,id',
            'target_number' => 'required|string|min:4',
        ];

        // If not authenticated, require user_id in request
        if (!auth()->check()) {
            $rules['user_id'] = 'required|exists:users,id';
        }

        $validated = $request->validate($rules);

        // Retrieve user
        $user = auth()->user();
        if (!$user) {
            $user = User::findOrFail($validated['user_id']);
        }

        // Retrieve product
        $product = Product::findOrFail($validated['product_id']);

        // Check product status
        if ($product->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Product is currently inactive.'
            ], 400);
        }

        try {
            // Process balance deduction and transaction creation within a DB transaction
            $transaction = DB::transaction(function () use ($user, $product, $validated) {
                // Lock user record for update to avoid race conditions
                $dbUser = User::where('id', $user->id)->lockForUpdate()->first();

                if ($dbUser->balance < $product->price) {
                    throw new Exception('Insufficient balance.');
                }

                // Deduct balance
                $dbUser->decrement('balance', $product->price);

                // Generate reference transaction ID
                $referenceId = $this->transactionService->generateTrxId();

                // Create the pending transaction
                return Transaction::create([
                    'reference_id' => $referenceId,
                    'user_id' => $dbUser->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'target' => $validated['target_number'],
                    'cost_price' => 0, // Will be set by ProviderRouterService
                    'sell_price' => $product->price,
                    'status' => 'pending',
                    'message' => 'Processing transaction',
                ]);
            });

            // Process order via cascading provider router
            $this->providerRouterService->processOrder($transaction);

            // Fresh load updated status, cost price, message, etc.
            $transaction->refresh();

            return response()->json([
                'success' => true,
                'message' => $transaction->status === 'success' 
                    ? 'Transaction successful.' 
                    : ($transaction->status === 'failed' ? 'Transaction failed: ' . $transaction->message : 'Transaction is pending.'),
                'data' => $transaction
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Show detail status of a transaction.
     *
     * @param string|int $trx_id
     * @return JsonResponse
     */
    public function show($trx_id): JsonResponse
    {
        $transaction = Transaction::where('reference_id', $trx_id)
            ->orWhere('id', $trx_id)
            ->with(['product', 'provider'])
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $transaction
        ]);
    }

    /**
     * Get transaction history for authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function history(Request $request): JsonResponse
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        $transactions = $user->transactions()
            ->with(['product', 'provider'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->query('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }
}
