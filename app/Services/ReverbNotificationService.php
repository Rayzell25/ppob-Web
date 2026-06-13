<?php

namespace App\Services;

use App\Models\Transaction;
use App\Events\TransactionCompleted;

class ReverbNotificationService
{
    /**
     * Broadcast a completed transaction notification.
     *
     * @param Transaction $transaction
     * @return void
     */
    public function broadcastCompleted(Transaction $transaction): void
    {
        event(new TransactionCompleted($transaction));
    }
}
