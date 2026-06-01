<?php

declare(strict_types=1);

namespace Modules\Cart\DTOs;

final readonly class UpdateCartItemDto
{
    public function __construct(
        public int $userId,
        public int $cartItemId,
        public int $quantity,
    ) {
    }
}
