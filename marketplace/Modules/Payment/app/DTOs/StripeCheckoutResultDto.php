<?php

declare(strict_types=1);

namespace Modules\Payment\DTOs;

final readonly class StripeCheckoutResultDto
{
    public function __construct(
        public string $sessionId,
        public string $checkoutUrl,
    ) {
    }
}
