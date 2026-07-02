<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MfaVerified
{
    public function handle(Request $request, Closure $next)
    {
        // Rediriger explicitement les visiteurs non authentifiés
        if (!Auth::check()) {
            return redirect()->route('login.form');
        }

        $user = Auth::user();

        // MFA requis uniquement pour super_admin et commune_admin
        if (!$user->isSuperAdmin() && !$user->isCommuneAdmin()) {
            return $next($request);
        }

        // Comparaison type-safe : cast (int) pour éviter les faux négatifs
        // entre un entier stocké en session et l'ID utilisateur (int vs string).
        if ((int) $request->session()->get('mfa_verified_user_id') === (int) $user->id) {
            return $next($request);
        }

        return redirect()->route('mfa.show');
    }
}
