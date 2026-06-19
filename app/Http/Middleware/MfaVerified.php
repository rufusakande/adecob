<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MfaVerified
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login.form');
        }

        // MFA requis uniquement pour super_admin et commune_admin
        if (!$user->isSuperAdmin() && !$user->isCommuneAdmin()) {
            return $next($request);
        }

        if ($request->session()->get('mfa_verified_user_id') === $user->id) {
            return $next($request);
        }

        return redirect()->route('mfa.show');
    }
}
