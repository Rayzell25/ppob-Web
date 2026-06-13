<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class AuthController extends Controller
{
    protected WhatsAppService $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Send OTP verification code to user WhatsApp.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phone_number' => 'required|string|regex:/^(08|\+62|62)\d{8,13}$/'
        ]);

        $phoneNumber = $request->input('phone_number');
        
        // Generate 6-digit random code
        $otp = (string) rand(100000, 999999);
        
        // Store in cache for 5 minutes
        Cache::put('otp_' . $phoneNumber, $otp, now()->addMinutes(5));

        $message = "Kode OTP Anda adalah: *{$otp}*. Berlaku selama 5 menit. Harap tidak membagikan kode ini kepada siapapun.";

        try {
            $this->whatsAppService->send($phoneNumber, $message);
            
            Log::info("OTP sent successfully to [{$phoneNumber}].");

            return response()->json([
                'success' => true,
                'message' => 'OTP code has been sent successfully to your WhatsApp.'
            ]);
        } catch (Exception $e) {
            Log::error("Failed to send OTP to [{$phoneNumber}]: " . $e->getMessage());
            
            // For testing/fallback in local development, return OTP in response if Fonnte is not configured
            if (config('app.env') === 'local' || !config('services.fonnte.token')) {
                return response()->json([
                    'success' => true,
                    'message' => 'OTP generated (Local Mock Mode: Fonnte token not configured).',
                    'mock_otp' => $otp // ONLY returned for ease of testing in local development
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP code. Please try again later.'
            ], 500);
        }
    }

    /**
     * Verify OTP and login/register user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phone_number' => 'required|string|regex:/^(08|\+62|62)\d{8,13}$/',
            'otp_code' => 'required|string|size:6',
        ]);

        $phoneNumber = $request->input('phone_number');
        $otpCode = $request->input('otp_code');

        $cachedOtp = Cache::get('otp_' . $phoneNumber);

        if (!$cachedOtp || $cachedOtp !== $otpCode) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP code.'
            ], 422);
        }

        // OTP is valid, clear from cache
        Cache::forget('otp_' . $phoneNumber);

        // Find or create user mapped to this phone number
        $email = $phoneNumber . '@wa.ppob';
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'WA Member ' . substr($phoneNumber, -4),
                'password' => bcrypt(Str::random(16)),
                'balance' => 0,
                'role' => 'user',
                'is_banned' => false,
            ]
        );

        // Issue Sanctum API token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Authentication successful.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }
}
