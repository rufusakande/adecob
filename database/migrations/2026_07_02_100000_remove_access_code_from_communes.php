<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Supprime les colonnes access_code et access_code_plain de la table communes.
 *
 * La fonctionnalité "code d'accès par commune" est retirée de la plateforme :
 * chaque utilisateur appartient définitivement à la commune choisie lors de
 * son inscription. Aucun code intermédiaire n'est plus nécessaire.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('communes', function (Blueprint $table) {
            if (Schema::hasColumn('communes', 'access_code')) {
                $table->dropColumn('access_code');
            }
            if (Schema::hasColumn('communes', 'access_code_plain')) {
                $table->dropColumn('access_code_plain');
            }
        });
    }

    public function down(): void
    {
        Schema::table('communes', function (Blueprint $table) {
            $table->string('access_code')->nullable()->after('logo');
            $table->text('access_code_plain')->nullable()->after('access_code');
        });
    }
};
