<?php

use App\Mail\Auth\PasswordChangedMail;
use App\Mail\Auth\ResetPasswordMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

test('requesting a password reset sends the branded email', function () {
    Mail::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email])
        ->assertSessionHas('status');

    Mail::assertQueued(ResetPasswordMail::class, fn (ResetPasswordMail $mail): bool => $mail->user->is($user));
});

test('resetting the password signs the user in and sends a confirmation email', function () {
    Mail::fake();

    $user = User::factory()->create(['password' => bcrypt('old-password')]);
    $token = Password::createToken($user);

    $this->post('/reset-password', [
        'token' => $token,
        'email' => $user->email,
        'password' => 'brand-new-password',
        'password_confirmation' => 'brand-new-password',
    ])->assertRedirect('/login');

    expect($user->refresh()->password)->not->toBe('old-password');

    Mail::assertQueued(PasswordChangedMail::class, fn (PasswordChangedMail $mail): bool => $mail->user->is($user));
});
