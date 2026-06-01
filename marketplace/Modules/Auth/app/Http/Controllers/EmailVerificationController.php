<?php

declare(strict_types=1);

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Auth\Repositories\Contracts\UserRepositoryInterface;
use Throwable;

class EmailVerificationController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly UserRepositoryInterface $users,
    ) {
    }

    public function verify(Request $request, int $id, string $hash): JsonResponse
    {
        try {
            $user = $this->users->findById($id);

            if ($user === null) {
                return $this->error('Invalid verification link.', 404);
            }

            if (! hash_equals($hash, sha1($user->getEmailForVerification()))) {
                return $this->error('Invalid verification hash.', 403);
            }

            if ($user->hasVerifiedEmail()) {
                return $this->success(null, 'Email already verified.');
            }

            $user->markEmailAsVerified();
            event(new Verified($user));

            return $this->success(null, 'Email verified successfully.');
        } catch (Throwable $e) {
            Log::error('Email verification failed', ['exception' => $e, 'user_id' => $id]);

            return $this->error('Verification failed.', 500);
        }
    }

    public function resend(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if ($user->hasVerifiedEmail()) {
                return $this->success(null, 'Email already verified.');
            }

            $user->sendEmailVerificationNotification();

            return $this->success(null, 'Verification email sent.');
        } catch (Throwable $e) {
            Log::error('Resending verification email failed', ['exception' => $e]);

            return $this->error('Unable to send verification email.', 500);
        }
    }
}
