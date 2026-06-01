<?php

declare(strict_types=1);

namespace Modules\Payment\Services;

use Illuminate\Support\Facades\Log;
use Modules\Order\Services\OrderService;
use Modules\Payment\Models\Payment;
use Modules\Payment\Repositories\Contracts\StripeWebhookEventRepositoryInterface;
use Stripe\Event;

/**
 * Processes verified Stripe events. Idempotent: checks stripe_webhook_events before acting.
 *
 * Cross-module writes go through OrderService (service-to-service),
 * not through OrderRepository directly.
 */
class WebhookDispatchService
{
    public function __construct(
        private readonly StripeWebhookEventRepositoryInterface $events,
        private readonly PaymentService $paymentService,
        private readonly OrderService $orderService,
    ) {
    }

    public function process(Event $event): void
    {
        if ($this->events->findByStripeEventId($event->id) !== null) {
            return;
        }

        $record = $this->events->create($event->id, $event->type, $event->toArray());

        match ($event->type) {
            'checkout.session.completed' => $this->handleCheckoutCompleted($event),
            'payment_intent.succeeded' => $this->handlePaymentIntentSucceeded($event),
            'payment_intent.payment_failed' => $this->handlePaymentIntentFailed($event),
            default => Log::info('Unhandled Stripe event', ['type' => $event->type, 'id' => $event->id]),
        };

        $this->events->markProcessed($record);
    }

    private function handleCheckoutCompleted(Event $event): void
    {
        $data = $event->data->object->toArray();
        $sessionId = $data['id'] ?? null;
        $paymentIntentId = $data['payment_intent'] ?? null;

        if ($sessionId === null) {
            return;
        }

        $payment = $this->paymentService->findByCheckoutSessionId((string) $sessionId);

        if ($payment === null) {
            Log::warning('Stripe checkout.session.completed for unknown session', ['session_id' => $sessionId]);

            return;
        }

        $this->paymentService->markPaid($payment, $paymentIntentId);
        $this->markOrderAsPaid($payment);
    }

    private function handlePaymentIntentSucceeded(Event $event): void
    {
        $data = $event->data->object->toArray();
        $paymentIntentId = $data['id'] ?? null;

        if ($paymentIntentId === null) {
            return;
        }

        $payment = $this->paymentService->findByPaymentIntentId((string) $paymentIntentId);

        if ($payment === null) {
            return;
        }

        $this->paymentService->markPaid($payment);
        $this->markOrderAsPaid($payment);
    }

    private function handlePaymentIntentFailed(Event $event): void
    {
        $data = $event->data->object->toArray();
        $paymentIntentId = $data['id'] ?? null;

        if ($paymentIntentId === null) {
            return;
        }

        $payment = $this->paymentService->findByPaymentIntentId((string) $paymentIntentId);

        if ($payment === null) {
            return;
        }

        $this->paymentService->markFailed($payment);

        $order = $this->orderService->findById($payment->order_id);
        if ($order !== null) {
            $this->orderService->markPaymentFailed($order);
        }
    }

    private function markOrderAsPaid(Payment $payment): void
    {
        $order = $this->orderService->findById($payment->order_id);
        if ($order !== null) {
            $this->orderService->markPaymentPaid($order);
        }
    }
}
