<?php

declare(strict_types=1);

namespace Modules\Catalog\Repositories;

use Modules\Catalog\Models\Product;
use Modules\Catalog\Models\ProductImage;
use Modules\Catalog\Repositories\Contracts\ProductImageRepositoryInterface;

class ProductImageRepository implements ProductImageRepositoryInterface
{
    public function countForProduct(Product $product): int
    {
        return ProductImage::query()->where('product_id', $product->id)->count();
    }

    public function create(Product $product, string $path, ?string $thumbPath, bool $isPrimary, int $sortOrder): ProductImage
    {
        return ProductImage::query()->create([
            'product_id' => $product->id,
            'path' => $path,
            'thumb_path' => $thumbPath,
            'is_primary' => $isPrimary,
            'sort_order' => $sortOrder,
        ]);
    }

    public function delete(ProductImage $image): bool
    {
        return (bool) $image->delete();
    }

    public function findByIdForProduct(int $imageId, int $productId): ?ProductImage
    {
        return ProductImage::query()
            ->where('id', $imageId)
            ->where('product_id', $productId)
            ->first();
    }
}
