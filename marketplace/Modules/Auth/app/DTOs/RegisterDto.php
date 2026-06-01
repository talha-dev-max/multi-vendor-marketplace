<?php

declare(strict_types=1);

namespace Modules\Auth\DTOs;

final readonly class RegisterDto
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {
    }
}
