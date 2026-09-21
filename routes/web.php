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

/*
|--------------------------------------------------------------------------
| cPanel Setup Helper (Untuk Server Tanpa Akses SSH/Terminal)
| Akses: https://domainanda.com/cpanel-setup?key=gamenexa2026
|--------------------------------------------------------------------------
*/
Route::get('/cpanel-setup', function (\Illuminate\Http\Request $request) {
    $secret = env('CPANEL_SETUP_KEY', 'gamenexa2026');
    if ($request->query('key') !== $secret) {
        abort(403, 'Akses Ditolak: Kunci keamanan salah.');
    }

    $results = [];

    // 1. Storage link
    try {
        \Illuminate\Support\Facades\Artisan::call('storage:link');
        $results['storage:link'] = trim(\Illuminate\Support\Facades\Artisan::output());
    } catch (\Throwable $e) {
        $results['storage:link'] = $e->getMessage();
    }

    // 2. Migrate database
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $results['migrate'] = trim(\Illuminate\Support\Facades\Artisan::output());
    } catch (\Throwable $e) {
        $results['migrate'] = $e->getMessage();
    }

    // 3. Clear cache
    try {
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        $results['optimize:clear'] = trim(\Illuminate\Support\Facades\Artisan::output());
    } catch (\Throwable $e) {
        $results['optimize:clear'] = $e->getMessage();
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Setup cPanel berhasil dijalankan!',
        'details' => $results,
    ]);
});

