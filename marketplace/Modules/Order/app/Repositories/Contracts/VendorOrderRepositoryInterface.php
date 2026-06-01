<?php

declare(strict_types=1);

namespace Modules\Order\Repositories\Contracts;

use DateTimeInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Order\Models\Order;
use Modules\Order\Models\VendorOrder;

interface VendorOrderRepositoryInterface
{
    public function create(Order $order, int $vendorId, float $subtotal, float $commission, float $net): VendorOrder;

    public function findByIdForVendor(int $id, int $vendorId): ?VendorOrder;

    public function findAllForOrder(int $orderId): Collection;

    public function updateStatus(VendorOrder $vendorOrder, string $status, ?DateTimeInterface $timestamp = null): VendorOrder;

    public function paginateForVendor(int $vendorId, int $perPage = 15): LengthAwarePaginator;
}
