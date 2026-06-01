<?php

declare(strict_types=1);

namespace Modules\Order\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Order\DTOs\PlaceOrderDto;
use Modules\Order\DTOs\ShippingAddressDto;
use Modules\Order\Models\Order;

class PlaceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'payment_method' => ['required', 'string', Rule::in([Order::PAYMENT_COD, Order::PAYMENT_STRIPE])],
            'stripe_success_url' => ['nullable', 'url', 'required_if:payment_method,stripe'],
            'stripe_cancel_url' => ['nullable', 'url', 'required_if:payment_method,stripe'],

            'shipping_address.full_name' => ['required', 'string', 'max:255'],
            'shipping_address.phone' => ['required', 'string', 'max:40'],
            'shipping_address.address_line1' => ['required', 'string', 'max:255'],
            'shipping_address.address_line2' => ['nullable', 'string', 'max:255'],
            'shipping_address.city' => ['required', 'string', 'max:100'],
            'shipping_address.state' => ['required', 'string', 'max:100'],
            'shipping_address.postal_code' => ['required', 'string', 'max:20'],
            'shipping_address.country' => ['required', 'string', 'size:2'],
        ];
    }

    public function toDto(): PlaceOrderDto
    {
        $addr = (array) $this->validated('shipping_address');

        return new PlaceOrderDto(
            userId: (int) $this->user()->id,
            paymentMethod: (string) $this->validated('payment_method'),
            shippingAddress: new ShippingAddressDto(
                fullName: (string) ($addr['full_name'] ?? ''),
                phone: (string) ($addr['phone'] ?? ''),
                addressLine1: (string) ($addr['address_line1'] ?? ''),
                addressLine2: $addr['address_line2'] ?? null,
                city: (string) ($addr['city'] ?? ''),
                state: (string) ($addr['state'] ?? ''),
                postalCode: (string) ($addr['postal_code'] ?? ''),
                country: (string) ($addr['country'] ?? ''),
            ),
            stripeSuccessUrl: $this->validated('stripe_success_url'),
            stripeCancelUrl: $this->validated('stripe_cancel_url'),
        );
    }
}
