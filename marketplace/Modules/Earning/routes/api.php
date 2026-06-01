<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Earning\Http\Controllers\VendorEarningController;

Route::prefix('v1')->middleware(['auth:sanctum', 'role:vendor'])->group(function (): void {
    Route::get('/vendor/earnings', [VendorEarningController::class, 'index'])->name('vendor.earnings.index');
    Route::get('/vendor/earnings/summary', [VendorEarningController::class, 'summary'])->name('vendor.earnings.summary');
});
