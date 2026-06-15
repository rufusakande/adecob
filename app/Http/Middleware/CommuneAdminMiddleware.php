<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CommuneAdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        if (!$user || !$user->isCommuneAdmin()) {
            abort(403, 'Accès réservé aux administrateurs de commune.');
        }
        return $next($request);
    }
}