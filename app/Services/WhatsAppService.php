<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send a WhatsApp message using Fonnte API.
     *
     * @param string $target  The recipient's phone number.
     * @param string $message The message content.
     * @return bool           True if successful, false otherwise.
     */
    public static function send(string $target, string $message): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => env('FONNTE_TOKEN'), // Get token from .env
            ])->post('https://api.fonnte.com/send', [
                'target' => $target,
                'message' => $message,
                // You can add 'delay' => '2' here if you are sending to multiple numbers to avoid bans
            ]);

            $responseData = $response->json();

            // Check if Fonnte accepted the request
            if ($response->successful() && isset($responseData['status']) && $responseData['status'] === true) {
                 Log::info("WhatsApp sent successfully to {$target}. Response: " . json_encode($responseData));
                 return true;
            }

            Log::error("Fonnte API returned an error for target {$target}: " . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error("Exception occurred while sending WhatsApp to {$target}: " . $e->getMessage());
            return false;
        }
    }
}
