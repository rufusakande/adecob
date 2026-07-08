<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('infrastructures', function (Blueprint $table) {
            if (!Schema::hasColumn('infrastructures', 'exported_at')) {
                $table->timestamp('exported_at')->nullable()->after('rejection_reason');
            }
            if (!Schema::hasColumn('infrastructures', 'export_count')) {
                $table->unsignedInteger('export_count')->default(0)->after('exported_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('infrastructures', function (Blueprint $table) {
            if (Schema::hasColumn('infrastructures', 'export_count')) {
                $table->dropColumn('export_count');
            }
            if (Schema::hasColumn('infrastructures', 'exported_at')) {
                $table->dropColumn('exported_at');
            }
        });
    }
};
