<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Cart\Http\Controllers\CartController;

Route::prefix('v1')->middleware('auth:sanctum')->group(function (): void {
    Route::get('/cart', [CartController::class, 'show'])->name('cart.show');
    Route::post('/cart/items', [CartController::class, 'addItem'])->name('cart.items.add');
    Route::put('/cart/items/{id}', [CartController::class, 'updateItem'])->whereNumber('id')->name('cart.items.update');
    Route::delete('/cart/items/{id}', [CartController::class, 'removeItem'])->whereNumber('id')->name('cart.items.remove');
    Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');
});
