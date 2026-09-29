<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CommuneAdminMiddleware
{
    /**
     * Vérifie que l'utilisateur est un administrateur de commune actif.
     *
     * Distingue explicitement :
     * - Visiteur non authentifié → redirect login
     * - Utilisateur authentifié sans le bon rôle → 403
     * - Commune admin sans MFA validé → redirect mfa
     *
     * ⚠️ La règle MFA vient de $user->requiresMfa(), la MÊME source que le
     * middleware « mfa.verified ». Ne jamais réécrire la condition en dur ici :
     * la MFA peut être mise en pause globalement (MFA_ENABLED), et deux
     * conditions divergentes provoquaient une boucle de redirection entre
     * le tableau de bord communal et /mfa.
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

        // Vérification MFA (ignorée si la MFA est en pause).
        if ($user->requiresMfa()
            && (int) $request->session()->get('mfa_verified_user_id') !== (int) $user->id) {
            return redirect()->route('mfa.show');
        }

        return $next($request);
    }
}
