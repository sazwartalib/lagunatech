<?php

namespace App\Listeners\Auth;

use App\Mail\Auth\PasswordChangedMail;
use App\Models\CustomerUser;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Mail;

class SendPasswordChangedEmail
{
    public function handle(PasswordReset $event): void
    {
        /** @var User|CustomerUser $user */
        $user = $event->user;

        Mail::to($user)->queue(new PasswordChangedMail($user, now()->toImmutable()));
    }
}
