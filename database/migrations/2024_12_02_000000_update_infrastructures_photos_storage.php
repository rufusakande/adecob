<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateInfrastructuresPhotosStorage extends Migration
{
    public function up()
    {
        // Cette migration est déjà couverte par la migration existante
        // 2024_12_01_000000_add_photos_json_to_infrastructures_table.php
        // Nous allons juste nous assurer que les colonnes photo1-4 existent
        Schema::table('infrastructures', function (Blueprint $table) {
            if (!Schema::hasColumn('infrastructures', 'photo1')) {
                $table->string('photo1')->nullable();
            }
            if (!Schema::hasColumn('infrastructures', 'photo2')) {
                $table->string('photo2')->nullable();
            }
            if (!Schema::hasColumn('infrastructures', 'photo3')) {
                $table->string('photo3')->nullable();
            }
            if (!Schema::hasColumn('infrastructures', 'photo4')) {
                $table->string('photo4')->nullable();
            }
        });
    }

    public function down()
    {
        // Pas de rollback nécessaire car les colonnes existent déjà
    }
}
