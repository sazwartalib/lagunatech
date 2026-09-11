<?php

namespace App\Mail\Auth;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StaffInvitationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly string $url,
        public readonly int $expiresMinutes,
    ) {}

    public function build(): self
    {
        return $this->view('emails.auth.invitation')
            ->subject('You’ve been added to '.config('app.name'));
    }
}
