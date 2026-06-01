<?php

declare(strict_types=1);

namespace Modules\Earning\Providers;

use Modules\Earning\Repositories\Contracts\VendorEarningRepositoryInterface;
use Modules\Earning\Repositories\VendorEarningRepository;
use Nwidart\Modules\Support\ModuleServiceProvider;

class EarningServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Earning';

    protected string $nameLower = 'earning';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    public function register(): void
    {
        parent::register();

        $this->app->bind(VendorEarningRepositoryInterface::class, VendorEarningRepository::class);
    }
}
