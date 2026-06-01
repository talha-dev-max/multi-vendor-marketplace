<?php

declare(strict_types=1);

namespace Modules\Payment\Repositories\Contracts;

use Modules\Payment\Models\Payment;

interface PaymentRepositoryInterface
{
    public function findById(int $id): ?Payment;

    public function findByPaymentIntentId(string $paymentIntentId): ?Payment;

    public function findByCheckoutSessionId(string $sessionId): ?Payment;

    public function create(int $orderId, string $method, float $amount, string $currency): Payment;

    public function attachStripeSession(Payment $payment, string $sessionId): Payment;

    public function markPaid(Payment $payment, ?string $paymentIntentId = null): Payment;

    public function markFailed(Payment $payment): Payment;
}
