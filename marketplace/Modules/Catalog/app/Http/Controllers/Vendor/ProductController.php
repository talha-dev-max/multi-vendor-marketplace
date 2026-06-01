<?php

declare(strict_types=1);

namespace Modules\Catalog\Http\Controllers\Vendor;

use App\Exceptions\Domain\DomainException;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Catalog\Http\Requests\StoreProductRequest;
use Modules\Catalog\Http\Requests\UploadProductImageRequest;
use Modules\Catalog\Http\Resources\ProductResource;
use Modules\Catalog\Managers\ProductManager;
use Modules\Vendor\Models\VendorProfile;
use Throwable;

class ProductController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly ProductManager $productManager,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $vendor = $this->vendorProfileForUser($request);
            $paginator = $this->productManager->paginateForVendor($vendor->id, (int) $request->query('per_page', 15));

            return $this->success([
                'items' => ProductResource::collection($paginator->items()),
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                ],
            ]);
        } catch (Throwable $e) {
            Log::error('Vendor product list failed', ['exception' => $e]);

            return $this->error('Unable to fetch products.', 500);
        }
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        try {
            $product = $this->productManager->create($request->toDto());

            return $this->success(ProductResource::make($product), 'Product created.', 201);
        } catch (Throwable $e) {
            Log::error('Vendor product create failed', ['exception' => $e]);

            return $this->error('Unable to create product.', 500);
        }
    }

    public function update(StoreProductRequest $request, int $id): JsonResponse
    {
        try {
            $vendor = $this->vendorProfileForUser($request);
            $product = $this->productManager->updateOwnedBy($id, $vendor->id, $request->toDto());

            return $this->success(ProductResource::make($product), 'Product updated.');
        } catch (DomainException $e) {
            return $this->error($e->getMessage(), $e->httpStatus());
        } catch (Throwable $e) {
            Log::error('Vendor product update failed', ['exception' => $e, 'product_id' => $id]);

            return $this->error('Unable to update product.', 500);
        }
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $vendor = $this->vendorProfileForUser($request);
            $this->productManager->deleteOwnedBy($id, $vendor->id);

            return $this->success(null, 'Product deleted.');
        } catch (DomainException $e) {
            return $this->error($e->getMessage(), $e->httpStatus());
        } catch (Throwable $e) {
            Log::error('Vendor product delete failed', ['exception' => $e, 'product_id' => $id]);

            return $this->error('Unable to delete product.', 500);
        }
    }

    public function uploadImage(UploadProductImageRequest $request, int $id): JsonResponse
    {
        try {
            $vendor = $this->vendorProfileForUser($request);
            $image = $this->productManager->uploadImage(
                $id,
                $vendor->id,
                $request->file('image'),
                (bool) $request->boolean('is_primary'),
            );

            return $this->success([
                'id' => $image->id,
                'path' => $image->path,
                'thumb_path' => $image->thumb_path,
                'url' => asset('storage/'.$image->path),
                'is_primary' => $image->is_primary,
            ], 'Image uploaded.', 201);
        } catch (DomainException $e) {
            return $this->error($e->getMessage(), $e->httpStatus());
        } catch (Throwable $e) {
            Log::error('Vendor product image upload failed', ['exception' => $e, 'product_id' => $id]);

            return $this->error('Unable to upload image.', 500);
        }
    }

    public function deleteImage(Request $request, int $id, int $imageId): JsonResponse
    {
        try {
            $vendor = $this->vendorProfileForUser($request);
            $this->productManager->deleteImage($id, $vendor->id, $imageId);

            return $this->success(null, 'Image deleted.');
        } catch (DomainException $e) {
            return $this->error($e->getMessage(), $e->httpStatus());
        } catch (Throwable $e) {
            Log::error('Vendor product image delete failed', ['exception' => $e, 'product_id' => $id]);

            return $this->error('Unable to delete image.', 500);
        }
    }

    private function vendorProfileForUser(Request $request): VendorProfile
    {
        return VendorProfile::query()->where('user_id', $request->user()->id)->firstOrFail();
    }
}
