<?php

declare(strict_types=1);

namespace Modules\Auth\Exceptions;

use App\Exceptions\Domain\DomainException;

class PasswordResetFailedException extends DomainException
{
    protected $message = 'Password reset failed.';

    public function httpStatus(): int
    {
        return 422;
    }
}
