<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomerUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('customer')->user();

        if ($user && ! $user->is_active) {
            Auth::guard('customer')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('portal.login')
                ->withErrors(['email' => 'This portal account has been disabled.']);
        }

        return $next($request);
    }
}
