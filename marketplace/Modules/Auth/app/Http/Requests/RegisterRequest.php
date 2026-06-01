<?php

declare(strict_types=1);

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Modules\Auth\DTOs\RegisterDto;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }

    public function toDto(): RegisterDto
    {
        return new RegisterDto(
            name: (string) $this->validated('name'),
            email: (string) $this->validated('email'),
            password: (string) $this->validated('password'),
        );
    }
}
