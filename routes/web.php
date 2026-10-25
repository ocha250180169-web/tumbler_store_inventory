<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

// ===== Guest =====
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:10,1') // batasi percobaan login
        ->name('login.attempt');
});

// ===== Harus login =====
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- Admin saja (dilindungi di server dengan Gate, bukan sekadar sembunyi tombol) ---
    // Penting: grup ini didaftarkan SEBELUM products.show agar /products/create tidak
    // dianggap sebagai {product} = "create".
    Route::middleware('can:manage-master')->group(function () {
        Route::resource('products', ProductController::class)->except(['index', 'show']);
        Route::resource('categories', CategoryController::class)->except('show');
        Route::resource('suppliers', SupplierController::class)->except('show');
    });

    Route::middleware('can:manage-users')->group(function () {
        Route::resource('users', UserController::class)->except('show');
    });

    Route::middleware('can:cancel-transaction')->group(function () {
        Route::post('/sales/{sale}/cancel', [SaleController::class, 'cancel'])->name('sales.cancel');
        Route::post('/purchases/{purchase}/cancel', [PurchaseController::class, 'cancel'])->name('purchases.cancel');
    });

    // --- Admin & Staff ---
    Route::get('/products/low-stock', [ProductController::class, 'lowStock'])->name('products.low-stock');
    Route::resource('products', ProductController::class)->only(['index', 'show']);

    Route::middleware('can:manage-transactions')->group(function () {
        Route::resource('sales', SaleController::class)->only(['index', 'create', 'store', 'show']);
        Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'show']);
    });
});
