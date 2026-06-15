<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInfrastructureIdToMairieAgentDataTable extends Migration
{
    public function up()
    {
        Schema::table('mairie_agent_data', function (Blueprint $table) {
            $table->unsignedBigInteger('infrastructure_id')->nullable()->after('id');

            $table->foreign('infrastructure_id')
                ->references('id')
                ->on('infrastructures')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('mairie_agent_data', function (Blueprint $table) {
            $table->dropForeign(['infrastructure_id']);
            $table->dropColumn('infrastructure_id');
        });
    }
}
