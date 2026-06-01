<?php

declare(strict_types=1);

namespace Modules\Order\Managers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\Order\DTOs\UpdateVendorOrderStatusDto;
use Modules\Order\Models\VendorOrder;
use Modules\Order\Services\OrderStatusRollupService;
use Modules\Order\Services\VendorOrderService;

class VendorOrderManager
{
    public function __construct(
        private readonly VendorOrderService $vendorOrderService,
        private readonly OrderStatusRollupService $rollupService,
    ) {
    }

    public function paginateForVendor(int $vendorId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->vendorOrderService->paginateForVendor($vendorId, $perPage);
    }

    public function findForVendor(int $id, int $vendorId): VendorOrder
    {
        return $this->vendorOrderService->findForVendorOrFail($id, $vendorId);
    }

    public function updateStatus(UpdateVendorOrderStatusDto $dto): VendorOrder
    {
        return DB::transaction(function () use ($dto): VendorOrder {
            $updated = $this->vendorOrderService->updateStatus($dto);

            // After child status change, recompute the parent order status
            $this->rollupService->rollupForOrder($updated->order_id);

            return $updated->fresh(['items.product', 'order']);
        });
    }
}
