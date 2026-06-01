<?php

declare(strict_types=1);

namespace Modules\Order\Repositories\Contracts;

use Modules\Order\Models\OrderItem;
use Modules\Order\Models\VendorOrder;

interface OrderItemRepositoryInterface
{
    public function create(
        VendorOrder $vendorOrder,
        int $productId,
        string $productName,
        float $unitPrice,
        int $quantity,
        float $lineTotal,
    ): OrderItem;
}
