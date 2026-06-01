<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Order\Http\Controllers\Admin\OrderController as AdminOrderController;
use Modules\Order\Http\Controllers\Customer\OrderController as CustomerOrderController;
use Modules\Order\Http\Controllers\Vendor\OrderController as VendorOrderController;

Route::prefix('v1')->middleware('auth:sanctum')->group(function (): void {
    // Customer
    Route::post('/orders', [CustomerOrderController::class, 'place'])->name('orders.place');
    Route::get('/orders', [CustomerOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [CustomerOrderController::class, 'show'])->whereNumber('id')->name('orders.show');

    // Vendor sub-orders
    Route::middleware('role:vendor')->prefix('vendor')->group(function (): void {
        Route::get('/orders', [VendorOrderController::class, 'index'])->name('vendor.orders.index');
        Route::get('/orders/{id}', [VendorOrderController::class, 'show'])->whereNumber('id')->name('vendor.orders.show');
        Route::put('/orders/{id}/status', [VendorOrderController::class, 'updateStatus'])->whereNumber('id')->name('vendor.orders.status');
    });

    // Admin
    Route::middleware('role:admin')->prefix('admin')->group(function (): void {
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
        Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->whereNumber('id')->name('admin.orders.show');
    });
});
