<?php

use App\Mail\Auth\PasswordChangedMail;
use App\Mail\Auth\ResetPasswordMail;
use App\Models\CustomerUser;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

test('requesting a portal password reset sends the branded email', function () {
    Mail::fake();

    $customerUser = CustomerUser::factory()->create();

    $this->post('/portal/forgot-password', ['email' => $customerUser->email])
        ->assertSessionHas('status');

    Mail::assertQueued(ResetPasswordMail::class, fn (ResetPasswordMail $mail): bool => $mail->user->is($customerUser));
});

test('resetting the portal password redirects to the portal login and sends a confirmation email', function () {
    Mail::fake();

    $customerUser = CustomerUser::factory()->create(['password' => bcrypt('old-password')]);
    $token = Password::broker('customer_users')->createToken($customerUser);

    $this->post('/portal/reset-password', [
        'token' => $token,
        'email' => $customerUser->email,
        'password' => 'brand-new-password',
        'password_confirmation' => 'brand-new-password',
    ])->assertRedirect(route('portal.login'));

    expect($customerUser->refresh()->password)->not->toBe('old-password');

    Mail::assertQueued(PasswordChangedMail::class, fn (PasswordChangedMail $mail): bool => $mail->user->is($customerUser));
});
