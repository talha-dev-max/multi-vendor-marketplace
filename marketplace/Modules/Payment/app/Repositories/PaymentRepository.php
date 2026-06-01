<?php

declare(strict_types=1);

namespace Modules\Payment\Repositories;

use Modules\Payment\Models\Payment;
use Modules\Payment\Repositories\Contracts\PaymentRepositoryInterface;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function findById(int $id): ?Payment
    {
        return Payment::query()->find($id);
    }

    public function findByPaymentIntentId(string $paymentIntentId): ?Payment
    {
        return Payment::query()
            ->where('stripe_payment_intent_id', $paymentIntentId)
            ->first();
    }

    public function findByCheckoutSessionId(string $sessionId): ?Payment
    {
        return Payment::query()
            ->where('stripe_checkout_session_id', $sessionId)
            ->first();
    }

    public function create(int $orderId, string $method, float $amount, string $currency): Payment
    {
        return Payment::query()->create([
            'order_id' => $orderId,
            'method' => $method,
            'amount' => $amount,
            'currency' => $currency,
            'status' => Payment::STATUS_PENDING,
        ]);
    }

    public function attachStripeSession(Payment $payment, string $sessionId): Payment
    {
        $payment->fill(['stripe_checkout_session_id' => $sessionId])->save();

        return $payment->fresh();
    }

    public function markPaid(Payment $payment, ?string $paymentIntentId = null): Payment
    {
        $payment->fill([
            'status' => Payment::STATUS_PAID,
            'paid_at' => now(),
            'stripe_payment_intent_id' => $paymentIntentId ?? $payment->stripe_payment_intent_id,
        ])->save();

        return $payment->fresh();
    }

    public function markFailed(Payment $payment): Payment
    {
        $payment->fill(['status' => Payment::STATUS_FAILED])->save();

        return $payment->fresh();
    }
}
