<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSecteurToMairieAgentDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mairie_agent_data', function (Blueprint $table) {
            $table->string('secteur')->nullable()->after('commune');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mairie_agent_data', function (Blueprint $table) {
            $table->dropColumn('secteur');
        });
    }
}
