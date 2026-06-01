<?php

declare(strict_types=1);

namespace Modules\Order\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Order\Models\Order;

interface OrderRepositoryInterface
{
    public function findByIdForCustomer(int $id, int $customerId): ?Order;

    public function findById(int $id): ?Order;

    public function create(int $customerId, float $total, string $currency, string $paymentMethod, array $shippingAddress): Order;

    public function updateStatus(Order $order, string $status): Order;

    public function updatePaymentStatus(Order $order, string $paymentStatus): Order;

    public function paginateForCustomer(int $customerId, int $perPage = 15): LengthAwarePaginator;

    public function paginateAll(int $perPage = 15): LengthAwarePaginator;
}
