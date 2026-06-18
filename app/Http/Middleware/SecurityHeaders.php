<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ajoute les en-têtes HTTP de sécurité recommandés par OWASP.
 *
 * Couvre :
 *  - Clickjacking (X-Frame-Options)
 *  - MIME sniffing (X-Content-Type-Options)
 *  - Fuite de Referer (Referrer-Policy)
 *  - Capteurs navigateur (Permissions-Policy)
 *  - HSTS sur HTTPS (Strict-Transport-Security)
 *  - XSS / injection ressources (Content-Security-Policy)
 *  - Désactivation du cache pour les pages authentifiées sensibles
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        // Ne pas casser les réponses de téléchargement (PDF, exports) qui définissent déjà leurs en-têtes.
        $headers = $response->headers;

        $headers->set('X-Frame-Options', 'SAMEORIGIN');
        $headers->set('X-Content-Type-Options', 'nosniff');
        $headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $headers->set('X-XSS-Protection', '0'); // déprécié mais explicitement désactivé
        $headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=(self), payment=(), usb=(), magnetometer=(), accelerometer=(), gyroscope=()'
        );

        // HSTS uniquement quand on est en HTTPS (évite de casser le dev local en http).
        if ($request->isSecure()) {
            $headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // Content-Security-Policy — politique compatible avec Bootstrap, FontAwesome, Google reCAPTCHA, Google Fonts, OpenStreetMap.
        // 'unsafe-inline' reste nécessaire pour les styles/scripts inline existants du projet ;
        // à durcir progressivement avec des nonces lors d'une refonte des vues.
        $csp = implode('; ', [
            "default-src 'self'",
            "base-uri 'self'",
            "frame-ancestors 'self'",
            "form-action 'self'",
            "img-src 'self' data: blob: https:",
            "font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com https://www.google.com https://www.gstatic.com",
            "connect-src 'self' https://nominatim.openstreetmap.org https://*.tile.openstreetmap.org",
            "frame-src https://www.google.com",
            "object-src 'none'",
        ]);
        $headers->set('Content-Security-Policy', $csp);

        // Empêcher la mise en cache navigateur/proxy des pages authentifiées
        // (évite que la page suivante l'utilisateur précédent reste accessible via "Précédent").
        if ($request->user()) {
            $headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $headers->set('Pragma', 'no-cache');
        }

        // Toujours supprimer l'en-tête qui dévoile la techno côté serveur.
        $headers->remove('X-Powered-By');
        $headers->remove('Server');

        return $response;
    }
}
