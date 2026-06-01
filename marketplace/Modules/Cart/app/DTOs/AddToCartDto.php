<?php

declare(strict_types=1);

namespace Modules\Cart\DTOs;

final readonly class AddToCartDto
{
    public function __construct(
        public int $userId,
        public int $productId,
        public int $quantity,
    ) {
    }
}
