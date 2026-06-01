<?php

declare(strict_types=1);

namespace Modules\Vendor\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Vendor\DTOs\RejectVendorDto;

class RejectVendorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ];
    }

    public function toDto(): RejectVendorDto
    {
        return new RejectVendorDto(
            reason: (string) $this->validated('reason'),
        );
    }
}
