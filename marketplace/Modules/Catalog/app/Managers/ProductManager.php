<?php

declare(strict_types=1);

namespace Modules\Catalog\Managers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Modules\Catalog\DTOs\ProductDto;
use Modules\Catalog\DTOs\ProductSearchDto;
use Modules\Catalog\Models\Product;
use Modules\Catalog\Models\ProductImage;
use Modules\Catalog\Services\ProductImageService;
use Modules\Catalog\Services\ProductService;

class ProductManager
{
    public function __construct(
        private readonly ProductService $productService,
        private readonly ProductImageService $imageService,
    ) {
    }

    public function search(ProductSearchDto $dto): LengthAwarePaginator
    {
        return $this->productService->search($dto);
    }

    public function findBySlug(string $slug): Product
    {
        return $this->productService->findBySlugOrFail($slug);
    }

    public function paginateForVendor(int $vendorId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->productService->paginateForVendor($vendorId, $perPage);
    }

    public function create(ProductDto $dto): Product
    {
        return DB::transaction(fn () => $this->productService->create($dto));
    }

    public function updateOwnedBy(int $productId, int $vendorId, ProductDto $dto): Product
    {
        return DB::transaction(function () use ($productId, $vendorId, $dto): Product {
            $product = $this->productService->findOwnedByOrFail($productId, $vendorId);

            return $this->productService->update($product, $dto);
        });
    }

    public function deleteOwnedBy(int $productId, int $vendorId): bool
    {
        return DB::transaction(function () use ($productId, $vendorId): bool {
            $product = $this->productService->findOwnedByOrFail($productId, $vendorId);

            return $this->productService->delete($product);
        });
    }

    public function uploadImage(int $productId, int $vendorId, UploadedFile $file, bool $isPrimary = false): ProductImage
    {
        return DB::transaction(function () use ($productId, $vendorId, $file, $isPrimary): ProductImage {
            $product = $this->productService->findOwnedByOrFail($productId, $vendorId);

            return $this->imageService->upload($product, $file, $isPrimary);
        });
    }

    public function deleteImage(int $productId, int $vendorId, int $imageId): bool
    {
        return DB::transaction(function () use ($productId, $vendorId, $imageId): bool {
            $product = $this->productService->findOwnedByOrFail($productId, $vendorId);

            return $this->imageService->delete($product, $imageId);
        });
    }
}
