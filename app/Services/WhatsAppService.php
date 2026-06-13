<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class WhatsAppService
{
    /**
     * Send a WhatsApp message via Fonnte.
     *
     * @param string $target
     * @param string $message
     * @return array
     * @throws Exception
     */
    public function send(string $target, string $message): array
    {
        $token = config('services.fonnte.token');

        if (!$token) {
            Log::warning("WhatsApp notification skipped: FONNTE_TOKEN is not configured in services config.");
            return ['status' => false, 'reason' => 'Fonnte token not configured'];
        }

        try {
            Log::info("Sending WhatsApp message to [{$target}]...");

            $response = Http::withHeaders([
                'Authorization' => $token
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target' => $target,
                'message' => $message,
            ]);

            if ($response->failed()) {
                throw new Exception("Fonnte HTTP request failed: " . $response->status() . " - " . $response->body());
            }

            $result = $response->json();
            
            // Fonnte returns {"status": true} on success
            if (!($result['status'] ?? false)) {
                throw new Exception("Fonnte API returned error: " . ($result['reason'] ?? $response->body()));
            }

            Log::info("WhatsApp message successfully sent to [{$target}]. Fonnte response: " . json_encode($result));

            return [
                'status' => true,
                'data' => $result
            ];
        } catch (Exception $e) {
            Log::error("Failed to send WhatsApp message to [{$target}]: " . $e->getMessage());
            throw $e;
        }
    }
}
