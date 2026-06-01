<?php

declare(strict_types=1);

namespace Modules\Cart\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Cart\DTOs\UpdateCartItemDto;

class UpdateCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'quantity' => ['required', 'integer', 'min:1', 'max:999'],
        ];
    }

    public function toDto(int $cartItemId): UpdateCartItemDto
    {
        return new UpdateCartItemDto(
            userId: (int) $this->user()->id,
            cartItemId: $cartItemId,
            quantity: (int) $this->validated('quantity'),
        );
    }
}
