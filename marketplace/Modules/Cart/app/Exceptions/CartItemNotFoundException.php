<?php

declare(strict_types=1);

namespace Modules\Cart\Exceptions;

use App\Exceptions\Domain\DomainException;

class CartItemNotFoundException extends DomainException
{
    protected $message = 'Cart item not found.';

    public function httpStatus(): int
    {
        return 404;
    }
}
