<?php

namespace App\Events;

use App\Models\Transaction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransactionCompleted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The transaction instance.
     *
     * @var Transaction
     */
    public $transaction;

    /**
     * Create a new event instance.
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('transactions'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'transaction.completed';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        $user = $this->transaction->user;
        $rawName = $user ? ($user->email ?: $user->name) : 'Guest';
        $maskedName = $this->maskString($rawName);

        return [
            'user' => $maskedName,
            'product_name' => $this->transaction->product_name,
            'amount' => $this->transaction->sell_price,
            'status' => $this->transaction->status,
        ];
    }

    /**
     * Mask sensitive user names or emails.
     *
     * @param string $str
     * @return string
     */
    private function maskString(string $str): string
    {
        if (str_contains($str, '@')) {
            [$user, $domain] = explode('@', $str, 2);
            $len = strlen($user);
            if ($len <= 2) {
                return $user . '@' . $domain;
            }
            $maskedUser = substr($user, 0, 1) . str_repeat('*', $len - 2) . substr($user, -1);
            return $maskedUser . '@' . $domain;
        }

        $len = strlen($str);
        if ($len <= 2) {
            return $str;
        }
        return substr($str, 0, 1) . str_repeat('*', $len - 2) . substr($str, -1);
    }
}
