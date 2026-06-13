<?php

require __DIR__.'/vendor/autoload.php';

// Boot Laravel Application
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Provider;
use App\Models\ProductProviderRoute;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

function printStep($title) {
    echo "\n========================================= \n";
    echo "STEP: " . $title . "\n";
    echo "========================================= \n";
}

function printDBState() {
    $transactions = Transaction::all();
    echo "Current Database Transactions:\n";
    if ($transactions->isEmpty()) {
        echo "No transactions in database.\n";
    } else {
        foreach ($transactions as $trx) {
            echo "ID: {$trx->id} | Ref: {$trx->reference_id} | Target: {$trx->target} | Provider ID: {$trx->provider_id} | Sell Price: {$trx->sell_price} | Cost Price: {$trx->cost_price} | Status: {$trx->status} | SN: {$trx->serial_number} | Message: {$trx->message}\n";
            if ($trx->routing_attempts) {
                echo "  Attempts: " . json_encode($trx->routing_attempts) . "\n";
            }
        }
    }
    $users = User::where('email', 'buyer@example.com')->get();
    foreach ($users as $u) {
        echo "User: {$u->name} | Balance: {$u->balance}\n";
    }
}

// -------------------------------------------------------------
printStep("1. PERSIAPAN DATA (CLEAN & SEED)");
// -------------------------------------------------------------

DB::statement('PRAGMA foreign_keys = OFF');
ProductProviderRoute::truncate();
Product::truncate();
Brand::truncate();
Category::truncate();
Provider::truncate();
Transaction::truncate();
User::where('email', 'buyer@example.com')->delete();
DB::statement('PRAGMA foreign_keys = ON');

// Create test user
$user = User::create([
    'name' => 'Test Buyer',
    'email' => 'buyer@example.com',
    'password' => bcrypt('password'),
    'balance' => 100000, // 100,000 balance
    'role' => 'user',
    'is_banned' => false,
]);

// Create catalog
$category = Category::create(['name' => 'Pulsa', 'slug' => 'pulsa']);
$brand = Brand::create(['category_id' => $category->id, 'name' => 'Telkomsel', 'slug' => 'telkomsel']);

$product = Product::create([
    'category_id' => $category->id,
    'brand_id' => $brand->id,
    'name' => 'Telkomsel 10K',
    'sku' => 'T10K',
    'price' => 11000,
    'type' => 'pulsa',
    'status' => 'active',
]);

// Create providers
$dfProvider = Provider::create([
    'name' => 'Digiflazz Supplier',
    'code' => 'digiflazz',
    'type' => 'digiflazz',
    'api_username' => 'df_user',
    'api_key' => 'df_key',
    'is_active' => true,
]);

$kmspProvider = Provider::create([
    'name' => 'KMSP Supplier',
    'code' => 'kmsp',
    'type' => 'kmsp',
    'api_username' => 'kmsp_user',
    'api_key' => 'kmsp_key',
    'is_active' => true,
]);

// Setup routes (Digiflazz has priority 1, KMSP has priority 2)
$routeDf = ProductProviderRoute::create([
    'product_id' => $product->id,
    'provider_id' => $dfProvider->id,
    'provider_sku' => 'df_t10k',
    'cost_price' => 10200,
    'priority' => 1,
    'is_active' => true,
    'status' => 'active',
]);

$routeKmsp = ProductProviderRoute::create([
    'product_id' => $product->id,
    'provider_id' => $kmspProvider->id,
    'provider_sku' => 'kmsp_t10k',
    'cost_price' => 10300,
    'priority' => 2,
    'is_active' => true,
    'status' => 'active',
]);

printDBState();

// -------------------------------------------------------------
// Global HTTP Client Mocks
// -------------------------------------------------------------
Http::fake(function ($request) {
    if (str_contains($request->url(), 'api.digiflazz.com')) {
        $body = json_decode($request->body(), true);
        $customerNo = $body['customer_no'] ?? '';
        
        // Simulating failure for failover scenario
        if ($customerNo === '08999999999') {
            return Http::response([
                'data' => [
                    'status' => 'failed',
                    'message' => 'Supplier out of stock'
                ]
            ], 200);
        }
        
        return Http::response([
            'data' => [
                'status' => 'success',
                'sn' => 'DF-SN-HAPPY-12345',
                'message' => 'Topup berhasil'
            ]
        ], 200);
    }
    
    if (str_contains($request->url(), 'api.kmsp.co.id')) {
        return Http::response([
            'status' => 'success',
            'sn' => 'KMSP-SN-FAILOVER-WORKED',
            'message' => 'Transaksi sukses lewat cadangan'
        ], 200);
    }
    
    if (str_contains($request->url(), 'api.fonnte.com')) {
        return Http::response([
            'status' => true,
            'reason' => 'Mocked success response'
        ], 200);
    }
    
    return Http::response([], 404);
});

// -------------------------------------------------------------
printStep("2. SKENARIO HAPPY PATH: A. BUAT TRANSAKSI (POST /api/transactions)");
// -------------------------------------------------------------

// Dispatch request to /api/transactions
$request = Request::create('/api/transactions', 'POST', [
    'product_id' => $product->id,
    'target_number' => '081234567890',
    'user_id' => $user->id
]);

$response = $app->handle($request);
echo "Response Status: " . $response->getStatusCode() . "\n";
echo "Response Body: " . $response->getContent() . "\n";

// Process queued jobs
\Illuminate\Support\Facades\Artisan::call('queue:work', ['--once' => true]);

printDBState();

// -------------------------------------------------------------
printStep("2. SKENARIO HAPPY PATH: B. SIMULASI WEBHOOK AUTOGOPAY PAYMENT");
// -------------------------------------------------------------

// reset transaction back to pending representing unpaid transaction
$transaction = Transaction::first()->fresh();
$transaction->update([
    'status' => 'pending',
    'serial_number' => null,
    'message' => 'Awaiting payment',
]);
$user->update(['balance' => 100000]); // Reset balance

printDBState();

// Construct AutoGoPay Webhook request
// Signature = hmac-sha256(payload, secret)
$secret = config('services.autogopay.secret') ?: 'default_secret';
$payload = json_encode([
    'reference_id' => $transaction->reference_id,
    'status' => 'settlement',
    'amount' => $transaction->sell_price
]);
$signature = hash_hmac('sha256', $payload, $secret);

$webhookRequest = Request::create('/api/webhook/autogopay', 'POST', [], [], [], [
    'HTTP_X-Signature' => $signature,
    'CONTENT_TYPE' => 'application/json',
], $payload);

$webhookResponse = $app->handle($webhookRequest);
echo "Webhook Response Status: " . $webhookResponse->getStatusCode() . "\n";
echo "Webhook Response Body: " . $webhookResponse->getContent() . "\n";

// Process queued jobs
\Illuminate\Support\Facades\Artisan::call('queue:work', ['--once' => true]);

printDBState();

// -------------------------------------------------------------
printStep("2. SKENARIO HAPPY PATH: C. SIMULASI PROVIDER CALLBACK (DIGIFLAZZ)");
// -------------------------------------------------------------

// Reset transaction status to pending to simulate provider pending status
$transaction = $transaction->fresh();
$transaction->update([
    'status' => 'pending',
    'serial_number' => null,
    'message' => 'Pending at provider'
]);

printDBState();

$expectedSign = md5('df_user' . 'df_key' . 'depok');
$callbackPayload = [
    'data' => [
        'ref_id' => $transaction->reference_id,
        'status' => 'success',
        'sn' => 'DF-SN-CALLBACK-FINAL',
        'message' => 'Transaksi Sukses',
        'sign' => $expectedSign
    ]
];

$callbackRequest = Request::create('/api/webhook/callback/digiflazz', 'POST', $callbackPayload);
$callbackResponse = $app->handle($callbackRequest);

echo "Callback Response Status: " . $callbackResponse->getStatusCode() . "\n";
echo "Callback Response Body: " . $callbackResponse->getContent() . "\n";

printDBState();


// -------------------------------------------------------------
printStep("3. SKENARIO FAILOVER (DIGIFLAZZ FAILS -> FALLBACK TO KMSP)");
// -------------------------------------------------------------

// Reset transaction and user balance
Transaction::truncate(); // Clear old transaction
$user->update(['balance' => 100000]);

// Dispatch order request
$request = Request::create('/api/transactions', 'POST', [
    'product_id' => $product->id,
    'target_number' => '08999999999', // Triggers the Digiflazz fail mockup
    'user_id' => $user->id
]);

$response = $app->handle($request);
echo "Failover Response Status: " . $response->getStatusCode() . "\n";
echo "Failover Response Body: " . $response->getContent() . "\n";

// Process queued jobs
\Illuminate\Support\Facades\Artisan::call('queue:work', ['--once' => true]);

printDBState();
