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
        Schema::table('infrastructures', function (Blueprint $table) {
            if (!Schema::hasColumn('infrastructures', 'commune_id')) {
                $table->unsignedBigInteger('commune_id')
                    ->nullable()
                    ->after('user_id');
                
                $table->foreign('commune_id')->references('id')->on('communes')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('infrastructures', function (Blueprint $table) {
            if (Schema::hasColumn('infrastructures', 'commune_id')) {
                $table->dropForeign(['commune_id']);
                $table->dropColumn('commune_id');
            }
        });
    }
};
