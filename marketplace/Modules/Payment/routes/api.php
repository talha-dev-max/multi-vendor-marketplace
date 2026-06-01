<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Payment\Http\Controllers\StripeWebhookController;

Route::prefix('v1')->group(function (): void {
    Route::post('/webhooks/stripe', [StripeWebhookController::class, 'handle'])
        ->middleware('throttle:60,1')
        ->name('webhooks.stripe');
});
