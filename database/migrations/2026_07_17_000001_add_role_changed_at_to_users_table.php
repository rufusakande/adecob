<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute un timestamp role_changed_at sur la table users.
     *
     * Ce champ est mis à jour chaque fois qu'un administrateur change
     * le rôle d'un utilisateur. Le middleware CheckApprovalStatus s'en
     * sert pour invalider les sessions antérieures au changement de rôle,
     * forçant l'utilisateur à se reconnecter et à accéder à son nouvel espace.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('role_changed_at')->nullable()->after('rejected_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role_changed_at');
        });
    }
};
