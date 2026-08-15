<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Convertit la colonne 'action' de la table audit_logs d'un ENUM restrictif
     * vers VARCHAR(50) afin d'accepter tous les types d'actions d'audit
     * (ex: 'user_deleted', 'commune_updated', ...) sans avoir à modifier l'ENUM
     * à chaque nouvelle fonctionnalité.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `audit_logs` MODIFY COLUMN `action` VARCHAR(50) NULL");
    }

    public function down(): void
    {
        // Restaure l'ENUM d'origine (union des valeurs définies dans les migrations de création).
        DB::statement("ALTER TABLE `audit_logs` MODIFY COLUMN `action` ENUM('login','logout','create','read','update','delete','export','import','password_reset','profile_update','approve_user','reject_user','assign_admin','revoke_admin') NULL");
    }
};
