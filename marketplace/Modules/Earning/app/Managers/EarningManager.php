<?php

declare(strict_types=1);

namespace Modules\Earning\Managers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\Earning\Models\VendorEarning;
use Modules\Earning\Services\EarningService;
use Modules\Order\Models\VendorOrder;

class EarningManager
{
    public function __construct(
        private readonly EarningService $earningService,
    ) {
    }

    public function recordForVendorOrder(VendorOrder $vendorOrder): VendorEarning
    {
        return DB::transaction(fn () => $this->earningService->recordForVendorOrder($vendorOrder));
    }

    public function releaseForVendorOrder(int $vendorOrderId): ?VendorEarning
    {
        return DB::transaction(fn () => $this->earningService->releaseForVendorOrder($vendorOrderId));
    }

    public function paginateForVendor(int $vendorId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->earningService->paginateForVendor($vendorId, $perPage);
    }

    /**
     * @return array{gross: float, net: float, pending: float, released: float}
     */
    public function summaryForVendor(int $vendorId): array
    {
        return $this->earningService->summaryForVendor($vendorId);
    }
}
