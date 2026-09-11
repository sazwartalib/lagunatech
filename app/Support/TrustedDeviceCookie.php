<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

/**
 * Lets a staff member skip the login OTP on a browser they've already
 * verified. The cookie value is bound to the user's current password hash,
 * so it stops working automatically the moment the password changes.
 */
class TrustedDeviceCookie
{
    private const PREFIX = 'trusted_device_';

    public static function isTrusted(Request $request, User $user): bool
    {
        $value = $request->cookie(self::cookieName($user));

        return $value !== null && hash_equals(self::token($user), $value);
    }

    public static function remember(User $user): void
    {
        Cookie::queue(
            self::cookieName($user),
            self::token($user),
            60 * 24 * (int) config('otp.trust_device_days'),
            path: '/',
            secure: app()->isProduction(),
            httpOnly: true,
            sameSite: 'lax',
        );
    }

    private static function cookieName(User $user): string
    {
        return self::PREFIX.$user->getKey();
    }

    private static function token(User $user): string
    {
        return hash_hmac('sha256', (string) $user->getKey(), $user->password);
    }
}
