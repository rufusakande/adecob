<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('infrastructure_works', function (Blueprint $table) {
            $table->string('annee_execution', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('infrastructure_works', function (Blueprint $table) {
            // Reverting back to tinyInteger
            // Note: Data loss may occur if the strings are not parseable as tinyInt.
            $table->unsignedTinyInteger('annee_execution')->nullable()->change();
        });
    }
};
