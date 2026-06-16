<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    public static function send($target, $message)
    {
        try {
            $response = Http::timeout(10)->post('http://localhost:3000/send', [
                'number' => $target,
                'message' => $message,
            ]);
            return $response->successful();
        } catch (\Exception $e) {
            Log::error("WA Bot Error: " . $e->getMessage());
            return false;
        }
    }
}
