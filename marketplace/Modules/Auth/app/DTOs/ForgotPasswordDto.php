<?php

declare(strict_types=1);

namespace Modules\Auth\DTOs;

final readonly class ForgotPasswordDto
{
    public function __construct(
        public string $email,
    ) {
    }
}
