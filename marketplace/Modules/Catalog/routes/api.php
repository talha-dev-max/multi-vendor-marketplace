<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Catalog\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use Modules\Catalog\Http\Controllers\PublicCategoryController;
use Modules\Catalog\Http\Controllers\PublicProductController;
use Modules\Catalog\Http\Controllers\Vendor\ProductController as VendorProductController;

Route::prefix('v1')->group(function (): void {
    // Public browsing (throttled tighter on search)
    Route::middleware('throttle:60,1')->group(function (): void {
        Route::get('/products', [PublicProductController::class, 'index'])->name('products.index');
        Route::get('/products/{slug}', [PublicProductController::class, 'show'])->name('products.show');
        Route::get('/categories', [PublicCategoryController::class, 'index'])->name('categories.index');
    });

    Route::middleware('auth:sanctum')->group(function (): void {
        // Vendor product CRUD
        Route::middleware('role:vendor')->prefix('vendor')->group(function (): void {
            Route::get('/products', [VendorProductController::class, 'index'])->name('vendor.products.index');
            Route::post('/products', [VendorProductController::class, 'store'])->name('vendor.products.store');
            Route::put('/products/{id}', [VendorProductController::class, 'update'])->whereNumber('id')->name('vendor.products.update');
            Route::delete('/products/{id}', [VendorProductController::class, 'destroy'])->whereNumber('id')->name('vendor.products.destroy');
            Route::post('/products/{id}/images', [VendorProductController::class, 'uploadImage'])->whereNumber('id')->name('vendor.products.images.upload');
            Route::delete('/products/{id}/images/{imageId}', [VendorProductController::class, 'deleteImage'])->whereNumber(['id', 'imageId'])->name('vendor.products.images.delete');
        });

        // Admin category CRUD
        Route::middleware('role:admin')->prefix('admin')->group(function (): void {
            Route::get('/categories', [AdminCategoryController::class, 'index'])->name('admin.categories.index');
            Route::post('/categories', [AdminCategoryController::class, 'store'])->name('admin.categories.store');
            Route::put('/categories/{id}', [AdminCategoryController::class, 'update'])->whereNumber('id')->name('admin.categories.update');
            Route::delete('/categories/{id}', [AdminCategoryController::class, 'destroy'])->whereNumber('id')->name('admin.categories.destroy');
        });
    });
});
