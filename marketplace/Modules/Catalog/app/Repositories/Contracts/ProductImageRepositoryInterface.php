<?php

declare(strict_types=1);

namespace Modules\Catalog\Repositories\Contracts;

use Modules\Catalog\Models\Product;
use Modules\Catalog\Models\ProductImage;

interface ProductImageRepositoryInterface
{
    public function countForProduct(Product $product): int;

    public function create(Product $product, string $path, ?string $thumbPath, bool $isPrimary, int $sortOrder): ProductImage;

    public function delete(ProductImage $image): bool;

    public function findByIdForProduct(int $imageId, int $productId): ?ProductImage;
}
