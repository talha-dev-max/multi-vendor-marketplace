<?php

declare(strict_types=1);

namespace Modules\Order\Repositories;

use Modules\Order\Models\OrderItem;
use Modules\Order\Models\VendorOrder;
use Modules\Order\Repositories\Contracts\OrderItemRepositoryInterface;

class OrderItemRepository implements OrderItemRepositoryInterface
{
    public function create(
        VendorOrder $vendorOrder,
        int $productId,
        string $productName,
        float $unitPrice,
        int $quantity,
        float $lineTotal,
    ): OrderItem {
        return OrderItem::query()->create([
            'vendor_order_id' => $vendorOrder->id,
            'product_id' => $productId,
            'product_name_snapshot' => $productName,
            'unit_price' => $unitPrice,
            'quantity' => $quantity,
            'line_total' => $lineTotal,
        ]);
    }
}
