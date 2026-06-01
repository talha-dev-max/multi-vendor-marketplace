<?php

declare(strict_types=1);

namespace Modules\Vendor\Exceptions;

use App\Exceptions\Domain\DomainException;

class VendorAlreadyAppliedException extends DomainException
{
    protected $message = 'User has already applied to become a vendor.';

    public function httpStatus(): int
    {
        return 409;
    }
}
