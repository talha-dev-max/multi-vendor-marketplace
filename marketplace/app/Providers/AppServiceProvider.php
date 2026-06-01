<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        VerifyEmail::createUrlUsing(function (object $notifiable): string {
            return URL::temporarySignedRoute(
                'api.verification.verify',
                now()->addMinutes(60),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ],
            );
        });

        ResetPassword::createUrlUsing(function (object $notifiable, string $token): string {
            $frontendUrl = (string) config('app.frontend_url', config('app.url'));

            return $frontendUrl.'/auth/reset-password?token='.$token.'&email='.urlencode($notifiable->getEmailForPasswordReset());
        });
    }
}
