<?php

use App\Http\Controllers\AccountValidationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

// Public Pages
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/order/{slug}', [OrderController::class, 'show'])->name('order.show');
Route::post('/order', [OrderController::class, 'store'])->name('order.store');
Route::get('/invoice/{order_number}', [OrderController::class, 'invoice'])->name('order.invoice');
Route::get('/tracking', [OrderController::class, 'tracking'])->name('order.tracking');

// AJAX Account & Phone Validation Check
Route::post('/api/check-account', [AccountValidationController::class, 'check'])->name('account.check');

// Webhook Handlers
Route::post('/webhook/midtrans', [WebhookController::class, 'midtrans'])->name('webhook.midtrans');
Route::post('/webhook/digiflazz', [WebhookController::class, 'digiflazz'])->name('webhook.digiflazz');
