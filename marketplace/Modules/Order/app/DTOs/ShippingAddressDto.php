<?php

declare(strict_types=1);

namespace Modules\Order\DTOs;

final readonly class ShippingAddressDto
{
    public function __construct(
        public string $fullName,
        public string $phone,
        public string $addressLine1,
        public ?string $addressLine2,
        public string $city,
        public string $state,
        public string $postalCode,
        public string $country,
    ) {
    }

    public function toArray(): array
    {
        return [
            'full_name' => $this->fullName,
            'phone' => $this->phone,
            'address_line1' => $this->addressLine1,
            'address_line2' => $this->addressLine2,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postalCode,
            'country' => $this->country,
        ];
    }
}
