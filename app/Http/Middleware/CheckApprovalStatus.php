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
     * Vérifie deux conditions :
     * 1. L'utilisateur est approuvé (sinon → page d'attente).
     * 2. Le rôle de l'utilisateur n'a pas changé depuis sa dernière connexion
     *    (sinon → déconnexion forcée + redirection login avec message).
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ce middleware est appliqué sur des routes authentifiées.
        // Si l'utilisateur n'est pas connecté, on le redirige vers login.
        if (! auth()->check()) {
            return redirect()->route('login.form');
        }

        $user = auth()->user();

        // ── Détection de session obsolète (changement de rôle) ──────────────────
        // Si le rôle a été changé par un admin après la création de cette session,
        // on force la déconnexion pour que l'utilisateur accède à son nouvel espace.
        $sessionCreatedAt = $request->session()->get('login_timestamp');

        if ($sessionCreatedAt && $user->role_changed_at) {
            $roleChangedAt = $user->role_changed_at->timestamp;

            if ($roleChangedAt > $sessionCreatedAt) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login.form')
                    ->with('message',
                        'Votre rôle a été modifié par un administrateur. '
                        . 'Veuillez vous reconnecter pour accéder à votre nouvel espace.'
                    );
            }
        }

        // ── Les super admins ont toujours accès (compte seedé manuellement) ────
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // ── Public users n'ont pas besoin d'approbation ─────────────────────────
        if ($user->isPublicUser()) {
            return $next($request);
        }

        // ── Autres rôles ont besoin d'être approuvés ────────────────────────────
        if (! $user->isApproved()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('registration.pending')
                ->with('message', 'Votre compte est en attente de validation par un administrateur.');
        }

        return $next($request);
    }
}
