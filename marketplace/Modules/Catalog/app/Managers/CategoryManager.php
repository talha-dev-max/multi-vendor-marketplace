<?php

declare(strict_types=1);

namespace Modules\Catalog\Managers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Catalog\DTOs\CategoryDto;
use Modules\Catalog\Models\Category;
use Modules\Catalog\Services\CategoryService;

class CategoryManager
{
    public function __construct(
        private readonly CategoryService $categoryService,
    ) {
    }

    public function listActive(): Collection
    {
        return $this->categoryService->listActive();
    }

    public function listAll(): Collection
    {
        return $this->categoryService->listAll();
    }

    public function create(CategoryDto $dto): Category
    {
        return DB::transaction(fn () => $this->categoryService->create($dto));
    }

    public function update(int $id, CategoryDto $dto): Category
    {
        return DB::transaction(function () use ($id, $dto): Category {
            $category = $this->categoryService->getByIdOrFail($id);

            return $this->categoryService->update($category, $dto);
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id): bool {
            $category = $this->categoryService->getByIdOrFail($id);

            return $this->categoryService->delete($category);
        });
    }
}
