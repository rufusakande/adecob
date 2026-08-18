<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute les champs nécessaires aux fiches de planification
     * annuelle (budget, trimestres, statut) et triennale
     * (unité, quantité, coût unitaire, répartition, priorité)
     * conformément aux modèles MDGL fournis.
     */
    public function up(): void
    {
        Schema::table('infrastructure_works', function (Blueprint $table) {
            // --- Fiche de planification TRIENNALE ---
            $table->string('unite')->nullable()->after('annee_execution');            // Unité
            $table->decimal('quantite', 15, 2)->nullable()->after('unite');           // Quantité
            $table->decimal('cout_unitaire', 15, 2)->nullable()->after('quantite');   // Coût unitaire (FCFA)
            $table->decimal('repartition_an1', 15, 2)->nullable()->after('cout_unitaire'); // Répartition An-1
            $table->decimal('repartition_an2', 15, 2)->nullable()->after('repartition_an1'); // Répartition An-2
            $table->decimal('repartition_an3', 15, 2)->nullable()->after('repartition_an2'); // Répartition An-3
            $table->string('priorite')->nullable()->after('repartition_an3');        // Priorité

            // --- Fiche de planification ANNUELLE ---
            $table->decimal('budget_annuel', 15, 2)->nullable()->after('priorite');   // Budget annuel (FCFA)
            $table->decimal('trimestre_t1', 15, 2)->nullable()->after('budget_annuel'); // Trimestre T1
            $table->decimal('trimestre_t2', 15, 2)->nullable()->after('trimestre_t1');  // Trimestre T2
            $table->decimal('trimestre_t3', 15, 2)->nullable()->after('trimestre_t2');  // Trimestre T3
            $table->decimal('trimestre_t4', 15, 2)->nullable()->after('trimestre_t3');  // Trimestre T4
            $table->string('statut_execution')->nullable()->after('trimestre_t4');   // Statut d'exécution
        });
    }

    public function down(): void
    {
        Schema::table('infrastructure_works', function (Blueprint $table) {
            $table->dropColumn([
                'unite',
                'quantite',
                'cout_unitaire',
                'repartition_an1',
                'repartition_an2',
                'repartition_an3',
                'priorite',
                'budget_annuel',
                'trimestre_t1',
                'trimestre_t2',
                'trimestre_t3',
                'trimestre_t4',
                'statut_execution',
            ]);
        });
    }
};
