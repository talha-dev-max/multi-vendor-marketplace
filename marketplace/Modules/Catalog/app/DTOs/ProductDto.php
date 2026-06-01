<?php

declare(strict_types=1);

namespace Modules\Catalog\DTOs;

final readonly class ProductDto
{
    public function __construct(
        public int $vendorId,
        public ?int $categoryId,
        public string $name,
        public ?string $description,
        public float $price,
        public int $stock,
        public string $status,
    ) {
    }
}
