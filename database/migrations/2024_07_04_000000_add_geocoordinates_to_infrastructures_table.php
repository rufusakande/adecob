<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGeocoordinatesToInfrastructuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('infrastructures', function (Blueprint $table) {
            $table->string('latitude')->nullable()->after('hameau');
            $table->string('longitude')->nullable()->after('latitude');
            $table->string('altitude')->nullable()->after('longitude');
            $table->string('precision')->nullable()->after('altitude');
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
            $table->dropColumn(['latitude', 'longitude', 'altitude', 'precision']);
        });
    }
}
