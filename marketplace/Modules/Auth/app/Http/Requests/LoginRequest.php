<?php

declare(strict_types=1);

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Auth\DTOs\LoginDto;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function toDto(): LoginDto
    {
        return new LoginDto(
            email: (string) $this->validated('email'),
            password: (string) $this->validated('password'),
            deviceName: $this->validated('device_name'),
        );
    }
}
