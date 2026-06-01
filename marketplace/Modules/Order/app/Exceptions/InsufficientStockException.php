<?php

declare(strict_types=1);

namespace Modules\Order\Exceptions;

use App\Exceptions\Domain\DomainException;

class InsufficientStockException extends DomainException
{
    protected $message = 'Insufficient stock for one or more items.';

    public function httpStatus(): int
    {
        return 409;
    }
}
