<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Accès aux journaux d'audit : réservé aux Super Administrateurs.
        // (Les routes /admin/audit sont déjà protégées par le middleware super.admin ;
        //  cette Gate assure la défense en profondeur au niveau contrôleur.)
        Gate::define('viewAuditLogs', function ($user) {
            return $user->isSuperAdmin();
        });
    }
}
