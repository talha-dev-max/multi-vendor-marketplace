<?php

declare(strict_types=1);

namespace Modules\Order\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Order\DTOs\UpdateVendorOrderStatusDto;
use Modules\Order\Events\VendorOrderDelivered;
use Modules\Order\Exceptions\InvalidStatusTransitionException;
use Modules\Order\Exceptions\OrderNotFoundException;
use Modules\Order\Models\VendorOrder;
use Modules\Order\Repositories\Contracts\VendorOrderRepositoryInterface;

class VendorOrderService
{
    /**
     * Allowed forward transitions for a vendor sub-order.
     *
     * @var array<string, array<int, string>>
     */
    private const TRANSITIONS = [
        VendorOrder::STATUS_PENDING => [VendorOrder::STATUS_CONFIRMED, VendorOrder::STATUS_CANCELED],
        VendorOrder::STATUS_CONFIRMED => [VendorOrder::STATUS_SHIPPED, VendorOrder::STATUS_CANCELED],
        VendorOrder::STATUS_SHIPPED => [VendorOrder::STATUS_DELIVERED],
    ];

    public function __construct(
        private readonly VendorOrderRepositoryInterface $vendorOrders,
    ) {
    }

    public function findForVendorOrFail(int $id, int $vendorId): VendorOrder
    {
        $vendorOrder = $this->vendorOrders->findByIdForVendor($id, $vendorId);

        if ($vendorOrder === null) {
            throw new OrderNotFoundException();
        }

        return $vendorOrder;
    }

    public function updateStatus(UpdateVendorOrderStatusDto $dto): VendorOrder
    {
        $vendorOrder = $this->findForVendorOrFail($dto->vendorOrderId, $dto->vendorId);

        $this->assertTransitionAllowed($vendorOrder->status, $dto->newStatus);

        $updated = $this->vendorOrders->updateStatus($vendorOrder, $dto->newStatus, now());

        if ($dto->newStatus === VendorOrder::STATUS_DELIVERED) {
            event(new VendorOrderDelivered($updated));
        }

        return $updated;
    }

    public function paginateForVendor(int $vendorId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->vendorOrders->paginateForVendor($vendorId, $perPage);
    }

    private function assertTransitionAllowed(string $current, string $target): void
    {
        $allowed = self::TRANSITIONS[$current] ?? [];

        if (! in_array($target, $allowed, true)) {
            throw new InvalidStatusTransitionException(
                "Cannot transition vendor order from [{$current}] to [{$target}].",
            );
        }
    }
}
