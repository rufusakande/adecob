<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute le stockage structuré de la plage d'années d'exécution :
     *  - annee_debut / annee_fin : bornes de la plage (ex. 2027 → 2030)
     *  - repartition_annees     : JSON { année: budget } — répartition triennale par année
     *  - trimestres_annees      : JSON { année: { t1..t4 } } — répartition trimestrielle annuelle
     *
     * Les colonnes historiques (annee_execution, repartition_an1..3,
     * budget_annuel, trimestre_t1..4) restent en place pour la rétrocompatibilité.
     */
    public function up(): void
    {
        Schema::table('infrastructure_works', function (Blueprint $table) {
            if (!Schema::hasColumn('infrastructure_works', 'annee_debut')) {
                $table->unsignedSmallInteger('annee_debut')->nullable()->after('annee_execution');
            }
            if (!Schema::hasColumn('infrastructure_works', 'annee_fin')) {
                $table->unsignedSmallInteger('annee_fin')->nullable()->after('annee_debut');
            }
            if (!Schema::hasColumn('infrastructure_works', 'repartition_annees')) {
                $table->json('repartition_annees')->nullable()->after('repartition_an3');
            }
            if (!Schema::hasColumn('infrastructure_works', 'trimestres_annees')) {
                $table->json('trimestres_annees')->nullable()->after('trimestre_t4');
            }
        });
    }

    public function down(): void
    {
        Schema::table('infrastructure_works', function (Blueprint $table) {
            foreach (['annee_debut', 'annee_fin', 'repartition_annees', 'trimestres_annees'] as $col) {
                if (Schema::hasColumn('infrastructure_works', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
