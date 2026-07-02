<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CommuneAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Distingue explicitement les visiteurs non authentifiés (redirect login)
     * des utilisateurs authentifiés sans le bon rôle (403).
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login.form')
                ->with('message', 'Veuillez vous connecter pour accéder à cette page.');
        }

        $user = auth()->user();

        if (!$user->isCommuneAdmin()) {
            abort(403, 'Accès réservé aux administrateurs de commune.');
        }

        return $next($request);
    }
}
