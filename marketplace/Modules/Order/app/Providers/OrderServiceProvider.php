<?php

declare(strict_types=1);

namespace Modules\Order\Providers;

use Modules\Order\Repositories\Contracts\OrderItemRepositoryInterface;
use Modules\Order\Repositories\Contracts\OrderRepositoryInterface;
use Modules\Order\Repositories\Contracts\VendorOrderRepositoryInterface;
use Modules\Order\Repositories\OrderItemRepository;
use Modules\Order\Repositories\OrderRepository;
use Modules\Order\Repositories\VendorOrderRepository;
use Nwidart\Modules\Support\ModuleServiceProvider;

class OrderServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Order';

    protected string $nameLower = 'order';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    public function register(): void
    {
        parent::register();

        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(VendorOrderRepositoryInterface::class, VendorOrderRepository::class);
        $this->app->bind(OrderItemRepositoryInterface::class, OrderItemRepository::class);
    }
}
