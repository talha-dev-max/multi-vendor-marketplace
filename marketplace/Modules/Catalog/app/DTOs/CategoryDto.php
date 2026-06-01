<?php

declare(strict_types=1);

namespace Modules\Catalog\DTOs;

final readonly class CategoryDto
{
    public function __construct(
        public string $name,
        public ?int $parentId,
        public int $sortOrder,
        public bool $isActive,
    ) {
    }
}
