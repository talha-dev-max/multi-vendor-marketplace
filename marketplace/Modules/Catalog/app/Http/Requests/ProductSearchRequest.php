<?php

declare(strict_types=1);

namespace Modules\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Catalog\DTOs\ProductSearchDto;

class ProductSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'vendor_id' => ['nullable', 'integer', 'exists:vendor_profiles,id'],
            'price_min' => ['nullable', 'numeric', 'min:0'],
            'price_max' => ['nullable', 'numeric', 'min:0'],
            'sort' => ['nullable', 'string', 'in:newest,price_asc,price_desc,name'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function toDto(): ProductSearchDto
    {
        return new ProductSearchDto(
            query: $this->validated('q'),
            categoryId: $this->validated('category_id') !== null ? (int) $this->validated('category_id') : null,
            vendorId: $this->validated('vendor_id') !== null ? (int) $this->validated('vendor_id') : null,
            priceMin: $this->validated('price_min') !== null ? (float) $this->validated('price_min') : null,
            priceMax: $this->validated('price_max') !== null ? (float) $this->validated('price_max') : null,
            sort: (string) ($this->validated('sort') ?? 'newest'),
            page: (int) ($this->validated('page') ?? 1),
            perPage: (int) ($this->validated('per_page') ?? 20),
        );
    }
}
