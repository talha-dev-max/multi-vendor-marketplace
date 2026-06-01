<?php

declare(strict_types=1);

namespace Modules\Order\Repositories;

use DateTimeInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Order\Models\Order;
use Modules\Order\Models\VendorOrder;
use Modules\Order\Repositories\Contracts\VendorOrderRepositoryInterface;

class VendorOrderRepository implements VendorOrderRepositoryInterface
{
    public function create(Order $order, int $vendorId, float $subtotal, float $commission, float $net): VendorOrder
    {
        return VendorOrder::query()->create([
            'order_id' => $order->id,
            'vendor_id' => $vendorId,
            'subtotal' => $subtotal,
            'commission' => $commission,
            'net' => $net,
            'status' => VendorOrder::STATUS_PENDING,
        ]);
    }

    public function findByIdForVendor(int $id, int $vendorId): ?VendorOrder
    {
        return VendorOrder::query()
            ->with('items.product', 'order.customer')
            ->where('id', $id)
            ->where('vendor_id', $vendorId)
            ->first();
    }

    public function findAllForOrder(int $orderId): Collection
    {
        return VendorOrder::query()->where('order_id', $orderId)->get();
    }

    public function updateStatus(VendorOrder $vendorOrder, string $status, ?DateTimeInterface $timestamp = null): VendorOrder
    {
        $updates = ['status' => $status];

        if ($timestamp !== null) {
            $updates[match ($status) {
                VendorOrder::STATUS_CONFIRMED => 'confirmed_at',
                VendorOrder::STATUS_SHIPPED => 'shipped_at',
                VendorOrder::STATUS_DELIVERED => 'delivered_at',
                default => 'updated_at',
            }] = $timestamp;
        }

        $vendorOrder->fill($updates)->save();

        return $vendorOrder->fresh(['items.product', 'order']);
    }

    public function paginateForVendor(int $vendorId, int $perPage = 15): LengthAwarePaginator
    {
        return VendorOrder::query()
            ->with('items.product', 'order.customer')
            ->where('vendor_id', $vendorId)
            ->latest()
            ->paginate($perPage);
    }
}
