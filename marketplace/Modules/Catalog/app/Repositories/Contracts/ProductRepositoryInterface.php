<?php

declare(strict_types=1);

namespace Modules\Catalog\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Catalog\DTOs\ProductDto;
use Modules\Catalog\DTOs\ProductSearchDto;
use Modules\Catalog\Models\Product;

interface ProductRepositoryInterface
{
    public function findById(int $id): ?Product;

    public function findByIdOwnedBy(int $id, int $vendorId): ?Product;

    public function findBySlug(string $slug): ?Product;

    public function searchFilter(ProductSearchDto $dto): LengthAwarePaginator;

    public function paginateForVendor(int $vendorId, int $perPage = 15): LengthAwarePaginator;

    public function createFromDto(ProductDto $dto, string $slug): Product;

    public function updateFromDto(Product $product, ProductDto $dto): Product;

    public function delete(Product $product): bool;

    /**
     * Lock and fetch products for a given list of IDs with SELECT ... FOR UPDATE.
     * Used by CheckoutManager to prevent stock race conditions.
     *
     * @param  array<int, int>  $productIds
     */
    public function lockForUpdateByIds(array $productIds): Collection;

    public function decrementStock(Product $product, int $quantity): Product;
}
