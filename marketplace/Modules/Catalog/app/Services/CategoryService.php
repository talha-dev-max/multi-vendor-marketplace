<?php

declare(strict_types=1);

namespace Modules\Catalog\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Modules\Catalog\DTOs\CategoryDto;
use Modules\Catalog\Exceptions\CategoryNotFoundException;
use Modules\Catalog\Models\Category;
use Modules\Catalog\Repositories\Contracts\CategoryRepositoryInterface;

class CategoryService
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categories,
    ) {
    }

    public function listActive(): Collection
    {
        return $this->categories->allActive();
    }

    public function listAll(): Collection
    {
        return $this->categories->all();
    }

    public function getByIdOrFail(int $id): Category
    {
        $category = $this->categories->findById($id);

        if ($category === null) {
            throw new CategoryNotFoundException();
        }

        return $category;
    }

    public function create(CategoryDto $dto): Category
    {
        return $this->categories->createFromDto($dto, $this->uniqueSlug($dto->name));
    }

    public function update(Category $category, CategoryDto $dto): Category
    {
        return $this->categories->updateFromDto($category, $dto);
    }

    public function delete(Category $category): bool
    {
        return $this->categories->delete($category);
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while ($this->categories->findBySlug($slug) !== null) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
