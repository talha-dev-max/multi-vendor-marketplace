<?php

declare(strict_types=1);

namespace Modules\Admin\Providers;

use Modules\Admin\Repositories\AdminStatsRepository;
use Modules\Admin\Repositories\Contracts\AdminStatsRepositoryInterface;
use Nwidart\Modules\Support\ModuleServiceProvider;

class AdminServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Admin';

    protected string $nameLower = 'admin';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    public function register(): void
    {
        parent::register();

        $this->app->bind(AdminStatsRepositoryInterface::class, AdminStatsRepository::class);
    }
}
