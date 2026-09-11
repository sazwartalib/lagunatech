<?php

use App\Models\User;

test('the login screen renders', function () {
    $this->get('/login')->assertOk()->assertSee('Welcome back');
});

test('correct credentials send the staff member to the OTP step', function () {
    $user = User::factory()->create(['password' => bcrypt('secret-password')]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'secret-password',
    ])->assertRedirect('/otp/verify');

    // Not authenticated yet — the OTP step still has to be completed.
    $this->assertGuest();
});

test('a staff member can sign in when OTP is disabled', function () {
    config(['otp.enabled' => false]);

    $user = User::factory()->create(['password' => bcrypt('secret-password')]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'secret-password',
    ])->assertRedirect('/dashboard');

    $this->assertAuthenticatedAs($user);
});

test('invalid credentials are rejected', function () {
    $user = User::factory()->create(['password' => bcrypt('secret-password')]);

    $this->from('/login')->post('/login', [
        'email' => $user->email,
        'password' => 'wrong',
    ])->assertRedirect('/login')->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('a deactivated account cannot sign in', function () {
    $user = User::factory()->inactive()->create(['password' => bcrypt('secret-password')]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'secret-password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('an authenticated user can sign out', function () {
    $this->actingAs(User::factory()->create());

    $this->post('/logout')->assertRedirect('/login');

    $this->assertGuest();
});

test('guests are redirected away from the dashboard', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});
