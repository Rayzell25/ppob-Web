<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Models\Transaction;
use App\Services\ProviderRouterService;
use App\Services\ReverbNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class WebhookController extends Controller
{
    protected ProviderRouterService $providerRouterService;
    protected ReverbNotificationService $reverbNotificationService;

    public function __construct(
        ProviderRouterService $providerRouterService,
        ReverbNotificationService $reverbNotificationService
    ) {
        $this->providerRouterService = $providerRouterService;
        $this->reverbNotificationService = $reverbNotificationService;
    }

    /**
     * Handle incoming AutoGoPay payment webhooks.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handleAutoGoPay(Request $request): JsonResponse
    {
        $signature = $request->header('X-Signature');
        $rawBody = $request->getContent();

        // Get AutoGoPay secret from config/env
        $secret = config('services.autogopay.secret') ?: \App\Models\Setting::where('key', 'autogopay_secret')->value('value') ?: 'default_secret';

        // Validate HMAC-SHA256 signature
        $computedSignature = hash_hmac('sha256', $rawBody, $secret);

        if (!hash_equals($computedSignature, (string)$signature)) {
            Log::warning('AutoGoPay webhook signature validation failed.', [
                'received' => $signature,
                'computed' => $computedSignature
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Invalid signature'
            ], 401);
        }

        $status = $request->input('status');
        $referenceId = $request->input('reference_id') ?? $request->input('external_id') ?? $request->input('merchant_ref');

        $transaction = Transaction::where('reference_id', $referenceId)->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        }

        // Only process settlement status
        if ($status === 'settlement') {
            // Check if transaction has already been paid/processed
            if (in_array($transaction->status, ['paid', 'success'])) {
                return response()->json([
                    'success' => true,
                    'message' => 'Transaction already processed'
                ]);
            }

            // Update status to 'paid'
            $transaction->update([
                'status' => 'paid',
                'message' => 'Payment settled via AutoGoPay. Order queued for routing...'
            ]);

            // Process order asynchronously via queue worker
            \App\Jobs\ProcessTransactionOrder::dispatch($transaction);

            return response()->json([
                'success' => true,
                'message' => 'Webhook processed and order queued.'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Webhook received but no action taken (status is not settlement).'
        ]);
    }

    /**
     * Handle callback updates from providers (Digiflazz, KMSP, Custom, etc.)
     *
     * @param Request $request
     * @param string $provider
     * @return JsonResponse
     */
    public function handleProviderCallback(Request $request, string $provider): JsonResponse
    {
        // Find provider model
        $providerModel = Provider::where('type', $provider)
            ->orWhere('code', $provider)
            ->first();

        if (!$providerModel) {
            return response()->json([
                'success' => false,
                'message' => "Provider [{$provider}] is not configured."
            ], 404);
        }

        $refId = null;
        $status = null;
        $sn = null;
        $message = '';

        try {
            // Flexible payload parsing & signature verification based on provider type
            switch (strtolower($providerModel->type)) {
                case 'digiflazz':
                    $data = $request->input('data');
                    if (!$data) {
                        throw new Exception('Invalid Digiflazz payload: data block is missing.');
                    }
                    $refId = $data['ref_id'] ?? null;
                    $status = $data['status'] ?? null;
                    $sn = $data['sn'] ?? null;
                    $message = $data['message'] ?? '';
                    $incomingSign = $data['sign'] ?? '';

                    // Digiflazz signature: md5(username + apiKey + 'depok')
                    $expectedSign = md5($providerModel->api_username . $providerModel->api_key . 'depok');
                    if ($incomingSign !== $expectedSign) {
                        throw new Exception('Invalid Digiflazz callback signature.');
                    }
                    break;

                case 'kmsp':
                    $refId = $request->input('trx_id') ?? $request->input('ref_id');
                    $status = $request->input('status');
                    $sn = $request->input('sn') ?? $request->input('serial_number');
                    $message = $request->input('message') ?? '';
                    $incomingSign = $request->input('signature') ?? $request->input('sign');

                    // KMSP signature: md5(username + apiKey + refId) or md5(apiKey + refId)
                    $expectedSign1 = md5($providerModel->api_username . $providerModel->api_key . $refId);
                    $expectedSign2 = md5($providerModel->api_key . $refId);
                    if ($incomingSign !== $expectedSign1 && $incomingSign !== $expectedSign2) {
                        throw new Exception('Invalid KMSP callback signature.');
                    }
                    break;

                default:
                    // Custom / Generic Provider
                    $refId = $request->input('ref_id') ?? $request->input('trx_id');
                    $status = $request->input('status');
                    $sn = $request->input('sn') ?? $request->input('serial_number');
                    $message = $request->input('message') ?? '';
                    $incomingKey = $request->header('X-API-Key') ?? $request->input('api_key');

                    // Simple token verification for custom provider
                    if ($incomingKey !== $providerModel->api_key) {
                        throw new Exception('Invalid API Key for Custom Provider.');
                    }
                    break;
            }

            if (!$refId) {
                throw new Exception('Callback payload is missing transaction reference ID.');
            }

            $transaction = Transaction::where('reference_id', $refId)->first();

            if (!$transaction) {
                throw new Exception("Transaction not found for reference ID: {$refId}");
            }

            // Map provider status to local status
            $localStatus = 'pending';
            $normalizedStatus = strtolower((string)$status);
            
            if (in_array($normalizedStatus, ['success', 'sukses', 'paid'])) {
                $localStatus = 'success';
            } elseif (in_array($normalizedStatus, ['failed', 'gagal', 'error'])) {
                $localStatus = 'failed';
            }

            // If the status has changed
            if ($transaction->status !== $localStatus) {
                // Update transaction details (serial_number stores the SN)
                $transaction->update([
                    'status' => $localStatus,
                    'serial_number' => $sn,
                    'message' => $message ?: "Status updated to {$localStatus} via callback",
                ]);

                // Handle refund if status is marked as failed
                if ($localStatus === 'failed') {
                    $user = $transaction->user;
                    if ($user) {
                        $user->increment('balance', $transaction->sell_price);
                    }
                }

                // Broadcast update notification
                $this->reverbNotificationService->broadcastCompleted($transaction);
            }

            return response()->json([
                'success' => true,
                'message' => 'Callback processed successfully.'
            ]);

        } catch (Exception $e) {
            Log::error('Webhook callback processing failed.', [
                'provider' => $provider,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
