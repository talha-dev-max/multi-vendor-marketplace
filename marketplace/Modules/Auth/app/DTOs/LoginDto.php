<?php

declare(strict_types=1);

namespace Modules\Auth\DTOs;

final readonly class LoginDto
{
    public function __construct(
        public string $email,
        public string $password,
        public ?string $deviceName = null,
    ) {
    }
}
