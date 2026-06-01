<?php

declare(strict_types=1);

namespace Modules\Vendor\DTOs;

final readonly class UpdateVendorProfileDto
{
    public function __construct(
        public string $storeName,
        public ?string $description = null,
    ) {
    }
}
