<?php

declare(strict_types=1);

namespace Modules\Catalog\Providers;

use Modules\Catalog\Repositories\CategoryRepository;
use Modules\Catalog\Repositories\Contracts\CategoryRepositoryInterface;
use Modules\Catalog\Repositories\Contracts\ProductImageRepositoryInterface;
use Modules\Catalog\Repositories\Contracts\ProductRepositoryInterface;
use Modules\Catalog\Repositories\ProductImageRepository;
use Modules\Catalog\Repositories\ProductRepository;
use Nwidart\Modules\Support\ModuleServiceProvider;

class CatalogServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Catalog';

    protected string $nameLower = 'catalog';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    public function register(): void
    {
        parent::register();

        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(ProductImageRepositoryInterface::class, ProductImageRepository::class);
    }
}
