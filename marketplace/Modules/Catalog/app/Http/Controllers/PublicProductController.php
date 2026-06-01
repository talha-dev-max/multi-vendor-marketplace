<?php

declare(strict_types=1);

namespace Modules\Catalog\Http\Controllers;

use App\Exceptions\Domain\DomainException;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Modules\Catalog\Http\Requests\ProductSearchRequest;
use Modules\Catalog\Http\Resources\ProductResource;
use Modules\Catalog\Managers\ProductManager;
use Throwable;

class PublicProductController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly ProductManager $productManager,
    ) {
    }

    public function index(ProductSearchRequest $request): JsonResponse
    {
        try {
            $paginator = $this->productManager->search($request->toDto());

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
            Log::error('Public product search failed', ['exception' => $e]);

            return $this->error('Unable to fetch products.', 500);
        }
    }

    public function show(string $slug): JsonResponse
    {
        try {
            $product = $this->productManager->findBySlug($slug);

            return $this->success(ProductResource::make($product));
        } catch (DomainException $e) {
            return $this->error($e->getMessage(), $e->httpStatus());
        } catch (Throwable $e) {
            Log::error('Public product show failed', ['exception' => $e]);

            return $this->error('Unable to fetch product.', 500);
        }
    }
}
