<?php

use App\Mail\Auth\OtpCodeMail;
use App\Models\LoginOtp;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

function signInAndCaptureOtpCode(User $user): string
{
    Mail::fake();

    test()->post('/login', [
        'email' => $user->email,
        'password' => 'secret-password',
    ])->assertRedirect('/otp/verify');

    $code = null;
    Mail::assertSent(OtpCodeMail::class, function (OtpCodeMail $mail) use (&$code, $user): bool {
        $code = $mail->code;

        return $mail->user->is($user);
    });

    return $code;
}

test('the OTP screen renders once a login is pending', function () {
    $user = User::factory()->create(['password' => bcrypt('secret-password')]);
    signInAndCaptureOtpCode($user);

    $this->get('/otp/verify')->assertOk()->assertSee('Check your email');
});

test('the OTP screen is unreachable without a pending login', function () {
    $this->get('/otp/verify')->assertRedirect('/login');
});

test('a correct OTP code completes sign in', function () {
    $user = User::factory()->create(['password' => bcrypt('secret-password')]);
    $code = signInAndCaptureOtpCode($user);

    $this->post('/otp/verify', ['code' => $code])->assertRedirect('/dashboard');

    $this->assertAuthenticatedAs($user);
    $this->assertNotNull(LoginOtp::first()->consumed_at);
});

test('an incorrect OTP code is rejected and does not sign in', function () {
    $user = User::factory()->create(['password' => bcrypt('secret-password')]);
    signInAndCaptureOtpCode($user);

    $this->post('/otp/verify', ['code' => '000000'])->assertSessionHasErrors('code');

    $this->assertGuest();
});

test('an expired OTP code is rejected', function () {
    $user = User::factory()->create(['password' => bcrypt('secret-password')]);
    $code = signInAndCaptureOtpCode($user);

    LoginOtp::first()->update(['expires_at' => now()->subMinute()]);

    $this->post('/otp/verify', ['code' => $code])->assertSessionHasErrors('code');

    $this->assertGuest();
});

test('too many wrong attempts locks the OTP code out', function () {
    $user = User::factory()->create(['password' => bcrypt('secret-password')]);
    $code = signInAndCaptureOtpCode($user);

    for ($i = 0; $i < 5; $i++) {
        $this->post('/otp/verify', ['code' => '000000']);
    }

    // Even the correct code no longer works once max attempts is reached.
    $this->post('/otp/verify', ['code' => $code])->assertSessionHasErrors('code');

    $this->assertGuest();
});

test('resend issues a new code and invalidates the previous one', function () {
    $user = User::factory()->create(['password' => bcrypt('secret-password')]);
    $firstCode = signInAndCaptureOtpCode($user);

    Mail::fake();
    $this->post('/otp/resend')->assertRedirect();

    $secondCode = null;
    Mail::assertSent(OtpCodeMail::class, function (OtpCodeMail $mail) use (&$secondCode): bool {
        $secondCode = $mail->code;

        return true;
    });

    $this->post('/otp/verify', ['code' => $firstCode])->assertSessionHasErrors('code');
    $this->assertGuest();

    $this->post('/otp/verify', ['code' => $secondCode])->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($user);
});

test('trusting a device skips the OTP step on the next login', function () {
    $user = User::factory()->create(['password' => bcrypt('secret-password')]);
    $code = signInAndCaptureOtpCode($user);

    $this->post('/otp/verify', ['code' => $code, 'trust_device' => '1'])
        ->assertRedirect('/dashboard');

    $this->post('/logout');
    $this->assertGuest();

    // Mirrors App\Support\TrustedDeviceCookie's derivation — the cookie is bound to
    // the current password hash, so a real browser would carry this exact value.
    $cookieName = 'trusted_device_'.$user->id;
    $cookieValue = hash_hmac('sha256', (string) $user->id, $user->password);

    // No mail should go out this time — the trusted-device cookie skips the OTP step.
    Mail::fake();

    $this->withCookie($cookieName, $cookieValue)->post('/login', [
        'email' => $user->email,
        'password' => 'secret-password',
    ])->assertRedirect('/dashboard');

    $this->assertAuthenticatedAs($user);
    Mail::assertNothingSent();
});
