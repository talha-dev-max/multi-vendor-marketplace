<?php

declare(strict_types=1);

namespace Modules\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Catalog\DTOs\CategoryDto;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function toDto(): CategoryDto
    {
        return new CategoryDto(
            name: (string) $this->validated('name'),
            parentId: $this->validated('parent_id') !== null ? (int) $this->validated('parent_id') : null,
            sortOrder: (int) ($this->validated('sort_order') ?? 0),
            isActive: (bool) $this->validated('is_active'),
        );
    }
}
