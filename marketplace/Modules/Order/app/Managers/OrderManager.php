<?php

declare(strict_types=1);

namespace Modules\Order\Managers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Order\Models\Order;
use Modules\Order\Services\OrderService;

class OrderManager
{
    public function __construct(
        private readonly OrderService $orderService,
    ) {
    }

    public function findForCustomer(int $id, int $customerId): Order
    {
        return $this->orderService->findForCustomerOrFail($id, $customerId);
    }

    public function findById(int $id): Order
    {
        return $this->orderService->findByIdOrFail($id);
    }

    public function paginateForCustomer(int $customerId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->orderService->paginateForCustomer($customerId, $perPage);
    }

    public function paginateAll(int $perPage = 15): LengthAwarePaginator
    {
        return $this->orderService->paginateAll($perPage);
    }
}
