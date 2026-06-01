<?php

declare(strict_types=1);

namespace Modules\Admin\Repositories;

use App\Models\User;
use Modules\Admin\Repositories\Contracts\AdminStatsRepositoryInterface;
use Modules\Catalog\Models\Product;
use Modules\Order\Models\Order;
use Modules\Vendor\Models\VendorProfile;

class AdminStatsRepository implements AdminStatsRepositoryInterface
{
    public function dashboardStats(): array
    {
        return [
            'users_total' => User::query()->count(),
            'vendors_approved' => VendorProfile::query()
                ->where('status', VendorProfile::STATUS_APPROVED)
                ->count(),
            'vendors_pending' => VendorProfile::query()
                ->where('status', VendorProfile::STATUS_PENDING)
                ->count(),
            'products_total' => Product::query()->count(),
            'products_active' => Product::query()
                ->where('status', Product::STATUS_ACTIVE)
                ->count(),
            'orders_total' => Order::query()->count(),
            'orders_pending' => Order::query()
                ->where('status', Order::STATUS_PENDING)
                ->count(),
            'gross_sales' => (float) Order::query()
                ->where('payment_status', Order::PAYMENT_STATUS_PAID)
                ->sum('total'),
        ];
    }
}
