<?php

declare(strict_types=1);

namespace Modules\Catalog\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Modules\Catalog\DTOs\CategoryDto;
use Modules\Catalog\Models\Category;

interface CategoryRepositoryInterface
{
    public function findById(int $id): ?Category;

    public function findBySlug(string $slug): ?Category;

    public function allActive(): Collection;

    public function all(): Collection;

    public function createFromDto(CategoryDto $dto, string $slug): Category;

    public function updateFromDto(Category $category, CategoryDto $dto): Category;

    public function delete(Category $category): bool;
}
