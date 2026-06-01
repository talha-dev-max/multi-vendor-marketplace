<?php

declare(strict_types=1);

namespace Modules\Catalog\Exceptions;

use App\Exceptions\Domain\DomainException;

class ProductNotFoundException extends DomainException
{
    protected $message = 'Product not found.';

    public function httpStatus(): int
    {
        return 404;
    }
}
