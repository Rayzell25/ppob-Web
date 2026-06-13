<?php

namespace App\Services\Providers;

use App\Models\Transaction;
use App\Models\ProductProviderRoute;
use Illuminate\Support\Facades\Http;
use Exception;

class KmspGateway implements ProviderGatewayInterface
{
    public function sendOrder(Transaction $transaction, ProductProviderRoute $route): array
    {
        $provider = $route->provider;
        $url = $provider->api_url ?: 'https://api.kmsp.co.id/v1/order';

        $payload = [
            'api_key' => $provider->api_key,
            'member_id' => $provider->api_username,
            'sku' => $route->provider_sku,
            'target' => $transaction->target,
            'trx_id' => $transaction->reference_id,
        ];

        try {
            $response = Http::timeout(15)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, $payload);

            if ($response->failed()) {
                throw new Exception("KMSP HTTP error: " . $response->status() . " - " . $response->body());
            }

            $resData = $response->json();
            $status = strtolower($resData['status'] ?? 'failed');

            if ($status === 'failed' || $status === 'error') {
                throw new Exception("KMSP API returned failure: " . ($resData['message'] ?? 'Unknown error'));
            }

            return [
                'success' => true,
                'sn' => $resData['sn'] ?? ($resData['serial_number'] ?? null),
                'message' => $resData['message'] ?? 'Transaction processed successfully',
            ];
        } catch (Exception $e) {
            throw new Exception("KMSP order failed: " . $e->getMessage(), 0, $e);
        }
    }
}
