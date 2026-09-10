<?php

namespace App\Actions\Staff;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UpsertStaff
{
    /**
     * @param  array<string, mixed>  $data
     * @param  list<string>  $roles
     */
    public function handle(array $data, array $roles, ?User $user = null): User
    {
        $user ??= new User;

        if (! $user->exists) {
            $user->password = Hash::make(Str::password(16));
            $user->email_verified_at = now();
        }

        $user->fill($data)->save();
        $user->syncRoles($roles);

        return $user->refresh();
    }
}
