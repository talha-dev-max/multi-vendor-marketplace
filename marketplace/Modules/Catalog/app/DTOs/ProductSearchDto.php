<?php

declare(strict_types=1);

namespace Modules\Catalog\DTOs;

final readonly class ProductSearchDto
{
    public function __construct(
        public ?string $query = null,
        public ?int $categoryId = null,
        public ?int $vendorId = null,
        public ?float $priceMin = null,
        public ?float $priceMax = null,
        public string $sort = 'newest',
        public int $page = 1,
        public int $perPage = 20,
    ) {
    }
}
