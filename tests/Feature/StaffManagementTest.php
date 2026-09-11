<?php

use App\Enums\Role;
use App\Livewire\Staff\StaffForm;
use App\Livewire\Staff\StaffIndex;
use App\Mail\Auth\StaffInvitationMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

test('an admin can add a staff member and assign roles', function () {
    Mail::fake();

    actingAsRole(Role::Admin);

    Livewire::test(StaffForm::class)
        ->set('name', 'Aisyah Rahman')
        ->set('email', 'aisyah@lagunatech.com')
        ->set('position', 'QA Engineer')
        ->set('roles', [Role::Developer->value])
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('staff.index'));

    $user = User::firstWhere('email', 'aisyah@lagunatech.com');

    expect($user)->not->toBeNull()
        ->and($user->hasRole(Role::Developer->value))->toBeTrue()
        ->and($user->password)->not->toBeEmpty();

    Mail::assertQueued(StaffInvitationMail::class, fn (StaffInvitationMail $mail): bool => $mail->user->is($user));
});

test('adding a staff member requires at least one role', function () {
    actingAsRole(Role::Admin);

    Livewire::test(StaffForm::class)
        ->set('name', 'No Role')
        ->set('email', 'norole@lagunatech.com')
        ->set('roles', [])
        ->call('save')
        ->assertHasErrors('roles');
});

test('an admin can deactivate another account', function () {
    actingAsRole(Role::Admin);
    $victim = userWithRole(Role::Developer, ['is_active' => true]);

    Livewire::test(StaffIndex::class)->call('toggleActive', $victim->id);

    expect($victim->fresh()->is_active)->toBeFalse();
});

test('an admin cannot deactivate themselves', function () {
    $admin = actingAsRole(Role::Admin);

    Livewire::test(StaffIndex::class)
        ->call('toggleActive', $admin->id)
        ->assertForbidden();

    expect($admin->fresh()->is_active)->toBeTrue();
});

test('the last active super admin cannot be deactivated', function () {
    $superAdmin = userWithRole(Role::SuperAdmin);
    $this->actingAs(userWithRole(Role::Admin));

    Livewire::test(StaffIndex::class)
        ->call('toggleActive', $superAdmin->id)
        ->assertForbidden();
});

test('a developer cannot open staff management', function () {
    $this->actingAs(userWithRole(Role::Developer));

    $this->get(route('staff.index'))->assertForbidden();
});
