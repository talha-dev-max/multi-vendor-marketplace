<?php

declare(strict_types=1);

namespace Modules\Cart\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $items = $this->items->map(fn ($item) => [
            'id' => $item->id,
            'product_id' => $item->product_id,
            'quantity' => (int) $item->quantity,
            'unit_price' => (float) $item->unit_price_snapshot,
            'line_total' => (float) $item->unit_price_snapshot * $item->quantity,
            'product' => [
                'id' => $item->product->id,
                'name' => $item->product->name,
                'slug' => $item->product->slug,
                'price' => (float) $item->product->price,
                'stock' => (int) $item->product->stock,
                'primary_image' => $item->product->images->firstWhere('is_primary', true)?->path
                    ?? $item->product->images->first()?->path,
                'vendor' => [
                    'id' => $item->product->vendor->id,
                    'store_name' => $item->product->vendor->store_name,
                ],
            ],
        ])->all();

        $total = array_sum(array_map(fn ($i) => $i['line_total'], $items));

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'items' => $items,
            'total' => $total,
            'item_count' => count($items),
            'quantity_total' => array_sum(array_map(fn ($i) => $i['quantity'], $items)),
        ];
    }
}
