<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Services\AuditService;
use Illuminate\Console\Command;

class ClearOldAuditLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audit:clear-old 
                            {--days=365 : Nombre de jours à conserver}
                            {--force : Ignorer la confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Supprime les logs d\'audit antérieurs au nombre de jours spécifié (par défaut: 365 jours)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $force = $this->option('force');

        $this->info("Suppression des logs d'audit antérieurs à {$days} jours...");

        $count = AuditLog::where('created_at', '<', now()->subDays($days))->count();

        if ($count === 0) {
            $this->info('✓ Aucun log à supprimer.');
            return 0;
        }

        if (!$force && !$this->confirm("Êtes-vous sûr de vouloir supprimer {$count} logs?")) {
            $this->info('Annulé.');
            return 0;
        }

        $deleted = AuditLog::where('created_at', '<', now()->subDays($days))->delete();

        // Log this cleanup action
        AuditService::log(
            'delete',
            description: "Nettoyage automatique: suppression de {$deleted} logs d'audit antérieurs à {$days} jours"
        );

        $this->info("✓ {$deleted} logs d'audit ont été supprimés.");

        return 0;
    }
}
