<?php

declare(strict_types=1);

namespace Modules\Payment\Providers;

use Modules\Payment\Repositories\Contracts\PaymentRepositoryInterface;
use Modules\Payment\Repositories\Contracts\StripeWebhookEventRepositoryInterface;
use Modules\Payment\Repositories\PaymentRepository;
use Modules\Payment\Repositories\StripeWebhookEventRepository;
use Nwidart\Modules\Support\ModuleServiceProvider;

class PaymentServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Payment';

    protected string $nameLower = 'payment';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    public function register(): void
    {
        parent::register();

        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);
        $this->app->bind(StripeWebhookEventRepositoryInterface::class, StripeWebhookEventRepository::class);
    }
}
