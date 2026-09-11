<?php

namespace App\Mail\Auth;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Sent synchronously (no ShouldQueue) — the user is waiting on-screen for this code.
     */
    public function __construct(
        public readonly User $user,
        public readonly string $code,
        public readonly int $expiresMinutes,
    ) {}

    public function build(): self
    {
        return $this->view('emails.auth.otp-code')
            ->subject('Your sign-in code');
    }
}
