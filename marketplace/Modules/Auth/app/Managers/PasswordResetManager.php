<?php

declare(strict_types=1);

namespace Modules\Auth\Managers;

use Modules\Auth\DTOs\ForgotPasswordDto;
use Modules\Auth\DTOs\ResetPasswordDto;
use Modules\Auth\Services\PasswordResetService;

class PasswordResetManager
{
    public function __construct(
        private readonly PasswordResetService $passwordResetService,
    ) {
    }

    public function sendResetLink(ForgotPasswordDto $dto): string
    {
        return $this->passwordResetService->sendResetLink($dto);
    }

    public function reset(ResetPasswordDto $dto): string
    {
        return $this->passwordResetService->reset($dto);
    }
}
