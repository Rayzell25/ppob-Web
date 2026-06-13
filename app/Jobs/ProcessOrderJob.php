<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Services\ProviderRouterService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Exception;

class ProcessOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Transaction $transaction;

    /**
     * Create a new job instance.
     *
     * @param Transaction $transaction
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Execute the job.
     *
     * @param ProviderRouterService $providerRouterService
     * @return void
     * @throws Exception
     */
    public function handle(ProviderRouterService $providerRouterService): void
    {
        Log::info("Job started: Processing order for transaction [{$this->transaction->reference_id}] via queue worker.");
        
        try {
            $providerRouterService->processOrder($this->transaction);
            Log::info("Job completed: Order processed for transaction [{$this->transaction->reference_id}].");
        } catch (Exception $e) {
            Log::error("Job failed: Error processing order for transaction [{$this->transaction->reference_id}]: " . $e->getMessage(), [
                'transaction_id' => $this->transaction->id,
                'exception' => $e
            ]);
            
            // Fallback: If still in pending/paid status, mark as failed and refund user
            $this->transaction->refresh();
            if (in_array($this->transaction->status, ['pending', 'paid', 'processing'])) {
                $this->transaction->update([
                    'status' => 'failed',
                    'message' => 'Queue processing failure: ' . $e->getMessage()
                ]);
                
                $user = $this->transaction->user;
                if ($user) {
                    $user->increment('balance', $this->transaction->sell_price);
                    Log::info("User balance refunded for transaction [{$this->transaction->reference_id}].");
                }
            }
            
            throw $e;
        }
    }
}
