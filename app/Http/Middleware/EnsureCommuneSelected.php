<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCommuneSelected
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // Super admin n'a pas besoin de sélectionner une commune
            if ($user->isSuperAdmin()) {
                return $next($request);
            }

            // Pour les autres rôles (agent, commune_admin), une commune doit être sélectionnée
            if (!session()->has('commune_id') && !$user->isSuperAdmin() && !$user->isPublicUser()) {
                return redirect()->route('home')
                    ->with('message', 'Veuillez sélectionner une commune.');
            }
        }

        return $next($request);
    }
}
