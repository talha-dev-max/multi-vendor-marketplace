<?php

declare(strict_types=1);

namespace Modules\Order\Managers;

use Illuminate\Support\Facades\DB;
use Modules\Order\DTOs\CheckoutResultDto;
use Modules\Order\DTOs\PlaceOrderDto;
use Modules\Order\Services\CheckoutService;

class CheckoutManager
{
    public function __construct(
        private readonly CheckoutService $checkoutService,
    ) {
    }

    public function placeOrder(PlaceOrderDto $dto): CheckoutResultDto
    {
        return DB::transaction(fn (): CheckoutResultDto => $this->checkoutService->execute($dto));
    }
}
