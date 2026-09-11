<?php

namespace App\Actions\Auth;

use App\Models\LoginOtp;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class VerifyLoginOtp
{
    public function handle(User $user, string $code): bool
    {
        $otp = LoginOtp::query()
            ->where('user_id', $user->id)
            ->whereNull('consumed_at')
            ->latest('id')
            ->first();

        if (! $otp || $otp->isExpired() || $otp->attempts >= config('otp.max_attempts')) {
            return false;
        }

        if (! Hash::check($code, $otp->code_hash)) {
            $otp->increment('attempts');

            return false;
        }

        $otp->update(['consumed_at' => now()]);

        return true;
    }
}
