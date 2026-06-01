<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Vendor\Http\Controllers\Admin\VendorApprovalController;
use Modules\Vendor\Http\Controllers\VendorApplicationController;

Route::prefix('v1')->middleware('auth:sanctum')->group(function (): void {
    Route::post('/vendor/applications', [VendorApplicationController::class, 'apply'])
        ->name('vendor.apply');

    Route::middleware('role:vendor')->group(function (): void {
        Route::get('/vendor/profile', [VendorApplicationController::class, 'myProfile'])
            ->name('vendor.profile.show');
        Route::put('/vendor/profile', [VendorApplicationController::class, 'updateMyProfile'])
            ->name('vendor.profile.update');
    });

    Route::middleware('role:admin')->prefix('admin')->group(function (): void {
        Route::get('/vendors', [VendorApprovalController::class, 'index'])
            ->name('admin.vendors.index');
        Route::post('/vendors/{id}/approve', [VendorApprovalController::class, 'approve'])
            ->whereNumber('id')
            ->name('admin.vendors.approve');
        Route::post('/vendors/{id}/reject', [VendorApprovalController::class, 'reject'])
            ->whereNumber('id')
            ->name('admin.vendors.reject');
    });
});
