<?php

namespace App\Listeners;

use App\Services\AuditService;
use Illuminate\Auth\Events\Logout;

class LogAuthenticatedLogout
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        // L'utilisateur peut être null (compte supprimé ou session expirée) :
        // on n'audite la déconnexion que s'il existe encore.
        if ($event->user) {
            AuditService::logLogout($event->user);
        }
    }
}
