<?php

declare(strict_types=1);

namespace Modules\Vendor\Exceptions;

use App\Exceptions\Domain\DomainException;

class VendorProfileNotFoundException extends DomainException
{
    protected $message = 'Vendor profile not found.';

    public function httpStatus(): int
    {
        return 404;
    }
}
