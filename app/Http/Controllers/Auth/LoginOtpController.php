<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\IssueLoginOtp;
use App\Actions\Auth\VerifyLoginOtp;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\TrustedDeviceCookie;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginOtpController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('auth.otp.user_id')) {
            return redirect()->route('login');
        }

        return view('auth.verify-otp', [
            'ttlMinutes' => config('otp.ttl_minutes'),
        ]);
    }

    public function verify(Request $request, VerifyLoginOtp $verifyLoginOtp): RedirectResponse
    {
        $userId = $request->session()->get('auth.otp.user_id');

        if (! $userId) {
            return redirect()->route('login');
        }

        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = User::findOrFail($userId);

        if (! $verifyLoginOtp->handle($user, (string) $request->input('code'))) {
            throw ValidationException::withMessages([
                'code' => 'That code is invalid or has expired.',
            ]);
        }

        $remember = (bool) $request->session()->pull('auth.otp.remember', false);
        $request->session()->forget('auth.otp.user_id');

        Auth::login($user, $remember);
        $request->session()->regenerate();

        if ($request->boolean('trust_device')) {
            TrustedDeviceCookie::remember($user);
        }

        return redirect()->intended(route('dashboard'));
    }

    public function resend(Request $request, IssueLoginOtp $issueLoginOtp): RedirectResponse
    {
        $userId = $request->session()->get('auth.otp.user_id');

        if (! $userId) {
            return redirect()->route('login');
        }

        $issueLoginOtp->handle(User::findOrFail($userId), $request->ip(), $request->userAgent());

        return back()->with('status', 'A new code has been sent to your email.');
    }
}
