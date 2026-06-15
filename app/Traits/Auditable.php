<?php

namespace App\Traits;

use App\Services\AuditService;
use Illuminate\Database\Eloquent\Model;

/**
 * Auditable Trait
 * 
 * Ajoute les capacités d'audit à un modèle
 */
trait Auditable
{
    /**
     * Boot the auditable trait
     */
    public static function bootAuditable(): void
    {
        // Log create
        static::created(function (Model $model) {
            AuditService::logCreate($model, $model->getAttributes());
        });

        // Log update
        static::updated(function (Model $model) {
            $changes = $model->getChanges();
            
            if (!empty($changes)) {
                // Récupérer les anciennes valeurs
                $oldValues = [];
                foreach ($changes as $key => $value) {
                    $oldValues[$key] = $model->getOriginal($key);
                }

                AuditService::logUpdate($model, $oldValues, $changes);
            }
        });

        // Log delete
        static::deleted(function (Model $model) {
            AuditService::logDelete($model, $model->getAttributes());
        });
    }

    /**
     * Get audit logs for this model
     */
    public function auditLogs()
    {
        return AuditService::getModelHistory($this);
    }
}
