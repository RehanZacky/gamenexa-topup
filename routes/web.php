<?php

use App\Http\Controllers\AccountValidationController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DigiflazzController as AdminDigiflazzController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\TopupTransactionController as AdminTopupTransactionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Customer Routes
|--------------------------------------------------------------------------
*/
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

/*
|--------------------------------------------------------------------------
| Admin Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

/*
|--------------------------------------------------------------------------
| Protected Admin Routes (Middleware: auth, admin)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['admin'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Category CRUD
    Route::resource('categories', AdminCategoryController::class)->except(['show']);
    Route::post('categories/{category}/toggle-status', [AdminCategoryController::class, 'toggleStatus'])->name('categories.toggle-status');

    // Product CRUD, Digiflazz SKU Fetcher & Pricing
    Route::get('products/digiflazz-skus', [AdminProductController::class, 'getDigiflazzSkus'])->name('products.digiflazz-skus');
    Route::resource('products', AdminProductController::class)->except(['show']);
    Route::post('products/{product}/toggle-status', [AdminProductController::class, 'toggleStatus'])->name('products.toggle-status');
    Route::post('products/bulk-price-update', [AdminProductController::class, 'bulkPriceUpdate'])->name('products.bulk-price-update');

    // Digiflazz Control & Sync
    Route::get('digiflazz', [AdminDigiflazzController::class, 'index'])->name('digiflazz.index');
    Route::post('digiflazz/sync', [AdminDigiflazzController::class, 'sync'])->name('digiflazz.sync');

    // Orders & Transactions Monitoring
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::get('payments', [AdminPaymentController::class, 'index'])->name('payments.index');
    Route::get('topup-transactions', [AdminTopupTransactionController::class, 'index'])->name('topup_transactions.index');
});
