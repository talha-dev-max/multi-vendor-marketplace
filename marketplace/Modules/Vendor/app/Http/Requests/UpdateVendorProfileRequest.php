<?php

declare(strict_types=1);

namespace Modules\Vendor\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Vendor\DTOs\UpdateVendorProfileDto;

class UpdateVendorProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->hasRole('vendor');
    }

    public function rules(): array
    {
        return [
            'store_name' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function toDto(): UpdateVendorProfileDto
    {
        return new UpdateVendorProfileDto(
            storeName: (string) $this->validated('store_name'),
            description: $this->validated('description'),
        );
    }
}
