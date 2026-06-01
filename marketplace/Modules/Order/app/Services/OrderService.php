<?php

declare(strict_types=1);

namespace Modules\Order\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Order\Exceptions\OrderNotFoundException;
use Modules\Order\Models\Order;
use Modules\Order\Repositories\Contracts\OrderRepositoryInterface;

class OrderService
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,
    ) {
    }

    public function findForCustomerOrFail(int $id, int $customerId): Order
    {
        $order = $this->orders->findByIdForCustomer($id, $customerId);

        if ($order === null) {
            throw new OrderNotFoundException();
        }

        return $order;
    }

    public function findByIdOrFail(int $id): Order
    {
        $order = $this->orders->findById($id);

        if ($order === null) {
            throw new OrderNotFoundException();
        }

        return $order;
    }

    public function findById(int $id): ?Order
    {
        return $this->orders->findById($id);
    }

    public function paginateForCustomer(int $customerId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->orders->paginateForCustomer($customerId, $perPage);
    }

    public function paginateAll(int $perPage = 15): LengthAwarePaginator
    {
        return $this->orders->paginateAll($perPage);
    }

    public function markPaymentPaid(Order $order): Order
    {
        return $this->orders->updatePaymentStatus($order, Order::PAYMENT_STATUS_PAID);
    }

    public function markPaymentFailed(Order $order): Order
    {
        return $this->orders->updatePaymentStatus($order, Order::PAYMENT_STATUS_FAILED);
    }
}
