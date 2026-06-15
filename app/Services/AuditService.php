<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    /**
     * Log an action
     */
    public static function log(
        string $action,
        ?Model $model = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        string $status = 'success',
        ?string $description = null,
        ?string $errorMessage = null
    ): AuditLog {
        $user = Auth::user();

        return AuditLog::create([
            'user_id' => $user?->id,
            'user_name' => $user?->name ?? 'System',
            'action' => $action,
            'auditable_type' => $model ? get_class($model) : null,
            'auditable_id' => $model?->id,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::header('User-Agent'),
            'method' => Request::method(),
            'url' => Request::url(),
            'status' => $status,
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * Log login
     */
    public static function logLogin(User $user): AuditLog
    {
        return self::log(
            'login',
            description: "Connexion de l'utilisateur {$user->name}"
        );
    }

    /**
     * Log logout
     */
    public static function logLogout(User $user): AuditLog
    {
        return self::log(
            'logout',
            description: "Déconnexion de l'utilisateur {$user->name}"
        );
    }

    /**
     * Log create
     */
    public static function logCreate(Model $model, array $attributes): AuditLog
    {
        return self::log(
            'create',
            $model,
            newValues: $attributes,
            description: "Création de " . class_basename($model) . " #{$model->id}"
        );
    }

    /**
     * Log update
     */
    public static function logUpdate(Model $model, array $oldValues, array $newValues): AuditLog
    {
        return self::log(
            'update',
            $model,
            $oldValues,
            $newValues,
            description: "Mise à jour de " . class_basename($model) . " #{$model->id}"
        );
    }

    /**
     * Log delete
     */
    public static function logDelete(Model $model, array $attributes): AuditLog
    {
        return self::log(
            'delete',
            $model,
            $attributes,
            newValues: null,
            description: "Suppression de " . class_basename($model) . " #{$model->id}"
        );
    }

    /**
     * Log export
     */
    public static function logExport(string $type, int $count, ?array $filters = null): AuditLog
    {
        return self::log(
            'export',
            description: "Export de {$count} " . $type,
            newValues: $filters
        );
    }

    /**
     * Log import
     */
    public static function logImport(string $type, int $count, ?array $details = null): AuditLog
    {
        return self::log(
            'import',
            description: "Import de {$count} " . $type,
            newValues: $details
        );
    }

    /**
     * Log password reset
     */
    public static function logPasswordReset(User $user): AuditLog
    {
        return self::log(
            'password_reset',
            description: "Réinitialisation du mot de passe pour {$user->name}"
        );
    }

    /**
     * Log profile update
     */
    public static function logProfileUpdate(User $user, array $oldValues, array $newValues): AuditLog
    {
        return self::log(
            'profile_update',
            $user,
            $oldValues,
            $newValues,
            description: "Mise à jour du profil de {$user->name}"
        );
    }

    /**
     * Log error
     */
    public static function logError(string $action, ?Model $model = null, ?string $message = null): AuditLog
    {
        return self::log(
            $action,
            $model,
            status: 'error',
            errorMessage: $message
        );
    }

    /**
     * Get audit logs with filters
     */
    public static function getAuditLogs(
        ?string $action = null,
        ?int $userId = null,
        ?string $auditableType = null,
        ?int $limit = 100
    ) {
        $query = AuditLog::with('user')
            ->latest('created_at');

        if ($action) {
            $query->where('action', $action);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($auditableType) {
            $query->where('auditable_type', $auditableType);
        }

        return $query->limit($limit)->get();
    }

    /**
     * Get recent logs
     */
    public static function getRecentLogs(int $days = 7, int $limit = 50)
    {
        return AuditLog::with('user')
            ->where('created_at', '>=', now()->subDays($days))
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get user activity
     */
    public static function getUserActivity(User $user, int $limit = 50)
    {
        return AuditLog::where('user_id', $user->id)
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get model history
     */
    public static function getModelHistory(Model $model, int $limit = 50)
    {
        return AuditLog::where('auditable_type', get_class($model))
            ->where('auditable_id', $model->id)
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }
}
