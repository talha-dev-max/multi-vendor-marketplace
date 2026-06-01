<?php

declare(strict_types=1);

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Auth\Http\Requests\LoginRequest;
use Modules\Auth\Http\Requests\RegisterRequest;
use Modules\Auth\Http\Resources\UserResource;
use Modules\Auth\Managers\AuthManager;
use Throwable;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly AuthManager $authManager,
    ) {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $user = $this->authManager->register($request->toDto());

            return $this->success(
                data: UserResource::make($user),
                message: 'Registration successful. Please verify your email.',
                status: 201,
            );
        } catch (Throwable $e) {
            Log::error('Registration failed', ['exception' => $e]);

            return $this->error('Registration failed.', 500);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authManager->login($request->toDto());

            return $this->success([
                'user' => UserResource::make($result['user']),
                'token' => $result['token'],
            ], 'Login successful.');
        } catch (AuthenticationException $e) {
            return $this->error($e->getMessage(), 401);
        } catch (Throwable $e) {
            Log::error('Login failed', ['exception' => $e]);

            return $this->error('Login failed.', 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $this->authManager->logout($request->user());

            return $this->success(null, 'Logged out.');
        } catch (Throwable $e) {
            Log::error('Logout failed', ['exception' => $e]);

            return $this->error('Logout failed.', 500);
        }
    }

    public function me(Request $request): JsonResponse
    {
        try {
            return $this->success(UserResource::make($request->user()));
        } catch (Throwable $e) {
            Log::error('Fetching current user failed', ['exception' => $e]);

            return $this->error('Unable to fetch user.', 500);
        }
    }
}
