<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Forcer HTTPS en production (génération d'URL + redirections internes).
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        $this->configureRateLimiters();
    }

    /**
     * Définit les limiteurs nommés utilisés par les routes d'authentification
     * pour bloquer les attaques par force brute et l'énumération de comptes.
     */
    protected function configureRateLimiters(): void
    {
        // 5 tentatives de connexion par minute par couple (email + IP).
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->input('email');
            return [
                Limit::perMinute(5)->by(mb_strtolower($email) . '|' . $request->ip()),
                Limit::perMinute(20)->by($request->ip()),
            ];
        });

        // Inscription : 3/min/IP — empêche les bots de spammer la table users.
        RateLimiter::for('register', fn (Request $request) =>
            Limit::perMinute(3)->by($request->ip())
        );

        // Mot de passe oublié : 3/min/IP + 5/heure/email pour éviter le bruit dans la file de mails.
        RateLimiter::for('password-reset', function (Request $request) {
            $email = mb_strtolower((string) $request->input('email'));
            return [
                Limit::perMinute(3)->by($request->ip()),
                Limit::perHour(5)->by($email ?: $request->ip()),
            ];
        });

        // Formulaire de contact public : 5/min/IP.
        RateLimiter::for('contact', fn (Request $request) =>
            Limit::perMinute(5)->by($request->ip())
        );
    }
}
