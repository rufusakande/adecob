<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * PR8 — Cohérence des données :
     *  - Ajoute commune_id (FK communes) et user_id (FK users) à mairie_agent_data.
     *  - Backfill via le nom de commune et nom_enqueteur (best effort).
     */
    public function up(): void
    {
        Schema::table('mairie_agent_data', function (Blueprint $table) {
            if (!Schema::hasColumn('mairie_agent_data', 'commune_id')) {
                $table->unsignedBigInteger('commune_id')->nullable()->after('commune');
                $table->index('commune_id');
            }
            if (!Schema::hasColumn('mairie_agent_data', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('commune_id');
                $table->index('user_id');
            }
        });

        // Backfill commune_id depuis le nom de commune
        if (Schema::hasTable('communes')) {
            DB::statement("
                UPDATE mairie_agent_data mad
                INNER JOIN communes c ON c.name = mad.commune
                SET mad.commune_id = c.id
                WHERE mad.commune_id IS NULL
            ");
        }

        // Backfill user_id depuis nom_enqueteur = users.name (best effort)
        if (Schema::hasColumn('mairie_agent_data', 'nom_enqueteur')) {
            DB::statement("
                UPDATE mairie_agent_data mad
                INNER JOIN users u ON u.name = mad.nom_enqueteur
                SET mad.user_id = u.id
                WHERE mad.user_id IS NULL
            ");
        }

        // FKs (best effort — on n'échoue pas si déjà présentes)
        try {
            Schema::table('mairie_agent_data', function (Blueprint $table) {
                $table->foreign('commune_id')->references('id')->on('communes')->nullOnDelete();
                $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            });
        } catch (\Throwable $e) {
            // Ignorer si les contraintes existent déjà
        }
    }

    public function down(): void
    {
        Schema::table('mairie_agent_data', function (Blueprint $table) {
            try { $table->dropForeign(['commune_id']); } catch (\Throwable $e) {}
            try { $table->dropForeign(['user_id']); } catch (\Throwable $e) {}
            if (Schema::hasColumn('mairie_agent_data', 'commune_id')) {
                $table->dropColumn('commune_id');
            }
            if (Schema::hasColumn('mairie_agent_data', 'user_id')) {
                $table->dropColumn('user_id');
            }
        });
    }
};
