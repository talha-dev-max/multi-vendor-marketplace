<?php

declare(strict_types=1);

namespace Modules\Catalog\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\Catalog\DTOs\CategoryDto;
use Modules\Catalog\Models\Category;
use Modules\Catalog\Repositories\Contracts\CategoryRepositoryInterface;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function findById(int $id): ?Category
    {
        return Category::query()->find($id);
    }

    public function findBySlug(string $slug): ?Category
    {
        return Category::query()->where('slug', $slug)->first();
    }

    public function allActive(): Collection
    {
        return Category::query()->active()->orderBy('sort_order')->orderBy('name')->get();
    }

    public function all(): Collection
    {
        return Category::query()->orderBy('sort_order')->orderBy('name')->get();
    }

    public function createFromDto(CategoryDto $dto, string $slug): Category
    {
        return Category::query()->create([
            'name' => $dto->name,
            'slug' => $slug,
            'parent_id' => $dto->parentId,
            'sort_order' => $dto->sortOrder,
            'is_active' => $dto->isActive,
        ]);
    }

    public function updateFromDto(Category $category, CategoryDto $dto): Category
    {
        $category->fill([
            'name' => $dto->name,
            'parent_id' => $dto->parentId,
            'sort_order' => $dto->sortOrder,
            'is_active' => $dto->isActive,
        ])->save();

        return $category->fresh();
    }

    public function delete(Category $category): bool
    {
        return (bool) $category->delete();
    }
}
