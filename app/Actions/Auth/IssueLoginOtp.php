<?php

namespace App\Actions\Auth;

use App\Mail\Auth\OtpCodeMail;
use App\Models\LoginOtp;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class IssueLoginOtp
{
    public function handle(User $user, ?string $ip = null, ?string $userAgent = null): LoginOtp
    {
        LoginOtp::query()
            ->where('user_id', $user->id)
            ->whereNull('consumed_at')
            ->delete();

        $code = (string) random_int(100000, 999999);

        $otp = LoginOtp::create([
            'user_id' => $user->id,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes(config('otp.ttl_minutes')),
            'ip_address' => $ip,
            'user_agent' => $userAgent,
        ]);

        // Sent synchronously — the user is waiting on-screen for this code.
        Mail::to($user)->send(new OtpCodeMail($user, $code, config('otp.ttl_minutes')));

        return $otp;
    }
}
