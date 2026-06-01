<?php

declare(strict_types=1);

namespace Modules\Catalog\Http\Controllers\Admin;

use App\Exceptions\Domain\DomainException;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Modules\Catalog\Http\Requests\StoreCategoryRequest;
use Modules\Catalog\Http\Resources\CategoryResource;
use Modules\Catalog\Managers\CategoryManager;
use Throwable;

class CategoryController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly CategoryManager $categoryManager,
    ) {
    }

    public function index(): JsonResponse
    {
        try {
            return $this->success(
                CategoryResource::collection($this->categoryManager->listAll()),
            );
        } catch (Throwable $e) {
            Log::error('Admin category list failed', ['exception' => $e]);

            return $this->error('Unable to fetch categories.', 500);
        }
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        try {
            $category = $this->categoryManager->create($request->toDto());

            return $this->success(CategoryResource::make($category), 'Category created.', 201);
        } catch (Throwable $e) {
            Log::error('Admin category create failed', ['exception' => $e]);

            return $this->error('Unable to create category.', 500);
        }
    }

    public function update(StoreCategoryRequest $request, int $id): JsonResponse
    {
        try {
            $category = $this->categoryManager->update($id, $request->toDto());

            return $this->success(CategoryResource::make($category), 'Category updated.');
        } catch (DomainException $e) {
            return $this->error($e->getMessage(), $e->httpStatus());
        } catch (Throwable $e) {
            Log::error('Admin category update failed', ['exception' => $e, 'id' => $id]);

            return $this->error('Unable to update category.', 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->categoryManager->delete($id);

            return $this->success(null, 'Category deleted.');
        } catch (DomainException $e) {
            return $this->error($e->getMessage(), $e->httpStatus());
        } catch (Throwable $e) {
            Log::error('Admin category delete failed', ['exception' => $e, 'id' => $id]);

            return $this->error('Unable to delete category.', 500);
        }
    }
}
