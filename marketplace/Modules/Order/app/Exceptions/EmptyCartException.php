<?php

declare(strict_types=1);

namespace Modules\Order\Exceptions;

use App\Exceptions\Domain\DomainException;

class EmptyCartException extends DomainException
{
    protected $message = 'Cart is empty.';

    public function httpStatus(): int
    {
        return 422;
    }
}
