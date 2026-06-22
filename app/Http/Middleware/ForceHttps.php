<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Force la redirection HTTP -> HTTPS en production.
 * Complète URL::forceScheme('https') en attrapant les requêtes entrantes
 * faites en clair (utile derrière un load-balancer mal configuré).
 */
class ForceHttps
{
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('production')
            && !$request->isSecure()
            && !$request->is('health*')) {
            return redirect()->secure($request->getRequestUri(), 301);
        }

        return $next($request);
    }
}
