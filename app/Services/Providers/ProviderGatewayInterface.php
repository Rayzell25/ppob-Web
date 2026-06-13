<?php

namespace App\Services\Providers;

use App\Models\Transaction;
use App\Models\ProductProviderRoute;

interface ProviderGatewayInterface
{
    /**
     * Send order request to the provider API.
     *
     * @param Transaction $transaction
     * @param ProductProviderRoute $route
     * @return array{success: bool, sn: string|null, message: string}
     * @throws \Exception
     */
    public function sendOrder(Transaction $transaction, ProductProviderRoute $route): array;
}
