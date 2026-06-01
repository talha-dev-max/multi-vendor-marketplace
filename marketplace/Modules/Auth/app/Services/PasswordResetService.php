<?php

declare(strict_types=1);

namespace Modules\Auth\Services;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;
use Modules\Auth\DTOs\ForgotPasswordDto;
use Modules\Auth\DTOs\ResetPasswordDto;
use Modules\Auth\Exceptions\PasswordResetFailedException;
use Modules\Auth\Repositories\Contracts\UserRepositoryInterface;

class PasswordResetService
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
    ) {
    }

    public function sendResetLink(ForgotPasswordDto $dto): string
    {
        $status = Password::sendResetLink(['email' => $dto->email]);

        if ($status !== Password::RESET_LINK_SENT) {
            throw new PasswordResetFailedException((string) __($status));
        }

        return (string) __($status);
    }

    public function reset(ResetPasswordDto $dto): string
    {
        $status = Password::reset(
            [
                'email' => $dto->email,
                'password' => $dto->password,
                'password_confirmation' => $dto->password,
                'token' => $dto->token,
            ],
            function (User $user, string $password): void {
                $this->users->updatePassword($user, $password);
                event(new PasswordReset($user));
            },
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw new PasswordResetFailedException((string) __($status));
        }

        return (string) __($status);
    }
}
