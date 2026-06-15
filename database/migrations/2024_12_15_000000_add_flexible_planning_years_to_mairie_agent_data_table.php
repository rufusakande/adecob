<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFlexiblePlanningYearsToMairieAgentDataTable extends Migration
{
    public function up()
    {
        Schema::table('mairie_agent_data', function (Blueprint $table) {
            $table->boolean('periode_2026')->default(false)->after('periode_2025');
            $table->boolean('periode_2027')->default(false)->after('periode_2026');
            $table->boolean('periode_2028')->default(false)->after('periode_2027');
            $table->boolean('periode_2029')->default(false)->after('periode_2028');
            $table->boolean('periode_2030')->default(false)->after('periode_2029');
            $table->string('maintenance_status')->nullable()->after('periode_2030');
            $table->date('maintenance_completed_date')->nullable()->after('maintenance_status');
            $table->text('maintenance_notes')->nullable()->after('maintenance_completed_date');
            $table->json('custom_planning_years')->nullable()->after('maintenance_notes');
        });
    }

    public function down()
    {
        Schema::table('mairie_agent_data', function (Blueprint $table) {
            $table->dropColumn([
                'periode_2026',
                'periode_2027',
                'periode_2028',
                'periode_2029',
                'periode_2030',
                'maintenance_status',
                'maintenance_completed_date',
                'maintenance_notes',
                'custom_planning_years'
            ]);
        });
    }
}
