<?php

declare(strict_types=1);

namespace Modules\Catalog\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vendor_id' => $this->vendor_id,
            'category_id' => $this->category_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => (float) $this->price,
            'stock' => (int) $this->stock,
            'status' => $this->status,
            'images' => $this->whenLoaded('images', fn () => $this->images->map(fn ($img) => [
                'id' => $img->id,
                'path' => $img->path,
                'thumb_path' => $img->thumb_path,
                'url' => $img->path ? asset('storage/'.$img->path) : null,
                'thumb_url' => $img->thumb_path ? asset('storage/'.$img->thumb_path) : null,
                'is_primary' => (bool) $img->is_primary,
            ])->all()),
            'category' => $this->whenLoaded('category', fn () => $this->category ? [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ] : null),
            'vendor' => $this->whenLoaded('vendor', fn () => $this->vendor ? [
                'id' => $this->vendor->id,
                'store_name' => $this->vendor->store_name,
                'store_slug' => $this->vendor->store_slug,
            ] : null),
        ];
    }
}
