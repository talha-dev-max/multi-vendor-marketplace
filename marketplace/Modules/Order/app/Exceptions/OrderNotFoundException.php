<?php

declare(strict_types=1);

namespace Modules\Order\Exceptions;

use App\Exceptions\Domain\DomainException;

class OrderNotFoundException extends DomainException
{
    protected $message = 'Order not found.';

    public function httpStatus(): int
    {
        return 404;
    }
}
