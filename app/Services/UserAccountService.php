<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\AccountDeleted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class UserAccountService
{
    /**
     * Supprime définitivement un compte utilisateur.
     *
     * - Invalide les sessions actives de l'utilisateur
     * - Supprime le compte (les dépendances sont gérées par les contraintes FK : set null / cascade)
     * - Journalise dans l'audit QUI a supprimé QUOI et QUAND
     * - Notifie l'utilisateur par email que son compte a été supprimé
     *
     * @return array{identity: string, email: string}
     */
    public static function delete(User $user, User $deletedBy): array
    {
        $identity  = trim(($user->prenom ?? '') . ' ' . $user->name);
        $email     = $user->email;
        $auditData = $user->only(['id', 'name', 'prenom', 'email', 'role', 'commune_id', 'is_approved']);

        DB::transaction(function () use ($user, $deletedBy, $auditData, $identity, $email) {
            // 1. Invalider les sessions actives (driver sessions en base).
            if (config('session.driver') === 'database') {
                DB::table('sessions')->where('user_id', $user->id)->delete();
            }

            // 2. Supprimer le compte sans déclencher le log automatique du trait Auditable
            //    (on journalise nous-même : plus lisible et sans données sensibles comme le hash du mot de passe).
            User::withoutEvents(fn () => $user->delete());

            // 3. Journal d'audit : qui a supprimé qui, et quand (created_at est enregistré automatiquement).
            AuditService::log(
                'user_deleted',
                $user,
                oldValues: $auditData,
                description: "Suppression du compte utilisateur {$identity} ({$email}) par {$deletedBy->name}"
            );
        });

        // 4. Notification email à l'utilisateur supprimé (après la transaction).
        try {
            Notification::route('mail', $email)->notify(new AccountDeleted($identity, $deletedBy->name));
        } catch (\Exception $e) {
            Log::warning('Notification de suppression de compte échouée: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'email'   => $email,
            ]);
        }

        return ['identity' => $identity, 'email' => $email];
    }
}
