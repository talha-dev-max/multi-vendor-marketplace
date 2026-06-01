<?php

declare(strict_types=1);

namespace Modules\Vendor\DTOs;

final readonly class RejectVendorDto
{
    public function __construct(
        public string $reason,
    ) {
    }
}
