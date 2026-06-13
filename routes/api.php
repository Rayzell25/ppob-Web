<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\WebhookController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Transaction API Routes
Route::post('/transactions', [TransactionController::class, 'store']);
Route::get('/transactions/history', [TransactionController::class, 'history'])->middleware('auth:sanctum');
Route::get('/transactions/{trx_id}', [TransactionController::class, 'show']);

// Webhook & Callback Routes
Route::post('/webhook/autogopay', [WebhookController::class, 'handleAutoGoPay']);
Route::post('/webhook/callback/{provider}', [WebhookController::class, 'handleProviderCallback']);
