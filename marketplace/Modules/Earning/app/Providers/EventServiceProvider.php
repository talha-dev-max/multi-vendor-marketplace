<?php

declare(strict_types=1);

namespace Modules\Earning\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Earning\Listeners\ReleaseEarningOnDelivery;
use Modules\Order\Events\VendorOrderDelivered;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        VendorOrderDelivered::class => [
            ReleaseEarningOnDelivery::class,
        ],
    ];

    protected static $shouldDiscoverEvents = false;

    protected function configureEmailVerification(): void
    {
    }
}
