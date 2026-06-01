<?php

declare(strict_types=1);

namespace Modules\Earning\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorEarningResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vendor_order_id' => $this->vendor_order_id,
            'gross' => (float) $this->gross,
            'commission' => (float) $this->commission,
            'net' => (float) $this->net,
            'status' => $this->status,
            'released_at' => $this->released_at?->toIso8601String(),
            'order_id' => $this->vendorOrder?->order_id,
        ];
    }
}
