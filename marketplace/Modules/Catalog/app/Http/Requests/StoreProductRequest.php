<?php

declare(strict_types=1);

namespace Modules\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Catalog\DTOs\ProductDto;
use Modules\Catalog\Models\Product;
use Modules\Vendor\Models\VendorProfile;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->hasRole('vendor');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'string', Rule::in([
                Product::STATUS_DRAFT,
                Product::STATUS_ACTIVE,
                Product::STATUS_INACTIVE,
            ])],
        ];
    }

    public function toDto(): ProductDto
    {
        $profile = VendorProfile::query()->where('user_id', $this->user()->id)->firstOrFail();

        return new ProductDto(
            vendorId: (int) $profile->id,
            categoryId: $this->validated('category_id') !== null ? (int) $this->validated('category_id') : null,
            name: (string) $this->validated('name'),
            description: $this->validated('description'),
            price: (float) $this->validated('price'),
            stock: (int) $this->validated('stock'),
            status: (string) $this->validated('status'),
        );
    }
}
