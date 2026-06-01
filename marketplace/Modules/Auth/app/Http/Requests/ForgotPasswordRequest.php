<?php

declare(strict_types=1);

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Auth\DTOs\ForgotPasswordDto;

class ForgotPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
        ];
    }

    public function toDto(): ForgotPasswordDto
    {
        return new ForgotPasswordDto(
            email: (string) $this->validated('email'),
        );
    }
}
