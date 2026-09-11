<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\IssueLoginOtp;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Support\TrustedDeviceCookie;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request, IssueLoginOtp $issueLoginOtp): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        if (! config('otp.enabled') || TrustedDeviceCookie::isTrusted($request, $user)) {
            return redirect()->intended(route('dashboard'));
        }

        // OTP still required — undo the login straight away so no authenticated
        // session exists until the code is verified. Only a pending marker remains.
        $remember = $request->boolean('remember');
        Auth::logout();

        $request->session()->put('auth.otp.user_id', $user->id);
        $request->session()->put('auth.otp.remember', $remember);

        $issueLoginOtp->handle($user, $request->ip(), $request->userAgent());

        return redirect()->route('login.otp.show');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
