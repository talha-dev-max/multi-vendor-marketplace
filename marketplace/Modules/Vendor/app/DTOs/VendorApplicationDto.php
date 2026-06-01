<?php

declare(strict_types=1);

namespace Modules\Vendor\DTOs;

final readonly class VendorApplicationDto
{
    public function __construct(
        public int $userId,
        public string $storeName,
        public ?string $description = null,
    ) {
    }
}
