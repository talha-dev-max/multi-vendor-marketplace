<?php

declare(strict_types=1);

namespace Modules\Catalog\Exceptions;

use App\Exceptions\Domain\DomainException;

class TooManyProductImagesException extends DomainException
{
    protected $message = 'Maximum number of product images exceeded.';

    public function httpStatus(): int
    {
        return 422;
    }
}
