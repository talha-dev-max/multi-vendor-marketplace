<?php

declare(strict_types=1);

namespace Modules\Cart\Exceptions;

use App\Exceptions\Domain\DomainException;

class ProductNotAvailableException extends DomainException
{
    protected $message = 'Product is not available for purchase.';

    public function httpStatus(): int
    {
        return 422;
    }
}
