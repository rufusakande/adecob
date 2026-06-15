<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNomEnqueteurToMairieAgentDataTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mairie_agent_data', function (Blueprint $table) {
            $table->string('nom_enqueteur')->after('infrastructure_id')->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mairie_agent_data', function (Blueprint $table) {
            $table->dropColumn('nom_enqueteur');
        });
    }
}
