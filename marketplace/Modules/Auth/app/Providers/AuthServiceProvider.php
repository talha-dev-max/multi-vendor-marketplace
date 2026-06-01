<?php

declare(strict_types=1);

namespace Modules\Auth\Providers;

use Modules\Auth\Repositories\Contracts\UserRepositoryInterface;
use Modules\Auth\Repositories\UserRepository;
use Nwidart\Modules\Support\ModuleServiceProvider;

class AuthServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Auth';

    protected string $nameLower = 'auth';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    public function register(): void
    {
        parent::register();

        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }
}
