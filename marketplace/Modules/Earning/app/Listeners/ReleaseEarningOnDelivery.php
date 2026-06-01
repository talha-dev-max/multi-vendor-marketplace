<?php

declare(strict_types=1);

namespace Modules\Earning\Listeners;

use Modules\Earning\Managers\EarningManager;
use Modules\Order\Events\VendorOrderDelivered;

class ReleaseEarningOnDelivery
{
    public function __construct(
        private readonly EarningManager $earningManager,
    ) {
    }

    public function handle(VendorOrderDelivered $event): void
    {
        $this->earningManager->releaseForVendorOrder($event->vendorOrder->id);
    }
}
