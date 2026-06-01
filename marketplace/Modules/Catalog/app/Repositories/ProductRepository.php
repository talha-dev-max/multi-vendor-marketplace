<?php

declare(strict_types=1);

namespace Modules\Catalog\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Catalog\DTOs\ProductDto;
use Modules\Catalog\DTOs\ProductSearchDto;
use Modules\Catalog\Models\Product;
use Modules\Catalog\Repositories\Contracts\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    public function findById(int $id): ?Product
    {
        return Product::query()->with('images', 'category', 'vendor')->find($id);
    }

    public function findByIdOwnedBy(int $id, int $vendorId): ?Product
    {
        return Product::query()
            ->with('images', 'category')
            ->where('id', $id)
            ->where('vendor_id', $vendorId)
            ->first();
    }

    public function findBySlug(string $slug): ?Product
    {
        return Product::query()
            ->with('images', 'category', 'vendor')
            ->where('slug', $slug)
            ->first();
    }

    public function searchFilter(ProductSearchDto $dto): LengthAwarePaginator
    {
        $query = Product::query()
            ->with('images', 'category', 'vendor')
            ->active();

        if ($dto->query !== null && $dto->query !== '') {
            $query->where('name', 'like', '%'.$dto->query.'%');
        }

        if ($dto->categoryId !== null) {
            $query->where('category_id', $dto->categoryId);
        }

        if ($dto->vendorId !== null) {
            $query->where('vendor_id', $dto->vendorId);
        }

        if ($dto->priceMin !== null) {
            $query->where('price', '>=', $dto->priceMin);
        }

        if ($dto->priceMax !== null) {
            $query->where('price', '<=', $dto->priceMax);
        }

        match ($dto->sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'name' => $query->orderBy('name'),
            default => $query->latest(),
        };

        return $query->paginate(perPage: $dto->perPage, page: $dto->page);
    }

    public function paginateForVendor(int $vendorId, int $perPage = 15): LengthAwarePaginator
    {
        return Product::query()
            ->with('images', 'category')
            ->where('vendor_id', $vendorId)
            ->latest()
            ->paginate($perPage);
    }

    public function createFromDto(ProductDto $dto, string $slug): Product
    {
        return Product::query()->create([
            'vendor_id' => $dto->vendorId,
            'category_id' => $dto->categoryId,
            'name' => $dto->name,
            'slug' => $slug,
            'description' => $dto->description,
            'price' => $dto->price,
            'stock' => $dto->stock,
            'status' => $dto->status,
        ]);
    }

    public function updateFromDto(Product $product, ProductDto $dto): Product
    {
        $product->fill([
            'category_id' => $dto->categoryId,
            'name' => $dto->name,
            'description' => $dto->description,
            'price' => $dto->price,
            'stock' => $dto->stock,
            'status' => $dto->status,
        ])->save();

        return $product->fresh(['images', 'category']);
    }

    public function delete(Product $product): bool
    {
        return (bool) $product->delete();
    }

    public function lockForUpdateByIds(array $productIds): Collection
    {
        return Product::query()
            ->whereIn('id', $productIds)
            ->lockForUpdate()
            ->get();
    }

    public function decrementStock(Product $product, int $quantity): Product
    {
        $product->fill(['stock' => $product->stock - $quantity])->save();

        return $product->fresh();
    }
}
