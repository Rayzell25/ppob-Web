<?php

namespace App\Services\Providers;

use App\Models\Transaction;
use App\Models\ProductProviderRoute;
use Illuminate\Support\Facades\Http;
use Exception;

class GenericGateway implements ProviderGatewayInterface
{
    public function sendOrder(Transaction $transaction, ProductProviderRoute $route): array
    {
        $provider = $route->provider;
        $url = $provider->api_url;

        if (!$url) {
            throw new Exception("Custom Provider API URL is not configured.");
        }

        $payload = [
            'api_key' => $provider->api_key,
            'username' => $provider->api_username,
            'sku' => $route->provider_sku,
            'target' => $transaction->target,
            'ref_id' => $transaction->reference_id,
        ];

        try {
            $response = Http::timeout(15)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, $payload);

            if ($response->failed()) {
                throw new Exception("Custom Provider HTTP error: " . $response->status() . " - " . $response->body());
            }

            $resData = $response->json();
            $success = $resData['success'] ?? ($resData['status'] === 'success' || false);

            if (!$success) {
                throw new Exception("Custom Provider API returned failure: " . ($resData['message'] ?? 'Unknown error'));
            }

            return [
                'success' => true,
                'sn' => $resData['sn'] ?? ($resData['serial_number'] ?? null),
                'message' => $resData['message'] ?? 'Transaction processed successfully',
            ];
        } catch (Exception $e) {
            throw new Exception("Custom Provider order failed: " . $e->getMessage(), 0, $e);
        }
    }
}
