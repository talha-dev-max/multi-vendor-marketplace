<?php

declare(strict_types=1);

namespace Modules\Order\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'vendor_id' => $this->vendor_id,
            'subtotal' => (float) $this->subtotal,
            'commission' => (float) $this->commission,
            'net' => (float) $this->net,
            'status' => $this->status,
            'confirmed_at' => $this->confirmed_at?->toIso8601String(),
            'shipped_at' => $this->shipped_at?->toIso8601String(),
            'delivered_at' => $this->delivered_at?->toIso8601String(),
            'vendor' => $this->whenLoaded('vendor', fn () => [
                'id' => $this->vendor->id,
                'store_name' => $this->vendor->store_name,
                'store_slug' => $this->vendor->store_slug,
            ]),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
