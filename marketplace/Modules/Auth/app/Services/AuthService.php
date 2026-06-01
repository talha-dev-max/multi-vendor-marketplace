<?php

declare(strict_types=1);

namespace Modules\Auth\Services;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;
use Modules\Auth\DTOs\LoginDto;
use Modules\Auth\DTOs\RegisterDto;
use Modules\Auth\Repositories\Contracts\UserRepositoryInterface;

class AuthService
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
    ) {
    }

    public function register(RegisterDto $dto): User
    {
        $user = $this->users->createFromRegisterDto($dto);
        $user->assignRole('customer');

        return $user->fresh();
    }

    /**
     * @return array{user: User, token: string}
     */
    public function login(LoginDto $dto): array
    {
        $user = $this->users->findByEmail($dto->email);

        if ($user === null || ! Hash::check($dto->password, $user->password)) {
            throw new AuthenticationException('Invalid credentials.');
        }

        $token = $user->createToken($dto->deviceName ?? 'api')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }
}
