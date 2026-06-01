<?php

declare(strict_types=1);

namespace Modules\Order\DTOs;

final readonly class PlaceOrderDto
{
    public function __construct(
        public int $userId,
        public string $paymentMethod,
        public ShippingAddressDto $shippingAddress,
        public ?string $stripeSuccessUrl = null,
        public ?string $stripeCancelUrl = null,
    ) {
    }
}
