<?php

declare(strict_types=1);

namespace Modules\Admin\Repositories\Contracts;

interface AdminStatsRepositoryInterface
{
    /**
     * @return array{
     *   users_total: int,
     *   vendors_approved: int,
     *   vendors_pending: int,
     *   products_total: int,
     *   products_active: int,
     *   orders_total: int,
     *   orders_pending: int,
     *   gross_sales: float,
     * }
     */
    public function dashboardStats(): array;
}
