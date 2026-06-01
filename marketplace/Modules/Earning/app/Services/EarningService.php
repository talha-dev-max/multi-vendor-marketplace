<?php

declare(strict_types=1);

namespace Modules\Earning\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Earning\Models\VendorEarning;
use Modules\Earning\Repositories\Contracts\VendorEarningRepositoryInterface;
use Modules\Order\Models\VendorOrder;

class EarningService
{
    public function __construct(
        private readonly VendorEarningRepositoryInterface $earnings,
    ) {
    }

    public function recordForVendorOrder(VendorOrder $vendorOrder): VendorEarning
    {
        $existing = $this->earnings->findByVendorOrderId($vendorOrder->id);

        if ($existing !== null) {
            return $existing;
        }

        return $this->earnings->create(
            vendorOrderId: $vendorOrder->id,
            vendorId: $vendorOrder->vendor_id,
            gross: (float) $vendorOrder->subtotal,
            commission: (float) $vendorOrder->commission,
            net: (float) $vendorOrder->net,
        );
    }

    public function releaseForVendorOrder(int $vendorOrderId): ?VendorEarning
    {
        $earning = $this->earnings->findByVendorOrderId($vendorOrderId);

        if ($earning === null || $earning->status === VendorEarning::STATUS_RELEASED) {
            return $earning;
        }

        return $this->earnings->markReleased($earning);
    }

    public function paginateForVendor(int $vendorId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->earnings->paginateForVendor($vendorId, $perPage);
    }

    /**
     * @return array{gross: float, net: float, pending: float, released: float}
     */
    public function summaryForVendor(int $vendorId): array
    {
        return $this->earnings->summaryForVendor($vendorId);
    }
}
