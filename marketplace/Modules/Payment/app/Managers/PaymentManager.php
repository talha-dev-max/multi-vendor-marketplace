<?php

declare(strict_types=1);

namespace Modules\Payment\Managers;

use Modules\Order\Models\Order;
use Modules\Payment\DTOs\StripeCheckoutResultDto;
use Modules\Payment\Models\Payment;
use Modules\Payment\Services\PaymentService;

class PaymentManager
{
    public function __construct(
        private readonly PaymentService $paymentService,
    ) {
    }

    public function recordCodPayment(Order $order): Payment
    {
        return $this->paymentService->recordCodPayment($order);
    }

    public function startStripeCheckout(Order $order, string $successUrl, string $cancelUrl): StripeCheckoutResultDto
    {
        return $this->paymentService->startStripeCheckout($order, $successUrl, $cancelUrl);
    }
}
