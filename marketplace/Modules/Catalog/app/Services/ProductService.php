<?php

declare(strict_types=1);

namespace Modules\Catalog\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Modules\Catalog\DTOs\ProductDto;
use Modules\Catalog\DTOs\ProductSearchDto;
use Modules\Catalog\Exceptions\ProductNotFoundException;
use Modules\Catalog\Models\Product;
use Modules\Catalog\Repositories\Contracts\ProductRepositoryInterface;

class ProductService
{
    public function __construct(
        private readonly ProductRepositoryInterface $products,
    ) {
    }

    public function search(ProductSearchDto $dto): LengthAwarePaginator
    {
        return $this->products->searchFilter($dto);
    }

    public function findBySlugOrFail(string $slug): Product
    {
        $product = $this->products->findBySlug($slug);

        if ($product === null) {
            throw new ProductNotFoundException();
        }

        return $product;
    }

    public function findOwnedByOrFail(int $id, int $vendorId): Product
    {
        $product = $this->products->findByIdOwnedBy($id, $vendorId);

        if ($product === null) {
            throw new ProductNotFoundException();
        }

        return $product;
    }

    public function paginateForVendor(int $vendorId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->products->paginateForVendor($vendorId, $perPage);
    }

    public function create(ProductDto $dto): Product
    {
        return $this->products->createFromDto($dto, $this->uniqueSlug($dto->name));
    }

    public function update(Product $product, ProductDto $dto): Product
    {
        return $this->products->updateFromDto($product, $dto);
    }

    public function delete(Product $product): bool
    {
        return $this->products->delete($product);
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while ($this->products->findBySlug($slug) !== null) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
