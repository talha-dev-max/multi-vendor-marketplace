<?php

declare(strict_types=1);

namespace Modules\Payment\Managers;

use Illuminate\Support\Facades\DB;
use Modules\Payment\Services\StripeService;
use Modules\Payment\Services\WebhookDispatchService;

class WebhookManager
{
    public function __construct(
        private readonly StripeService $stripeService,
        private readonly WebhookDispatchService $dispatcher,
    ) {
    }

    public function handleStripeWebhook(string $payload, string $signatureHeader): void
    {
        $event = $this->stripeService->verifyWebhook($payload, $signatureHeader);

        DB::transaction(fn () => $this->dispatcher->process($event));
    }
}
