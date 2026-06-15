<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        if (!auth()->check() || (!$user->isSuperAdmin() && !$user->isCommuneAdmin())) {
            return redirect('/')->with('error', 'Accès non autorisé. Cette section est réservée aux administrateurs.');
        }

        return $next($request);
    }
}
