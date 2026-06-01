<?php

declare(strict_types=1);

namespace Modules\Vendor\Providers;

use Modules\Vendor\Repositories\Contracts\VendorProfileRepositoryInterface;
use Modules\Vendor\Repositories\VendorProfileRepository;
use Nwidart\Modules\Support\ModuleServiceProvider;

class VendorServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Vendor';

    protected string $nameLower = 'vendor';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    public function register(): void
    {
        parent::register();

        $this->app->bind(VendorProfileRepositoryInterface::class, VendorProfileRepository::class);
    }
}
