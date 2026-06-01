<?php

declare(strict_types=1);

namespace Modules\Admin\Services;

use Modules\Admin\Repositories\Contracts\AdminStatsRepositoryInterface;

class AdminDashboardService
{
    public function __construct(
        private readonly AdminStatsRepositoryInterface $stats,
    ) {
    }

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
    public function stats(): array
    {
        return $this->stats->dashboardStats();
    }
}
