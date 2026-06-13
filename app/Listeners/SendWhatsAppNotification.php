<?php

namespace App\Listeners;

use App\Events\TransactionCompleted;
use App\Services\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendWhatsAppNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param TransactionCompleted $event
     * @return void
     */
    public function handle(TransactionCompleted $event): void
    {
        $transaction = $event->transaction;
        $user = $transaction->user;

        $target = $transaction->target;
        
        // Simple regex check for Indonesian phone number format (08xx, 62xx, +62xx)
        if (!preg_match('/^(08|\+62|62)\d{8,13}$/', $target)) {
            Log::info("WhatsApp notification skipped: Target [{$target}] does not look like a valid phone number.");
            return;
        }

        $name = $user ? $user->name : 'Pelanggan';
        $product = $transaction->product_name;
        $status = $transaction->status;
        $sn = $transaction->serial_number ?: '-';

        $message = "Halo {$name}, transaksi {$product} Anda status: {$status}. SN: {$sn}. Terima kasih!";

        WhatsAppService::send($target, $message);
    }
}
