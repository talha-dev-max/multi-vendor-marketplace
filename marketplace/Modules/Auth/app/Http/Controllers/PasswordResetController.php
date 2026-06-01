<?php

declare(strict_types=1);

namespace Modules\Auth\Http\Controllers;

use App\Exceptions\Domain\DomainException;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Modules\Auth\Http\Requests\ForgotPasswordRequest;
use Modules\Auth\Http\Requests\ResetPasswordRequest;
use Modules\Auth\Managers\PasswordResetManager;
use Throwable;

class PasswordResetController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly PasswordResetManager $passwordResetManager,
    ) {
    }

    public function sendLink(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            $message = $this->passwordResetManager->sendResetLink($request->toDto());

            return $this->success(null, $message);
        } catch (DomainException $e) {
            return $this->error($e->getMessage(), $e->httpStatus());
        } catch (Throwable $e) {
            Log::error('Forgot password failed', ['exception' => $e]);

            return $this->error('Unable to send reset link.', 500);
        }
    }

    public function reset(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $message = $this->passwordResetManager->reset($request->toDto());

            return $this->success(null, $message);
        } catch (DomainException $e) {
            return $this->error($e->getMessage(), $e->httpStatus());
        } catch (Throwable $e) {
            Log::error('Password reset failed', ['exception' => $e]);

            return $this->error('Unable to reset password.', 500);
        }
    }
}
