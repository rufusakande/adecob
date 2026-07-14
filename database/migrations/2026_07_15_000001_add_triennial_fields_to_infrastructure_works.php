<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('infrastructure_works', function (Blueprint $table) {
            if (!Schema::hasColumn('infrastructure_works', 'acteurs_concernes')) {
                $table->text('acteurs_concernes')->nullable()->after('provider_contact');
            }
            if (!Schema::hasColumn('infrastructure_works', 'sources_financement')) {
                $table->text('sources_financement')->nullable()->after('acteurs_concernes');
            }
            if (!Schema::hasColumn('infrastructure_works', 'annee_execution')) {
                $table->unsignedTinyInteger('annee_execution')->nullable()->after('sources_financement');
            }
        });
    }

    public function down(): void
    {
        Schema::table('infrastructure_works', function (Blueprint $table) {
            foreach (['acteurs_concernes', 'sources_financement', 'annee_execution'] as $col) {
                if (Schema::hasColumn('infrastructure_works', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
