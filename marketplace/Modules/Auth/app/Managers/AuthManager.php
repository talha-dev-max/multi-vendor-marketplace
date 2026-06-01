<?php

declare(strict_types=1);

namespace Modules\Auth\Managers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Modules\Auth\DTOs\LoginDto;
use Modules\Auth\DTOs\RegisterDto;
use Modules\Auth\Services\AuthService;

class AuthManager
{
    public function __construct(
        private readonly AuthService $authService,
    ) {
    }

    public function register(RegisterDto $dto): User
    {
        return DB::transaction(function () use ($dto): User {
            $user = $this->authService->register($dto);

            event(new \Illuminate\Auth\Events\Registered($user));

            return $user;
        });
    }

    /**
     * @return array{user: User, token: string}
     */
    public function login(LoginDto $dto): array
    {
        return $this->authService->login($dto);
    }

    public function logout(User $user): void
    {
        $this->authService->logout($user);
    }
}
