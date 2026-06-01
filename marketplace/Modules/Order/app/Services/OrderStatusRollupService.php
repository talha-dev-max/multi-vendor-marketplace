<?php

declare(strict_types=1);

namespace Modules\Order\Services;

use Modules\Order\Models\Order;
use Modules\Order\Models\VendorOrder;
use Modules\Order\Repositories\Contracts\OrderRepositoryInterface;
use Modules\Order\Repositories\Contracts\VendorOrderRepositoryInterface;

class OrderStatusRollupService
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,
        private readonly VendorOrderRepositoryInterface $vendorOrders,
    ) {
    }

    public function rollupForOrder(int $orderId): ?Order
    {
        $order = $this->orders->findById($orderId);

        if ($order === null) {
            return null;
        }

        $statuses = $this->vendorOrders->findAllForOrder($orderId)->pluck('status')->all();

        if ($statuses === []) {
            return $order;
        }

        $computed = $this->computeParentStatus($statuses);

        return $this->orders->updateStatus($order, $computed);
    }

    /**
     * @param  array<int, string>  $childStatuses
     */
    private function computeParentStatus(array $childStatuses): string
    {
        $allMatch = fn (string $s): bool => count(array_filter($childStatuses, fn ($x) => $x !== $s)) === 0;
        $anyMatch = fn (string $s): bool => in_array($s, $childStatuses, true);

        return match (true) {
            $allMatch(VendorOrder::STATUS_CANCELED) => Order::STATUS_CANCELED,
            $allMatch(VendorOrder::STATUS_DELIVERED) => Order::STATUS_DELIVERED,
            $allMatch(VendorOrder::STATUS_SHIPPED) => Order::STATUS_SHIPPED,
            $allMatch(VendorOrder::STATUS_CONFIRMED) => Order::STATUS_CONFIRMED,
            $anyMatch(VendorOrder::STATUS_DELIVERED) => Order::STATUS_PARTIALLY_DELIVERED,
            $anyMatch(VendorOrder::STATUS_SHIPPED) => Order::STATUS_PARTIALLY_SHIPPED,
            default => Order::STATUS_PENDING,
        };
    }
}
