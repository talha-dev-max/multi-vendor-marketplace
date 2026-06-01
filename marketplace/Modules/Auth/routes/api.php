<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthController;
use Modules\Auth\Http\Controllers\EmailVerificationController;
use Modules\Auth\Http\Controllers\PasswordResetController;

Route::prefix('v1')->group(function (): void {
    Route::middleware('throttle:20,1')->group(function (): void {
        Route::post('/auth/register', [AuthController::class, 'register'])->name('auth.register');
        Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');
        Route::post('/auth/forgot-password', [PasswordResetController::class, 'sendLink'])->name('password.email');
        Route::post('/auth/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
    });

    Route::get('/auth/verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::get('/auth/me', [AuthController::class, 'me'])->name('auth.me');
        Route::post('/auth/email/resend', [EmailVerificationController::class, 'resend'])
            ->middleware('throttle:6,1')
            ->name('verification.send');
    });
});
