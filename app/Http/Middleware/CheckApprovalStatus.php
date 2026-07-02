<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckApprovalStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ce middleware est appliqué sur des routes authentifiées.
        // Si l'utilisateur n'est pas connecté (ex. : bug de configuration),
        // on le redirige vers login plutôt que de passer silencieusement.
        if (!auth()->check()) {
            return redirect()->route('login.form');
        }

        $user = auth()->user();

        // Les super admins ont toujours accès (compte seedé manuellement).
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Public users n'ont pas besoin d'approbation
        if ($user->isPublicUser()) {
            return $next($request);
        }

        // Autres rôles ont besoin d'être approuvés
        if (!$user->isApproved()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('registration.pending')
                ->with('message', 'Votre compte est en attente de validation par un administrateur.');
        }

        return $next($request);
    }
}
