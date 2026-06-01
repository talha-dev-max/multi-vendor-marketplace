<?php

declare(strict_types=1);

namespace Modules\Payment\Services;

use Modules\Order\Models\Order;
use Modules\Payment\DTOs\StripeCheckoutResultDto;
use Modules\Payment\Models\Payment;
use Modules\Payment\Repositories\Contracts\PaymentRepositoryInterface;

class PaymentService
{
    public function __construct(
        private readonly PaymentRepositoryInterface $payments,
        private readonly StripeService $stripeService,
    ) {
    }

    public function recordCodPayment(Order $order): Payment
    {
        return $this->payments->create(
            orderId: $order->id,
            method: Payment::METHOD_COD,
            amount: (float) $order->total,
            currency: $order->currency,
        );
    }

    public function startStripeCheckout(Order $order, string $successUrl, string $cancelUrl): StripeCheckoutResultDto
    {
        $payment = $this->payments->create(
            orderId: $order->id,
            method: Payment::METHOD_STRIPE,
            amount: (float) $order->total,
            currency: $order->currency,
        );

        $result = $this->stripeService->createCheckoutSession($order, $successUrl, $cancelUrl);

        $this->payments->attachStripeSession($payment, $result->sessionId);

        return $result;
    }

    public function findByCheckoutSessionId(string $sessionId): ?Payment
    {
        return $this->payments->findByCheckoutSessionId($sessionId);
    }

    public function findByPaymentIntentId(string $paymentIntentId): ?Payment
    {
        return $this->payments->findByPaymentIntentId($paymentIntentId);
    }

    public function markPaid(Payment $payment, ?string $paymentIntentId = null): Payment
    {
        return $this->payments->markPaid($payment, $paymentIntentId);
    }

    public function markFailed(Payment $payment): Payment
    {
        return $this->payments->markFailed($payment);
    }
}
