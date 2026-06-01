<?php

declare(strict_types=1);

namespace Modules\Cart\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Cart\DTOs\AddToCartDto;

class AddToCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:999'],
        ];
    }

    public function toDto(): AddToCartDto
    {
        return new AddToCartDto(
            userId: (int) $this->user()->id,
            productId: (int) $this->validated('product_id'),
            quantity: (int) $this->validated('quantity'),
        );
    }
}
