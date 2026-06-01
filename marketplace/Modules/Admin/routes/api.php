<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\DashboardController;
use Modules\Admin\Http\Controllers\UserController;

Route::prefix('v1')->middleware(['auth:sanctum', 'role:admin'])->group(function (): void {
    Route::get('/admin/dashboard/stats', [DashboardController::class, 'stats'])->name('admin.dashboard.stats');
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
});
