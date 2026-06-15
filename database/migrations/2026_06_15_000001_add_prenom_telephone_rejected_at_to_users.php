<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'prenom')) {
                $table->string('prenom')->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'telephone')) {
                $table->string('telephone', 32)->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('approved_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (['prenom', 'telephone', 'rejected_at'] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
