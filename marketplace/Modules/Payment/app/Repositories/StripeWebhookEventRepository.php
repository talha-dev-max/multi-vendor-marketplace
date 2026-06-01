<?php

declare(strict_types=1);

namespace Modules\Payment\Repositories;

use Modules\Payment\Models\StripeWebhookEvent;
use Modules\Payment\Repositories\Contracts\StripeWebhookEventRepositoryInterface;

class StripeWebhookEventRepository implements StripeWebhookEventRepositoryInterface
{
    public function findByStripeEventId(string $stripeEventId): ?StripeWebhookEvent
    {
        return StripeWebhookEvent::query()
            ->where('stripe_event_id', $stripeEventId)
            ->first();
    }

    public function create(string $stripeEventId, string $type, array $payload): StripeWebhookEvent
    {
        return StripeWebhookEvent::query()->create([
            'stripe_event_id' => $stripeEventId,
            'type' => $type,
            'payload' => $payload,
        ]);
    }

    public function markProcessed(StripeWebhookEvent $event): StripeWebhookEvent
    {
        $event->fill(['processed_at' => now()])->save();

        return $event->fresh();
    }
}
