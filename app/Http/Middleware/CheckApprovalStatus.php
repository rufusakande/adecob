<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
        if (auth()->check()) {
            $user = auth()->user();
            
            // Public users n'ont pas besoin d'approbation
            if ($user->isPublicUser()) {
                return $next($request);
            }

            // Autres rôles ont besoin d'être approuvés
            if (!$user->isApproved()) {
                return redirect()->route('registration.pending')
                    ->with('message', 'Votre compte est en attente de validation par un administrateur.');
            }
        }

        return $next($request);
    }
}
