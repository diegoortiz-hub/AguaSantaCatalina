<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\CouponController;

/*
|--------------------------------------------------------------------------
| API Routes — Aguas Santa Catalina
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // ── Productos ─────────────────────────────────────────────────────────
    Route::get('/products',       [ProductController::class, 'index']);
    Route::get('/products/{slug}',[ProductController::class, 'show']);

    // ── Categorías ────────────────────────────────────────────────────────
    Route::get('/categories', [CategoryController::class, 'index']);

    // ── Carrito ───────────────────────────────────────────────────────────
    Route::post('/cart',                       [CartController::class, 'store']);
    Route::get('/cart/{session}',              [CartController::class, 'show']);
    Route::delete('/cart/{session}/{product}', [CartController::class, 'destroy']);

    // ── Pedidos ───────────────────────────────────────────────────────────
    Route::post('/orders',     [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);

    // ── Cupones ───────────────────────────────────────────────────────────
    Route::post('/coupons/validate', [CouponController::class, 'validate']);
});

// Alias sin versión para compatibilidad con el frontend
Route::prefix('')->group(function () {
    Route::get('/products',       [ProductController::class, 'index']);
    Route::get('/products/{slug}',[ProductController::class, 'show']);
    Route::get('/categories',     [CategoryController::class, 'index']);
    Route::post('/cart',                       [CartController::class, 'store']);
    Route::get('/cart/{session}',              [CartController::class, 'show']);
    Route::delete('/cart/{session}/{product}', [CartController::class, 'destroy']);
    Route::post('/orders',     [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/coupons/validate', [CouponController::class, 'validate']);
});
