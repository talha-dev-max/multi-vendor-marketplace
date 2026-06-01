<?php

declare(strict_types=1);

namespace Modules\Catalog\Exceptions;

use App\Exceptions\Domain\DomainException;

class CategoryNotFoundException extends DomainException
{
    protected $message = 'Category not found.';

    public function httpStatus(): int
    {
        return 404;
    }
}
