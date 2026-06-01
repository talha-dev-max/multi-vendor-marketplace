<?php

declare(strict_types=1);

namespace Modules\Order\DTOs;

use Modules\Order\Models\Order;

final readonly class CheckoutResultDto
{
    public function __construct(
        public Order $order,
        public ?string $stripeCheckoutUrl = null,
    ) {
    }
}
