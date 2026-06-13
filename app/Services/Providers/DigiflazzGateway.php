<?php

namespace App\Services\Providers;

use App\Models\Transaction;
use App\Models\ProductProviderRoute;
use Illuminate\Support\Facades\Http;
use Exception;

class DigiflazzGateway implements ProviderGatewayInterface
{
    public function sendOrder(Transaction $transaction, ProductProviderRoute $route): array
    {
        $provider = $route->provider;
        $username = $provider->api_username;
        $apiKey = $provider->api_key;
        $url = $provider->api_url ?: 'https://api.digiflazz.com/v1/transaction';

        $refId = $transaction->reference_id;
        $sign = md5($username . $apiKey . $refId);

        $payload = [
            'username' => $username,
            'buyer_sku_code' => $route->provider_sku,
            'customer_no' => $transaction->target,
            'ref_id' => $refId,
            'sign' => $sign,
        ];

        try {
            $response = Http::timeout(15)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, $payload);

            if ($response->failed()) {
                throw new Exception("Digiflazz HTTP error: " . $response->status() . " - " . $response->body());
            }

            $data = $response->json('data');

            if (!$data) {
                throw new Exception("Digiflazz response empty or invalid: " . $response->body());
            }

            $status = strtolower($data['status'] ?? 'failed');

            if ($status === 'failed') {
                throw new Exception("Digiflazz API returned failure: " . ($data['message'] ?? 'Unknown error'));
            }

            return [
                'success' => true,
                'sn' => $data['sn'] ?? null,
                'message' => $data['message'] ?? 'Transaction processed successfully',
            ];
        } catch (Exception $e) {
            throw new Exception("Digiflazz order failed: " . $e->getMessage(), 0, $e);
        }
    }
}
