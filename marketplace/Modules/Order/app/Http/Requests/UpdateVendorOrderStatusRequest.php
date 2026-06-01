<?php

declare(strict_types=1);

namespace Modules\Order\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Order\DTOs\UpdateVendorOrderStatusDto;
use Modules\Order\Models\VendorOrder;
use Modules\Vendor\Models\VendorProfile;

class UpdateVendorOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->hasRole('vendor');
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in([
                VendorOrder::STATUS_CONFIRMED,
                VendorOrder::STATUS_SHIPPED,
                VendorOrder::STATUS_DELIVERED,
                VendorOrder::STATUS_CANCELED,
            ])],
        ];
    }

    public function toDto(int $vendorOrderId): UpdateVendorOrderStatusDto
    {
        $profile = VendorProfile::query()->where('user_id', $this->user()->id)->firstOrFail();

        return new UpdateVendorOrderStatusDto(
            vendorOrderId: $vendorOrderId,
            vendorId: (int) $profile->id,
            newStatus: (string) $this->validated('status'),
        );
    }
}
