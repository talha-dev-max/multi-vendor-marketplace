<?php

declare(strict_types=1);

namespace Modules\Order\DTOs;

final readonly class UpdateVendorOrderStatusDto
{
    public function __construct(
        public int $vendorOrderId,
        public int $vendorId,
        public string $newStatus,
    ) {
    }
}
