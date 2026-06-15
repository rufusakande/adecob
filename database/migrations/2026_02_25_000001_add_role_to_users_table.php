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
        Schema::table('users', function (Blueprint $table) {
            // Ajouter les champs si ils n'existent pas
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['super_admin', 'commune_admin', 'agent', 'public_user'])
                    ->default('public_user')
                    ->after('email');
            }
            
            if (!Schema::hasColumn('users', 'commune_id')) {
                $table->unsignedBigInteger('commune_id')
                    ->nullable()
                    ->after('role');
            }
            
            if (!Schema::hasColumn('users', 'is_approved')) {
                $table->boolean('is_approved')
                    ->default(false)
                    ->after('commune_id');
            }

            // Modifier la colonne status si elle existe
            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
            if (Schema::hasColumn('users', 'commune_id')) {
                $table->dropColumn('commune_id');
            }
            if (Schema::hasColumn('users', 'is_approved')) {
                $table->dropColumn('is_approved');
            }
        });
    }
};
