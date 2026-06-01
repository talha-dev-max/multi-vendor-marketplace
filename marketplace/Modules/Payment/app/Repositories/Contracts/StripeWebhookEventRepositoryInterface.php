<?php

declare(strict_types=1);

namespace Modules\Payment\Repositories\Contracts;

use Modules\Payment\Models\StripeWebhookEvent;

interface StripeWebhookEventRepositoryInterface
{
    public function findByStripeEventId(string $stripeEventId): ?StripeWebhookEvent;

    public function create(string $stripeEventId, string $type, array $payload): StripeWebhookEvent;

    public function markProcessed(StripeWebhookEvent $event): StripeWebhookEvent;
}
