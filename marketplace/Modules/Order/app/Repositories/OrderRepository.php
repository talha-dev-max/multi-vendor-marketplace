<?php

declare(strict_types=1);

namespace Modules\Order\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Order\Models\Order;
use Modules\Order\Repositories\Contracts\OrderRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{
    public function findByIdForCustomer(int $id, int $customerId): ?Order
    {
        return Order::query()
            ->with('vendorOrders.items.product', 'vendorOrders.vendor')
            ->where('id', $id)
            ->where('customer_id', $customerId)
            ->first();
    }

    public function findById(int $id): ?Order
    {
        return Order::query()
            ->with('vendorOrders.items.product', 'vendorOrders.vendor', 'customer')
            ->find($id);
    }

    public function create(int $customerId, float $total, string $currency, string $paymentMethod, array $shippingAddress): Order
    {
        return Order::query()->create([
            'customer_id' => $customerId,
            'total' => $total,
            'currency' => $currency,
            'status' => Order::STATUS_PENDING,
            'payment_method' => $paymentMethod,
            'payment_status' => Order::PAYMENT_STATUS_PENDING,
            'shipping_address' => $shippingAddress,
            'placed_at' => now(),
        ]);
    }

    public function updateStatus(Order $order, string $status): Order
    {
        $order->fill(['status' => $status])->save();

        return $order->fresh();
    }

    public function updatePaymentStatus(Order $order, string $paymentStatus): Order
    {
        $order->fill(['payment_status' => $paymentStatus])->save();

        return $order->fresh();
    }

    public function paginateForCustomer(int $customerId, int $perPage = 15): LengthAwarePaginator
    {
        return Order::query()
            ->with('vendorOrders.items.product', 'vendorOrders.vendor')
            ->where('customer_id', $customerId)
            ->latest()
            ->paginate($perPage);
    }

    public function paginateAll(int $perPage = 15): LengthAwarePaginator
    {
        return Order::query()
            ->with('customer', 'vendorOrders.vendor')
            ->latest()
            ->paginate($perPage);
    }
}
