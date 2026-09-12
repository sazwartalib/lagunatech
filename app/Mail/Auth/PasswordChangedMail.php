<?php

namespace App\Mail\Auth;

use App\Models\CustomerUser;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordChangedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User|CustomerUser $user,
        public readonly CarbonImmutable $changedAt,
    ) {}

    public function build(): self
    {
        return $this->view('emails.auth.password-changed')
            ->subject('Your password was changed');
    }
}
