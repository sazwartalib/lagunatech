<?php

use App\Enums\Role as RoleEnum;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->beforeEach(function (): void {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->seed(RolePermissionSeeder::class);
    })
    ->in('Feature');

/**
 * Create a user carrying the given role (defaults to a fully-privileged admin).
 */
function userWithRole(RoleEnum $role = RoleEnum::Admin, array $attributes = []): User
{
    $user = User::factory()->create($attributes);
    $user->assignRole($role->value);

    return $user;
}

/**
 * Authenticate as a user with the given role and return it.
 */
function actingAsRole(RoleEnum $role = RoleEnum::Admin, array $attributes = []): User
{
    $user = userWithRole($role, $attributes);
    test()->actingAs($user);

    return $user;
}
