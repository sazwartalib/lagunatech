<?php

namespace App\Actions\Staff;

use App\Mail\Auth\StaffInvitationMail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class UpsertStaff
{
    /**
     * @param  array<string, mixed>  $data
     * @param  list<string>  $roles
     */
    public function handle(array $data, array $roles, ?User $user = null): User
    {
        $isNew = ! $user?->exists;
        $user ??= new User;

        if (! $user->exists) {
            $user->password = Hash::make(Str::password(16));
            $user->email_verified_at = now();
        }

        $user->fill($data)->save();
        $user->syncRoles($roles);
        $user->refresh();

        if ($isNew) {
            $this->sendInvitation($user);
        }

        return $user;
    }

    protected function sendInvitation(User $user): void
    {
        $token = Password::broker()->createToken($user);
        $url = url(route('password.reset', ['token' => $token], false).'?email='.rawurlencode($user->email));

        Mail::to($user)->queue(new StaffInvitationMail($user, $url, config('auth.passwords.users.expire')));
    }
}
