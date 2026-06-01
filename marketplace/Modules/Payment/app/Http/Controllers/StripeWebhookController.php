<?php

declare(strict_types=1);

namespace Modules\Payment\Http\Controllers;

use App\Exceptions\Domain\DomainException;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Payment\Managers\WebhookManager;
use Throwable;

class StripeWebhookController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly WebhookManager $webhookManager,
    ) {
    }

    public function handle(Request $request): JsonResponse
    {
        try {
            $this->webhookManager->handleStripeWebhook(
                payload: $request->getContent(),
                signatureHeader: (string) $request->header('Stripe-Signature', ''),
            );

            return $this->success(null, 'Webhook received.');
        } catch (DomainException $e) {
            Log::warning('Stripe webhook rejected', ['message' => $e->getMessage()]);

            return $this->error($e->getMessage(), $e->httpStatus());
        } catch (Throwable $e) {
            Log::error('Stripe webhook processing failed', ['exception' => $e]);

            return $this->error('Webhook processing failed.', 500);
        }
    }
}
