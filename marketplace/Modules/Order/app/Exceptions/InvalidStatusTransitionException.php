<?php

declare(strict_types=1);

namespace Modules\Order\Exceptions;

use App\Exceptions\Domain\DomainException;

class InvalidStatusTransitionException extends DomainException
{
    protected $message = 'Invalid vendor order status transition.';

    public function httpStatus(): int
    {
        return 422;
    }
}
