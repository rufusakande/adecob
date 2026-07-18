<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CommuneAdminMiddleware
{
    /**
     * Vérifie que l'utilisateur est un administrateur de commune actif,
     * qu'il est correctement authentifié et que le MFA est validé.
     *
     * Distingue explicitement :
     * - Visiteur non authentifié → redirect login
     * - Utilisateur authentifié sans le bon rôle → 403
     * - Commune admin sans MFA validé → redirect mfa
     */
    public function handle(Request $request, Closure $next)
    {
        if (! auth()->check()) {
            return redirect()->route('login.form')
                ->with('message', 'Veuillez vous connecter pour accéder à cette page.');
        }

        $user = auth()->user();

        if (! $user->isCommuneAdmin()) {
            abort(403, 'Accès réservé aux administrateurs de commune.');
        }

        // Vérification MFA obligatoire pour les commune_admin.
        if ((int) $request->session()->get('mfa_verified_user_id') !== (int) $user->id) {
            return redirect()->route('mfa.show');
        }

        return $next($request);
    }
}
