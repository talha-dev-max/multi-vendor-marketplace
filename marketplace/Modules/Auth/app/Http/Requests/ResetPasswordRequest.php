<?php

declare(strict_types=1);

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Modules\Auth\DTOs\ResetPasswordDto;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }

    public function toDto(): ResetPasswordDto
    {
        return new ResetPasswordDto(
            email: (string) $this->validated('email'),
            token: (string) $this->validated('token'),
            password: (string) $this->validated('password'),
        );
    }
}
