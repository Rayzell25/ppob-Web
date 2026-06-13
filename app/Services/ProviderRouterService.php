<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\ProductProviderRoute;
use App\Services\Providers\DigiflazzGateway;
use App\Services\Providers\KmspGateway;
use App\Services\Providers\GenericGateway;
use App\Events\TransactionCompleted;
use Exception;

class ProviderRouterService
{
    /**
     * Factory method to get the gateway instance based on type.
     *
     * @param string $type
     * @return \App\Services\Providers\ProviderGatewayInterface
     * @throws Exception
     */
    protected function getGateway(string $type)
    {
        return match (strtolower($type)) {
            'digiflazz' => new DigiflazzGateway(),
            'kmsp' => new KmspGateway(),
            'custom', 'generic' => new GenericGateway(),
            default => throw new Exception("Unsupported provider gateway type: {$type}"),
        };
    }

    /**
     * Process order with cascading provider routing failover.
     *
     * @param Transaction $transaction
     * @return bool
     */
    public function processOrder(Transaction $transaction): bool
    {
        $product = $transaction->product;

        if (!$product) {
            $transaction->update([
                'status' => 'failed',
                'message' => 'Product not found.',
            ]);
            return false;
        }

        // Get all active routing paths ordered by priority (1 = highest)
        $routes = $product->providerRoutes()
            ->with('provider')
            ->where('is_active', true)
            ->where('status', 'active')
            ->get();

        if ($routes->isEmpty()) {
            $transaction->update([
                'status' => 'failed',
                'message' => 'No active provider routes configured for this product.',
            ]);
            return false;
        }

        foreach ($routes as $route) {
            $provider = $route->provider;
            if (!$provider || !$provider->is_active) {
                continue;
            }

            try {
                // Update transaction with current provider attempt
                $transaction->update([
                    'provider_id' => $provider->id,
                    'cost_price' => $route->cost_price,
                ]);

                // Get gateway adapter
                $gateway = $this->getGateway($provider->type);

                // Call gateway
                $result = $gateway->sendOrder($transaction, $route);

                // Successfully processed
                $transaction->update([
                    'status' => 'success',
                    'serial_number' => $result['sn'],
                    'message' => $result['message'],
                ]);

                // Trigger transaction completed broadcast event
                event(new TransactionCompleted($transaction));

                return true;
            } catch (Exception $e) {
                // Record the failed attempt
                $attempts = $transaction->routing_attempts ?? [];
                $attempts[] = [
                    'provider_id' => $provider->id,
                    'provider_name' => $provider->name,
                    'provider_sku' => $route->provider_sku,
                    'error' => $e->getMessage(),
                    'attempted_at' => now()->toIso8601String(),
                ];

                $transaction->update([
                    'routing_attempts' => $attempts,
                    'message' => 'Route failed: ' . $e->getMessage(),
                ]);

                // Continue to the next priority route...
            }
        }

        // If we reach here, all routing attempts failed
        $transaction->update([
            'status' => 'failed',
            'message' => 'All provider routes failed.',
        ]);

        // Refund the user's balance
        $user = $transaction->user;
        if ($user) {
            $user->increment('balance', $transaction->sell_price);
        }

        return false;
    }
}
