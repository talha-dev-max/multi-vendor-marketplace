<?php

declare(strict_types=1);

namespace Modules\Payment\Exceptions;

use App\Exceptions\Domain\DomainException;

class PaymentFailedException extends DomainException
{
    protected $message = 'Payment processing failed.';

    public function httpStatus(): int
    {
        return 402;
    }
}
