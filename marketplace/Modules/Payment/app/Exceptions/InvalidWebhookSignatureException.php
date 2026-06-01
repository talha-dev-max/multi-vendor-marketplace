<?php

declare(strict_types=1);

namespace Modules\Payment\Exceptions;

use App\Exceptions\Domain\DomainException;

class InvalidWebhookSignatureException extends DomainException
{
    protected $message = 'Invalid Stripe webhook signature.';

    public function httpStatus(): int
    {
        return 400;
    }
}
