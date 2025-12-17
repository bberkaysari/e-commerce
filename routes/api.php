<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Cart\CartController;
use App\Http\Controllers\Api\Catalog\ProductController;
use App\Http\Controllers\Api\Catalog\VariantController;
use App\Http\Controllers\Api\Order\OrderController;
use App\Http\Controllers\Api\Order\PaymentController;
use App\Http\Controllers\Api\Import\ImportController;

// Auth routes (public)
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:register');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');

Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);

    // Products
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::put('/products/{id}', [ProductController::class, 'update']);

    // Variants
    Route::get('/variants', [VariantController::class, 'index']);
    Route::post('/variants', [VariantController::class, 'store']);
    Route::get('/variants/{id}', [VariantController::class, 'show']);
    Route::put('/variants/{id}', [VariantController::class, 'update']);

    // Cart
    Route::get('/cart', [CartController::class, 'show']);
    Route::post('/cart/items', [CartController::class, 'add']);
    Route::put('/cart/items/{variantId}', [CartController::class, 'update']) ->whereNumber('variantId');
    Route::delete('/cart/items/{variantId}', [CartController::class, 'remove'])-> whereNumber('variantId');

    // Orders
    Route::post('/checkout', [OrderController::class, 'checkout']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);
    Route::post('/orders/{id}/refund', [OrderController::class, 'refund']);

    // Payment
    Route::post('/payments', [PaymentController::class, 'pay']);

    // Import
    Route::post('/imports/products', [ImportController::class, 'import']);
    Route::get('/imports/batches/{batchId}', [ImportController::class, 'status']);
});