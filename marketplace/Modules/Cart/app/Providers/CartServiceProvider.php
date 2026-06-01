<?php

declare(strict_types=1);

namespace Modules\Cart\Providers;

use Modules\Cart\Repositories\CartRepository;
use Modules\Cart\Repositories\Contracts\CartRepositoryInterface;
use Nwidart\Modules\Support\ModuleServiceProvider;

class CartServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Cart';

    protected string $nameLower = 'cart';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    public function register(): void
    {
        parent::register();

        $this->app->bind(CartRepositoryInterface::class, CartRepository::class);
    }
}
