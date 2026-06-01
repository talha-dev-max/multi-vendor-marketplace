<?php

declare(strict_types=1);

namespace Modules\Earning\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Earning\Models\VendorEarning;

interface VendorEarningRepositoryInterface
{
    public function findByVendorOrderId(int $vendorOrderId): ?VendorEarning;

    public function create(int $vendorOrderId, int $vendorId, float $gross, float $commission, float $net): VendorEarning;

    public function markReleased(VendorEarning $earning): VendorEarning;

    public function paginateForVendor(int $vendorId, int $perPage = 15): LengthAwarePaginator;

    /**
     * @return array{gross: float, net: float, pending: float, released: float}
     */
    public function summaryForVendor(int $vendorId): array;
}
